<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreParentMessageRequest;
use App\Jobs\SendBulletinWhatsAppJob;
use App\Jobs\SendParentMessageJob;
use App\Models\AcademicYear;
use App\Models\BulletinSend;
use App\Models\ClassGroup;
use App\Models\ParentMessage;
use App\Models\ParentMessageRecipient;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Services\Notification\NotificationGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class ParentCommunicationController extends Controller
{
    public function __construct(private NotificationGateway $gateway) {}

    // ── PAGE PRINCIPALE ───────────────────────────────────────────────────
    public function index(Request $request)
    {
        $activeYear = AcademicYear::active();
        $classes    = $activeYear
            ? ClassGroup::where('academic_year_id', $activeYear->id)
                ->with('level.section')->orderBy('name')->get()
            : collect();

        $recentMessages = ParentMessage::with('sender')
            ->orderByDesc('created_at')->take(10)->get();

        $stats = [
            'total_sent' => ParentMessage::sum('sent_count'),
            'this_month' => ParentMessage::whereMonth('created_at', now()->month)->count(),
            'simulation' => $this->gateway->isSimulationMode(),
        ];

        return view('communication.parents.index', compact(
            'classes', 'recentMessages', 'stats', 'activeYear'
        ));
    }

    // ── FORMULAIRE D'ENVOI ────────────────────────────────────────────────
    public function create(Request $request)
    {
        $activeYear = AcademicYear::active();
        $classes    = $activeYear
            ? ClassGroup::where('academic_year_id', $activeYear->id)
                ->with('level.section')->orderBy('name')->get()
            : collect();

        $students = $activeYear
            ? Student::whereHas('enrollments', fn($q) =>
                    $q->where('academic_year_id', $activeYear->id)
                      ->where('status', 'active')
                )
                ->with(['enrollments' => fn($q) =>
                    $q->where('academic_year_id', $activeYear->id)
                      ->where('status', 'active')
                      ->with('classGroup.level.section')
                ])
                ->orderBy('last_name')
                ->get()
            : collect();

        return view('communication.parents.create', compact('classes', 'students'));
    }

    // ── ENVOI DU MESSAGE ───────────────────────────────────────────────────
    public function store(StoreParentMessageRequest $request)
    {
        $recipients = $this->resolveRecipients($request);

        if ($recipients->isEmpty()) {
            return back()->with('error',
                'Aucun numéro de téléphone valide trouvé pour les destinataires sélectionnés.');
        }

        $parentMessage = ParentMessage::create([
            'sender_id'        => Auth::id(),
            'subject'          => $request->subject,
            'body'             => $request->body,
            'channel'          => $request->channel,
            'target_type'      => $request->target_type,
            'class_group_id'   => $request->class_group_id,
            'total_recipients' => $recipients->count(),
            'status'           => 'pending',
        ]);

        foreach ($recipients as $r) {
            ParentMessageRecipient::create([
                'parent_message_id' => $parentMessage->id,
                'student_id'        => $r['student_id'],
                'phone_number'      => $r['phone'],
                'recipient_type'    => $r['type'],
            ]);
        }

        // Dispatch en file d'attente (asynchrone)
        SendParentMessageJob::dispatch($parentMessage);

        return redirect()
            ->route('communication.parents.show', $parentMessage)
            ->with('success',
                "Message en cours d'envoi à {$recipients->count()} destinataire(s).");
    }

    // ── DÉTAIL D'UN ENVOI ─────────────────────────────────────────────────
    public function show(ParentMessage $parentMessage)
    {
        $parentMessage->load(['sender', 'classGroup', 'recipients.student']);
        return view('communication.parents.show', compact('parentMessage'));
    }

    // ── LISTE DES ENVOIS ───────────────────────────────────────────────────
    public function history(Request $request)
    {
        $messages = ParentMessage::with('sender', 'classGroup')
            ->orderByDesc('created_at')->paginate(20);

        return view('communication.parents.history', compact('messages'));
    }

    // ── ENVOI BULLETIN WHATSAPP (depuis page bulletin) ────────────────────
    public function sendBulletin(Request $request, StudentEnrollment $enrollment)
    {
        $request->validate([
            'phone'   => ['nullable', 'string'],
            'pdf_url' => ['required', 'url'],
        ]);

        $student = $enrollment->student;
        $available = array_filter([
            $student->father_phone ?? null,
            $student->mother_phone ?? null,
            $student->guardian_phone ?? null,
        ]);

        if ($request->filled('phone') && $request->input('phone') !== 'all') {
            $targets = [$request->input('phone')];
        } else {
            $targets = array_values(array_unique($available));
        }

        if (empty($targets)) {
            return back()->with('error', 'Aucun numéro parent/tuteur trouvé pour cet élève.');
        }

        $count = 0;
        foreach ($targets as $phone) {
            $bulletinSend = BulletinSend::create([
                'student_enrollment_id' => $enrollment->id,
                'sent_by'               => Auth::id(),
                'phone_number'          => $phone,
                'status'                => 'pending',
            ]);

            SendBulletinWhatsAppJob::dispatch(
                $bulletinSend,
                $request->input('pdf_url'),
                $enrollment->student->full_name
            );
            $count++;
        }

        return back()->with('success', "{$count} envoi(s) de bulletin programmés par WhatsApp.");
    }

    // ── ENVOI BULLETINS EN MASSE (classe entière) ─────────────────────────
    public function sendBulletinsBulk(Request $request)
    {
        $request->validate([
            'class_group_id' => ['required', 'exists:class_groups,id'],
            'type'           => ['required', 'in:sequentiel,trimestriel,annuel'],
            'sequence_id'    => ['nullable'],
            'trimester_id'   => ['nullable'],
            'student_ids'    => ['nullable', 'array'],
            'student_ids.*'  => ['integer', 'exists:students,id'],
        ]);

        $classGroup  = ClassGroup::find($request->class_group_id);
        $activeYear  = AcademicYear::active();

        $enrollments = StudentEnrollment::where([
            'class_group_id'   => $classGroup->id,
            'academic_year_id' => $activeYear->id,
            'status'           => 'active',
        ])
        ->when($request->filled('student_ids'), fn($query) => $query->whereIn('student_id', $request->student_ids))
        ->with('student')
        ->get();

        $count = 0;
        foreach ($enrollments as $enr) {
            $phones = array_filter([
                $enr->student->father_phone ?? null,
                $enr->student->mother_phone ?? null,
                $enr->student->guardian_phone ?? null,
            ]);

            if (empty($phones)) continue;

            $pdfUrl = URL::temporarySignedRoute(
                'bulletins.signed-pdf',
                now()->addMinutes(30),
                [
                    'enrollment'   => $enr,
                    'type'         => $request->type,
                    'sequence_id'  => $request->sequence_id,
                    'trimester_id' => $request->trimester_id,
                ]
            );

            foreach (array_values(array_unique($phones)) as $phone) {
                $bulletinSend = BulletinSend::create([
                    'student_enrollment_id' => $enr->id,
                    'sent_by'               => Auth::id(),
                    'phone_number'          => $phone,
                    'status'                => 'pending',
                ]);

                SendBulletinWhatsAppJob::dispatch($bulletinSend, $pdfUrl, $enr->student->full_name);
                $count++;
            }
        }

        return back()->with('success', "{$count} envoi(s) de bulletins programmés par WhatsApp.");
    }

    // ── RÉSOUDRE LES DESTINATAIRES ─────────────────────────────────────────
    private function resolveRecipients(StoreParentMessageRequest $request): \Illuminate\Support\Collection
    {
        $activeYear = AcademicYear::active();
        $recipients = collect();

        if ($request->target_type === 'all') {
            $students = Student::whereHas('enrollments', fn($q) =>
                $q->where('academic_year_id', $activeYear->id)->where('status', 'active')
            )->get();

        } elseif ($request->target_type === 'class') {
            $students = Student::whereHas('enrollments', fn($q) =>
                $q->where('class_group_id', $request->class_group_id)
                  ->where('status', 'active')
            )->get();

        } else { // selected
            $students = Student::whereIn('id', $request->student_ids)->get();
        }

        foreach ($students as $student) {
            if ($student->father_phone) {
                $recipients->push([
                    'student_id' => $student->id,
                    'phone'      => $student->father_phone,
                    'type'       => 'père',
                ]);
            }
            if ($student->mother_phone) {
                $recipients->push([
                    'student_id' => $student->id,
                    'phone'      => $student->mother_phone,
                    'type'       => 'mère',
                ]);
            }
            if ($student->guardian_phone) {
                $recipients->push([
                    'student_id' => $student->id,
                    'phone'      => $student->guardian_phone,
                    'type'       => 'tuteur',
                ]);
            }
        }

        return $recipients->unique('phone');
    }
}