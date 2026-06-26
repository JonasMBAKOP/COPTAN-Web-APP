<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDisciplineRequest;
use App\Models\DisciplineRecord;
use App\Models\AcademicYear;
use App\Models\ClassGroup;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class DisciplinesController extends Controller
{
    // ─── Index ───────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $activeYear = AcademicYear::where('is_active', true)->firstOrFail();

        $query = DisciplineRecord::with(['studentEnrollment.student', 'classe.level', 'reporter'])
            ->where('academic_year_id', $activeYear->id)
            ->orderByDesc('incident_date');

        // Filtres
        if ($request->filled('class_id')) {
            $query->where('class_group_id', $request->class_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('sanction_type')) {
            $query->where('sanction_type', $request->sanction_type);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('studentEnrollment.student', function ($q) use ($search) {
                $q->where('last_name', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('matricule', 'like', "%{$search}%");
            });
        }

        $records  = $query->paginate(20)->withQueryString();
        $classes  = ClassGroup::with('level.section')
                        ->where('academic_year_id', $activeYear->id)
                        ->orderBy('name')->get();

        // Statistiques rapides
        $statsQuery = DisciplineRecord::where('academic_year_id', $activeYear->id);
        $stats = [
            'total'               => (clone $statsQuery)->count(),
            'ouverts'             => (clone $statsQuery)->where('status', 'ouvert')->count(),
            'exclusions'          => (clone $statsQuery)->where('sanction_type', 'exclusion_definitive')->count(),
            'renvois'             => (clone $statsQuery)->where('sanction_type', 'renvoi_temporaire')->count(),
        ];

        return view('discipline.index', compact('records', 'classes', 'stats', 'activeYear'));
    }

    // ─── Create ──────────────────────────────────────────────────────────────

    public function create(Request $request)
    {
        $activeYear  = AcademicYear::where('is_active', true)->firstOrFail();
        $classes     = ClassGroup::with('level.section')
                           ->where('academic_year_id', $activeYear->id)
                           ->orderBy('name')->get();

        $enrollments = StudentEnrollment::where('academic_year_id', $activeYear->id)
            ->with('student', 'classGroup.level.section')
            ->get()
            ->sortBy(fn ($enrollment) => $enrollment->student->last_name);

        // Pré-sélection élève si passé en query string
        $selectedEnrollment = null;
        if ($request->filled('student_enrollment_id')) {
            $selectedEnrollment = $enrollments->firstWhere('id', $request->student_enrollment_id);
        }

        $incidentTypes  = DisciplineRecord::$incidentTypes;
        $sanctionTypes  = DisciplineRecord::$sanctionTypes;
        $staffList      = Staff::orderBy('last_name')->get();

        return view('discipline.create', compact(
            'activeYear', 'classes', 'enrollments', 'selectedEnrollment',
            'incidentTypes', 'sanctionTypes', 'staffList'
        ));
    }

    // ─── Store ───────────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_enrollment_id' => 'required|exists:student_enrollments,id',
            'incident_date'         => 'required|date',
            'incident_type'         => 'required|in:' . implode(',', array_keys(DisciplineRecord::$incidentTypes)),
            'description'           => 'required|string|min:10',
            'sanction_type'         => 'required|in:' . implode(',', array_keys(DisciplineRecord::$sanctionTypes)),
            'sanction_days'         => 'nullable|integer|min:1|max:30',
            'sanction_start'        => 'nullable|date',
            'sanction_end'          => 'nullable|date|after_or_equal:sanction_start',
            'notes_internes'        => 'nullable|string',
            'convocation_parent'    => 'boolean',
            'convocation_date'      => 'nullable|date',
        ]);

        $activeYear = AcademicYear::where('is_active', true)->firstOrFail();

        $enrollment = StudentEnrollment::findOrFail($validated['student_enrollment_id']);

        $record = DisciplineRecord::create([
            ...$validated,
            'academic_year_id'   => $activeYear->id,
            'class_group_id'     => $enrollment->class_group_id,
            'reported_by'        => Auth::user()->staff->id ?? Staff::first()->id,
            'convocation_parent' => $request->boolean('convocation_parent'),
            'status'             => 'ouvert',
        ]);

        return redirect()
            ->route('discipline.show', $record)
            ->with('success', 'Dossier disciplinaire créé avec succès.');
    }

    // ─── Show ────────────────────────────────────────────────────────────────

    public function show(DisciplineRecord $discipline)
    {
        $discipline->load(['studentEnrollment.student', 'studentEnrollment.classGroup.level.section', 'classe.level.section', 'reporter', 'schoolYear']);

        // Historique complet du même élève
        $history = DisciplineRecord::with(['schoolYear', 'reporter'])
            ->where('student_enrollment_id', $discipline->student_enrollment_id)
            ->orderByDesc('incident_date')
            ->get();

        return view('discipline.show', compact('discipline', 'history'));
    }

    // ─── Edit ────────────────────────────────────────────────────────────────

    public function edit(DisciplineRecord $discipline)
    {
        $activeYear    = AcademicYear::where('is_active', true)->firstOrFail();
        $classes       = ClassGroup::with('level.section')
                             ->where('school_year_id', $activeYear->id)
                             ->orderBy('name')->get();
        $incidentTypes = DisciplineRecord::$incidentTypes;
        $sanctionTypes = DisciplineRecord::$sanctionTypes;
        $staffList     = Staff::orderBy('last_name')->get();
        $statusLabels  = DisciplineRecord::$statusLabels;

        return view('discipline.edit', compact(
            'discipline', 'classes', 'incidentTypes',
            'sanctionTypes', 'staffList', 'statusLabels'
        ));
    }

    // ─── Update ──────────────────────────────────────────────────────────────

    public function update(Request $request, DisciplineRecord $discipline)
    {
        $validated = $request->validate([
            'incident_date'       => 'required|date',
            'incident_type'       => 'required|in:' . implode(',', array_keys(DisciplineRecord::$incidentTypes)),
            'description'         => 'required|string|min:10',
            'sanction_type'       => 'required|in:' . implode(',', array_keys(DisciplineRecord::$sanctionTypes)),
            'sanction_days'       => 'nullable|integer|min:1|max:30',
            'sanction_start'      => 'nullable|date',
            'sanction_end'        => 'nullable|date|after_or_equal:sanction_start',
            'status'              => 'required|in:ouvert,resolu,classe',
            'notes_internes'      => 'nullable|string',
            'convocation_parent'  => 'boolean',
            'convocation_date'    => 'nullable|date',
        ]);

        $discipline->update([
            ...$validated,
            'convocation_parent' => $request->boolean('convocation_parent'),
        ]);

        return redirect()
            ->route('discipline.show', $discipline)
            ->with('success', 'Dossier mis à jour avec succès.');
    }

    // ─── Destroy ─────────────────────────────────────────────────────────────

    public function destroy(DisciplineRecord $discipline)
    {
        $discipline->delete();

        return redirect()
            ->route('discipline.index')
            ->with('success', 'Dossier supprimé.');
    }

    // ─── PDF Convocation ─────────────────────────────────────────────────────

    public function convocation(DisciplineRecord $discipline)
    {
        $discipline->load(['studentEnrollment.student', 'classe.level.section', 'reporter', 'schoolYear']);

        $schoolSettings = \App\Models\SchoolSetting::first();

        $pdf = Pdf::loadView('discipline.pdf.convocation', compact('discipline', 'schoolSettings'))
            ->setPaper('a4', 'portrait');

        $filename = 'convocation_' . $discipline->student->matricule . '_' . $discipline->incident_date->format('Ymd') . '.pdf';

        return $pdf->stream($filename);
    }

    // ─── AJAX : Élèves par classe ─────────────────────────────────────────────

    public function studentsByClass(Request $request)
    {
        $students = Student::whereHas('enrollments', function ($q) use ($request) {
            $q->where('class_group_id', $request->class_id)
              ->where('academic_year_id', $request->academic_year_id ?? AcademicYear::where('is_active', true)->value('id'));
        })->orderBy('last_name')->get(['id', 'last_name', 'first_name', 'matricule']);

        return response()->json($students);
    }
}