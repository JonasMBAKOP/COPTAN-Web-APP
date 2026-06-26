<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDisciplineRequest;
use App\Models\AcademicYear;
use App\Models\AuditLog;
use App\Models\ClassGroup;
use App\Models\DisciplineIncident;
use App\Models\Section;
use App\Models\StudentEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class DisciplineController extends Controller
{
    // // ── LISTE DES INCIDENTS ───────────────────────────────────────────────
    // public function index(Request $request)
    // {
    //     $activeYear = AcademicYear::active();

    //     $query = DisciplineIncident::with([
    //         'studentEnrollment.student',
    //         'studentEnrollment.classGroup.level.section',
    //         'reportedBy',
    //         'decidedBy',
    //     ])->orderByDesc('incident_date');

    //     // Filtres
    //     if ($request->filled('type')) {
    //         $query->where('incident_type', $request->type);
    //     }
    //     if ($request->filled('status')) {
    //         $query->where('status', $request->status);
    //     }
    //     if ($request->filled('sanction')) {
    //         $query->where('sanction_type', $request->sanction);
    //     }
    //     if ($request->filled('search')) {
    //         $search = $request->search;
    //         $query->whereHas('studentEnrollment.student', fn($q) =>
    //             $q->where('last_name', 'like', "%{$search}%")
    //               ->orWhere('first_name', 'like', "%{$search}%")
    //               ->orWhere('matricule', 'like', "%{$search}%")
    //         );
    //     }
    //     if ($activeYear) {
    //         $query->whereHas('studentEnrollment', fn($q) =>
    //             $q->where('academic_year_id', $activeYear->id)
    //         );
    //     }

    //     $incidents = $query->paginate(20)->withQueryString();

    //     // Stats rapides
    //     $stats = [
    //         'total'      => DisciplineIncident::when($activeYear, fn($q) =>
    //                             $q->whereHas('studentEnrollment', fn($q2) =>
    //                                 $q2->where('academic_year_id', $activeYear->id)
    //                             )
    //                         )->count(),
    //         'pending'    => DisciplineIncident::when($activeYear, fn($q) =>
    //                             $q->whereHas('studentEnrollment', fn($q2) =>
    //                                 $q2->where('academic_year_id', $activeYear->id)
    //                             )
    //                         )->where('status', 'pending')->count(),
    //         'resolved'   => DisciplineIncident::when($activeYear, fn($q) =>
    //                             $q->whereHas('studentEnrollment', fn($q2) =>
    //                                 $q2->where('academic_year_id', $activeYear->id)
    //                             )
    //                         )->where('status', 'resolved')->count(),
    //         'exclusions' => DisciplineIncident::when($activeYear, fn($q) =>
    //                             $q->whereHas('studentEnrollment', fn($q2) =>
    //                                 $q2->where('academic_year_id', $activeYear->id)
    //                             )
    //                         )->whereIn('sanction_type', [
    //                             'temporary_suspension', 'definitive_exclusion'
    //                         ])->count(),
    //     ];

    //     // Élèves récidivistes
    //     $recidivists = StudentEnrollment::where('status', 'active')
    //         ->when($activeYear, fn($q) =>
    //             $q->where('academic_year_id', $activeYear->id)
    //         )
    //         ->withCount('disciplineIncidents')
    //         ->having('discipline_incidents_count', '>', 1)
    //         ->orderByDesc('discipline_incidents_count')
    //         ->with('student', 'classGroup.level.section')
    //         ->take(8)
    //         ->get();

    //     $incidentTypes  = DisciplineIncident::TYPES ?? $this->incidentTypes();
    //     $sanctionTypes  = DisciplineIncident::SANCTIONS ?? $this->sanctionTypes();

    //     return view('discipline.index', compact(
    //         'incidents', 'stats', 'recidivists',
    //         'incidentTypes', 'sanctionTypes', 'activeYear'
    //     ));
    // }

    // ── LISTE ─────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $activeYear = AcademicYear::active();
        $sections   = Section::orderBy('id')->get();

        $query = DisciplineIncident::with([
            'studentEnrollment.student',
            'studentEnrollment.classGroup.level.section',
            'decidedBy', 'reportedBy',
        ]);

        if ($request->filled('class_id')) {
            $query->whereHas('studentEnrollment',
                fn($q) => $q->where('class_group_id', $request->class_id)
            );
        }

        if ($request->filled('sanction')) {
            $query->where('sanction_type', $request->sanction);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('studentEnrollment.student', fn($q) =>
                $q->where('first_name', 'like', "%{$s}%")
                  ->Where('last_name',  'like', "%{$s}%")
                  ->orWhere('matricule', 'like', "%{$s}%")
            );
        }

        $incidents = $query
            ->orderByDesc('incident_date')
            ->orderByDesc('incident_time')
            ->orderByDesc('created_at')
            ->paginate(20);

        $classes = $activeYear
            ? ClassGroup::where('academic_year_id', $activeYear->id)
                ->with('level.section')->orderBy('name')->get()
            : collect();

        $stats = [
            'total'       => DisciplineIncident::count(),
            'pending'     => DisciplineIncident::where('status', 'open')->count(),
            'resolved'    => DisciplineIncident::where('status', 'closed')->count(),
            'suspensions' => DisciplineIncident::where('sanction_type', 'temporary_suspension')->count(),
            'exclusions'  => DisciplineIncident::where('sanction_type', 'definitive_exclusion')->count(),
        ];

        return view('discipline.index', compact(
            'incidents', 'classes', 'stats', 'activeYear'
        ));
    }

    // // ── FORMULAIRE DE CRÉATION ────────────────────────────────────────────
    // public function create(Request $request)
    // {
    //     $activeYear  = AcademicYear::active();
    //     $enrollments = collect();

    //     if ($activeYear) {
    //         $enrollments = StudentEnrollment::where([
    //             'academic_year_id' => $activeYear->id,
    //             'status'           => 'active',
    //         ])->with([
    //             'student',
    //             'classGroup.level.section',
    //         ])->get()->sortBy('student.last_name');
    //     }

    //     // Pré-sélection élève depuis l'URL ?enrollment_id=X
    //     $selectedEnrollment = null;
    //     if ($request->filled('enrollment_id')) {
    //         $selectedEnrollment = StudentEnrollment::with(
    //             'student', 'classGroup.level.section'
    //         )->find($request->enrollment_id);
    //     }

    //     $incidentTypes = $this->incidentTypes();
    //     $sanctionTypes = $this->sanctionTypes();

    //     return view('discipline.create', compact(
    //         'enrollments', 'selectedEnrollment',
    //         'incidentTypes', 'sanctionTypes'
    //     ));
    // }

    // ── FORMULAIRE DE CREATION ────────────────────────────────────────────────────────
    public function create(Request $request)
    {
        $activeYear = AcademicYear::active();
        $sections   = Section::orderBy('id')->get();
        $classes    = $activeYear
            ? ClassGroup::where('academic_year_id', $activeYear->id)
                ->with('level.section')->orderBy('name')->get()
            : collect();

        $preEnrollmentId = $request->input('enrollment_id');
        $preEnrollment   = $preEnrollmentId
            ? StudentEnrollment::with('student', 'classGroup.level.section')->find($preEnrollmentId)
            : null;

        $preSectionId = $preEnrollment?->classGroup?->level?->section_id;
        $classesJson  = $this->classesJsonForForms($classes);

        return view('discipline.create', compact(
            'classes', 'sections', 'activeYear', 'preEnrollment', 'preSectionId', 'classesJson'
        ));
    }

    private function classesJsonForForms($classes): array
    {
        return $classes->map(fn ($c) => [
            'id'         => $c->id,
            'name'       => $c->full_name,
            'section_id' => $c->level?->section_id,
        ])->values()->all();
    }

    // // ── ENREGISTREMENT ────────────────────────────────────────────────────
    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'student_enrollment_id'  => 'required|exists:student_enrollments,id',
    //         'incident_date'          => 'required|date',
    //         'incident_time'          => 'nullable|date_format:H:i',
    //         'location'               => 'nullable|string|max:200',
    //         'incident_type'          => 'required|string|max:100',
    //         'description'            => 'required|string|max:2000',
    //         'sanction_type'          => 'nullable|string|max:100',
    //         'sanction_duration_days' => 'nullable|integer|min:1|max:365',
    //         'parent_convoked'        => 'boolean',
    //         'convocation_date'       => 'nullable|date',
    //         'status'                 => 'required|in:pending,in_progress,resolved',
    //     ]);

    //     $validated['reported_by']    = Auth::id();
    //     $validated['parent_convoked'] = $request->boolean('parent_convoked');

    //     $incident = DisciplineIncident::create($validated);
    //     AuditLog::log('discipline_incident_created', $incident);

    //     return redirect()
    //         ->route('discipline.show', $incident->id)
    //         ->with('success', 'Incident disciplinaire enregistré.');
    // }

    // ── ENREGISTREMENT ────────────────────────────────────────────────────
    public function store(StoreDisciplineRequest $request)
    {
        $incident = DisciplineIncident::create([
            'student_enrollment_id'  => $request->student_enrollment_id,
            'incident_date'          => $request->incident_date,
            'incident_time'          => $request->incident_time,
            'location'               => $request->location ?: null,
            'incident_type'          => $request->incident_type,
            'description'            => $request->description,
            'sanction_type'          => $request->sanction_type ?: 'observation',
            'sanction_duration_days' => $request->sanction_duration_days,
            'parent_convoked'        => $request->boolean('parent_convoked'),
            'convocation_date'       => $request->convocation_date,
            'status'                 => 'open',
            'reported_by'            => Auth::id(),
            'decided_by'             => Auth::id(),
        ]);

        AuditLog::log('discipline_created', $incident);

        return redirect()
            ->route('discipline.show', $incident)
            ->with('success', 'Incident disciplinaire enregistré.');
    }

    // // ── DÉTAIL D'UN INCIDENT ──────────────────────────────────────────────
    // public function show(DisciplineIncident $incident)
    // {
    //     $incident->load([
    //         'studentEnrollment.student',
    //         'studentEnrollment.classGroup.level.section',
    //         'studentEnrollment.classGroup.academicYear',
    //         'reportedBy',
    //         'decidedBy',
    //     ]);

    //     // Historique des incidents de cet élève
    //     $history = DisciplineIncident::where(
    //         'student_enrollment_id', $incident->student_enrollment_id
    //     )->where('id', '!=', $incident->id)
    //      ->orderByDesc('incident_date')
    //      ->with('reportedBy')
    //      ->take(5)
    //      ->get();

    //     $incidentTypes = $this->incidentTypes();
    //     $sanctionTypes = $this->sanctionTypes();

    //     return view('discipline.show', compact(
    //         'incident', 'history', 'incidentTypes', 'sanctionTypes'
    //     ));
    // }

    // ── DÉTAIL ────────────────────────────────────────────────────────────
    public function show(DisciplineIncident $disciplineIncident)
    {
        $disciplineIncident->load([
            'studentEnrollment.student',
            'studentEnrollment.classGroup.level.section',
            'studentEnrollment.academicYear',
            'decidedBy', 'reportedBy',
        ]);

        // Autres incidents du même élève
        $otherIncidents = DisciplineIncident::where(
            'student_enrollment_id',
            $disciplineIncident->student_enrollment_id
        )->where('id', '!=', $disciplineIncident->id)
         ->orderByDesc('incident_date')
         ->orderByDesc('incident_time')
         ->orderByDesc('created_at')
         ->take(5)->get();

        return view('discipline.show', compact(
            'disciplineIncident', 'otherIncidents'
        ));
    }

    // // ── FORMULAIRE D'ÉDITION ──────────────────────────────────────────────
    // public function edit(DisciplineIncident $incident)
    // {
    //     $incident->load([
    //         'studentEnrollment.student',
    //         'studentEnrollment.classGroup.level.section',
    //     ]);

    //     $incidentTypes = $this->incidentTypes();
    //     $sanctionTypes = $this->sanctionTypes();

    //     return view('discipline.edit', compact(
    //         'incident', 'incidentTypes', 'sanctionTypes'
    //     ));
    // }

    // // ── MISE À JOUR ───────────────────────────────────────────────────────
    // public function update(Request $request, DisciplineIncident $incident)
    // {
    //     $validated = $request->validate([
    //         'incident_date'          => 'required|date',
    //         'incident_time'          => 'nullable|date_format:H:i',
    //         'location'               => 'nullable|string|max:200',
    //         'incident_type'          => 'required|string|max:100',
    //         'description'            => 'required|string|max:2000',
    //         'sanction_type'          => 'nullable|string|max:100',
    //         'sanction_duration_days' => 'nullable|integer|min:1|max:365',
    //         'parent_convoked'        => 'boolean',
    //         'convocation_date'       => 'nullable|date',
    //         'status'                 => 'required|in:pending,in_progress,resolved',
    //     ]);

    //     $validated['decided_by']     = Auth::id();
    //     $validated['parent_convoked'] = $request->boolean('parent_convoked');

    //     $incident->update($validated);
    //     AuditLog::log('discipline_incident_updated', $incident);

    //     return redirect()
    //         ->route('discipline.show', $incident->id)
    //         ->with('success', 'Incident mis à jour.');
    // }

    // ── MODIFIER STATUT ───────────────────────────────────────────────────
    public function updateStatus(Request $request, DisciplineIncident $disciplineIncident)
    {
        $request->validate([
            'status' => ['required', Rule::in(array_keys(DisciplineIncident::STATUSES))],
        ]);

        $disciplineIncident->update(['status' => $request->status]);

        return back()->with('success', 'Statut mis à jour.');
    }

    // // ── SUPPRESSION ───────────────────────────────────────────────────────
    // public function destroy(DisciplineIncident $incident)
    // {
    //     AuditLog::log('discipline_incident_deleted', $incident);
    //     $incident->delete();

    //     return redirect()
    //         ->route('discipline.index')
    //         ->with('success', 'Incident supprimé.');
    // }

    // ── SUPPRESSION ───────────────────────────────────────────────────────
    public function destroy(DisciplineIncident $disciplineIncident)
    {
        $disciplineIncident->delete();
        return redirect()->route('discipline.index')
            ->with('success', 'Incident supprimé.');
    }

    // ── AJAX élèves d'une classe ──────────────────────────────────────────
    public function apiStudents(Request $request)
    {
        $classId    = (int)$request->class_id;
        $activeYear = AcademicYear::active();

        $enrollments = StudentEnrollment::where([
            'class_group_id'   => $classId,
            'academic_year_id' => $activeYear?->id,
            'status'           => 'active',
        ])->with('student')
          ->get()->sortBy('student.last_name')
          ->map(fn($e) => [
              'id'        => $e->id,
              'full_name' => $e->student->full_name,
          ])->values();

        return response()->json(['enrollments' => $enrollments]);
    }

    // // ── LISTES DE RÉFÉRENCE ───────────────────────────────────────────────
    // private function incidentTypes(): array
    // {
    //     return [
    //         'retard'            => 'Retard',
    //         'absence_injustifiee'=> 'Absence injustifiée',
    //         'tenue_incorrecte'  => 'Tenue incorrecte',
    //         'insolence'         => 'Insolence / Irrespect',
    //         'violence_verbale'  => 'Violence verbale',
    //         'violence_physique' => 'Violence physique',
    //         'fraude'            => 'Fraude / Triche',
    //         'vandalisme'        => 'Vandalisme',
    //         'autre'             => 'Autre',
    //     ];
    // }

    // private function sanctionTypes(): array
    // {
    //     return [
    //         'observation'          => 'Observation',
    //         'warning'              => 'Avertissement',
    //         'detention'            => 'Retenue',
    //         'temporary_suspension' => 'Renvoi temporaire',
    //         'definitive_exclusion' => 'Exclusion définitive',
    //     ];
    // }
}
