<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDisciplineRequest;
use App\Models\DisciplineRecord;
use App\Models\AcademicYear;
use App\Models\ClassGroup;
use App\Models\Student;
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

        $query = DisciplineRecord::with(['student', 'classe.level', 'reporter'])
            ->where('school_year_id', $activeYear->id)
            ->orderByDesc('incident_date');

        // Filtres
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('sanction_type')) {
            $query->where('sanction_type', $request->sanction_type);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', function ($q) use ($search) {
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
        $statsQuery = DisciplineRecord::where('school_year_id', $activeYear->id);
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
                           ->where('school_year_id', $activeYear->id)
                           ->orderBy('name')->get();

        // Pré-sélection élève si passé en query string
        $selectedStudent = null;
        if ($request->filled('student_id')) {
            $selectedStudent = Student::find($request->student_id);
        }

        $incidentTypes  = DisciplineRecord::$incidentTypes;
        $sanctionTypes  = DisciplineRecord::$sanctionTypes;
        $staffList      = Staff::orderBy('last_name')->get();

        return view('discipline.create', compact(
            'activeYear', 'classes', 'selectedStudent',
            'incidentTypes', 'sanctionTypes', 'staffList'
        ));
    }

    // ─── Store ───────────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id'          => 'required|exists:students,id',
            'class_id'            => 'required|exists:school_classes,id',
            'incident_date'       => 'required|date',
            'incident_type'       => 'required|in:' . implode(',', array_keys(DisciplineRecord::$incidentTypes)),
            'description'         => 'required|string|min:10',
            'sanction_type'       => 'required|in:' . implode(',', array_keys(DisciplineRecord::$sanctionTypes)),
            'sanction_days'       => 'nullable|integer|min:1|max:30',
            'sanction_start'      => 'nullable|date',
            'sanction_end'        => 'nullable|date|after_or_equal:sanction_start',
            'notes_internes'      => 'nullable|string',
            'convocation_parent'  => 'boolean',
            'convocation_date'    => 'nullable|date',
        ]);

        $activeYear = AcademicYear::where('is_active', true)->firstOrFail();

        $record = DisciplineRecord::create([
            ...$validated,
            'school_year_id'     => $activeYear->id,
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
        $discipline->load(['student.enrollments.classe.level.section', 'classe.level.section', 'reporter', 'schoolYear']);

        // Historique complet de l'élève
        $history = DisciplineRecord::with(['schoolYear', 'reporter'])
            ->where('student_id', $discipline->student_id)
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
        $discipline->load(['student', 'classe.level.section', 'reporter', 'schoolYear']);

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
            $q->where('class_id', $request->class_id)
              ->where('school_year_id', $request->school_year_id ?? AcademicYear::where('is_active', true)->value('id'));
        })->orderBy('last_name')->get(['id', 'last_name', 'first_name', 'matricule']);

        return response()->json($students);
    }
}