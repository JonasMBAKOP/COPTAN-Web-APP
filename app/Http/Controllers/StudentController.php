<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEnrollmentRequest;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Models\AcademicYear;
use App\Models\AuditLog;
use App\Models\ClassGroup;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Services\EnrollmentService;
use App\Services\StudentDocumentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{
    public function __construct(
        private readonly EnrollmentService $enrollments,
        private readonly StudentDocumentService $documents
    ) {}
    // ── LISTE ─────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $activeYear = AcademicYear::active();
        $selectedYearId = $request->input('year_id', $activeYear?->id);
        $selectedYear   = $selectedYearId
            ? AcademicYear::find($selectedYearId)
            : null;

        $query = Student::query();
        $renewalFilter = $request->input('renewal') === 'pending';

        // Filtrer par classe / année
        if ($selectedYear) {
            if ($renewalFilter && $selectedYear->is_active) {
                $query->whereDoesntHave('enrollments', fn ($q) =>
                    $q->where('academic_year_id', $selectedYear->id)
                      ->where('status', StudentEnrollment::STATUS_ACTIVE)
                )->whereHas('enrollments', fn ($q) =>
                    $q->whereHas('academicYear', fn ($y) =>
                        $y->where('start_date', '<', $selectedYear->start_date)
                    )
                );
            } else {
                $query->whereHas('enrollments', fn ($q) =>
                    $q->where('academic_year_id', $selectedYear->id)
                      ->where('status', StudentEnrollment::STATUS_ACTIVE)
                );
            }
        }

        // Filtrer par classe spécifique
        if ($request->filled('class_id')) {
            $query->whereHas('enrollments', fn($q) =>
                $q->where('class_group_id', $request->class_id)
            );
        }

        // Filtrer par section
        if ($request->filled('section_id')) {
            $query->whereHas('enrollments.classGroup.level', fn($q) =>
                $q->where('section_id', $request->section_id)
            );
        }

        // Recherche
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) =>
                $q->where('first_name',  'like', "%{$s}%")
                  ->orWhere('last_name',  'like', "%{$s}%")
                  ->orWhere('matricule',  'like', "%{$s}%")
            );
        }

        $students = $query
            ->with([
                'enrollments' => function ($q) use ($selectedYear, $renewalFilter) {
                    $q->with('classGroup.level.section', 'academicYear');

                    if ($renewalFilter && $selectedYear) {
                        $q->whereHas('academicYear', fn ($y) =>
                            $y->where('start_date', '<', $selectedYear->start_date)
                        );
                    } elseif ($selectedYear) {
                        $q->where('academic_year_id', $selectedYear->id)
                          ->where('status', StudentEnrollment::STATUS_ACTIVE);
                    }
                },
            ])
            ->orderBy('last_name')->orderBy('first_name')
            ->paginate(20)->withQueryString();

        $years    = AcademicYear::orderByDesc('start_date')->get();
        $sections = Section::orderBy('id')->get();
        $classes  = $selectedYear
            ? ClassGroup::where('academic_year_id', $selectedYear->id)
                ->with('level.section')->orderBy('name')->get()
            : collect();

        // Stats
        $stats = [
            'total'     => $selectedYear
                ? StudentEnrollment::where('academic_year_id', $selectedYear->id)
                    ->where('status', 'active')->count()
                : Student::count(),
            'boys'      => $selectedYear
                ? StudentEnrollment::where('academic_year_id', $selectedYear->id)
                    ->where('status', 'active')
                    ->whereHas('student', fn($q) => $q->where('gender', 'M'))
                    ->count()
                : 0,
            'girls'     => $selectedYear
                ? StudentEnrollment::where('academic_year_id', $selectedYear->id)
                    ->where('status', 'active')
                    ->whereHas('student', fn($q) => $q->where('gender', 'F'))
                    ->count()
                : 0,
            'repeating' => $selectedYear
                ? StudentEnrollment::where('academic_year_id', $selectedYear->id)
                    ->where('status', 'active')
                    ->where('is_repeating', true)->count()
                : 0,
        ];

        // Année éditable = active uniquement
        $isYearEditable = $selectedYear && $selectedYear->is_active;
        $pendingRenewal = $activeYear
            ? $this->enrollments->pendingRenewalCount($activeYear)
            : 0;

        $listPrintParams = $this->documents->listPrintParamsFromIndexFilters(
            $selectedYear,
            $request->input('class_id'),
            $request->input('section_id'),
            $renewalFilter
        );

        return view('students.index', compact(
            'students', 'years', 'sections', 'classes',
            'selectedYear', 'stats', 'activeYear', 'isYearEditable',
            'renewalFilter', 'pendingRenewal', 'listPrintParams'
        ));
    }

    // ── FORMULAIRE CRÉATION ───────────────────────────────────────────────
    public function create(Request $request)
    {
        $activeYear = AcademicYear::active();
        $suggestedMatricule = Student::generateMatricule();

        // Pré-sélection classe si vient de la page classe
        $preSelectedClass = $request->filled('class_id')
            ? ClassGroup::with('level.section', 'academicYear')
                ->find($request->class_id)
            : null;

        // Sections avec leurs cycles et classes
        $sectionsJson = Section::with(['levels' => fn($q) =>
            $q->orderBy('order_index')
        ])->orderBy('id')->get()->map(function($s) {
            return [
                'id'     => $s->id,
                'name'   => $s->name,
                'levels' => $s->levels->map(fn($l) => [
                    'id'    => $l->id,
                    'name'  => $l->name,
                    'cycle' => $l->cycle,
                ])->values()->toArray(),
            ];
        })->values()->toArray();

        // Classes de l'année active avec stats et cycle
        $classesJson = [];
        if ($activeYear) {
            $classes = ClassGroup::where('academic_year_id', $activeYear->id)
                ->with(['level.section'])
                ->get();

            foreach ($classes as $c) {
                $cycle = $c->level?->cycle
                    ?? (($c->level?->order_index ?? 0) <= 4 ? '1er' : '2nd');

                $classesJson[] = [
                    'id'            => $c->id,
                    'full_name'     => $c->full_name,
                    'level_id'      => $c->level_id,
                    'level_name'    => $c->level?->name,
                    'cycle'         => $cycle,
                    'section_id'    => $c->level?->section_id,
                    'section_code'  => $c->level?->section?->code,
                    'section_name'  => $c->level?->section?->name,
                    'max_students'  => $c->max_students,
                    'students_count'=> $c->studentEnrollments()
                                        ->where('status', 'active')
                                        ->count(),
                ];
            }
        }

        // Toutes les classes pour la liste "classe précédente" (de l'année passée)
        $allClasses = ClassGroup::with('level.section', 'academicYear')
            ->whereHas('academicYear', fn($q) =>
                $q->where('id', '!=', $activeYear?->id)
            )
            ->orderBy('name')->get();

        return view('students.create', compact(
            'activeYear', 'suggestedMatricule', 'preSelectedClass',
            'sectionsJson', 'classesJson', 'allClasses'
        ));
    }

    // ── ENREGISTREMENT ────────────────────────────────────────────────────
    public function store(StoreStudentRequest $request)
    {
        // Validation préalable des données critiques
        try {
            $class = ClassGroup::findOrFail($request->class_group_id);
            $academicYear = AcademicYear::findOrFail($request->academic_year_id);
            
            // Vérifier que la classe appartient à l'année
            if ($class->academic_year_id !== $academicYear->id) {
                return back()
                    ->withInput()
                    ->with('error', 'La classe sélectionnée n\'appartient pas à l\'année académique choisie.');
            }
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Classe ou année académique invalide.');
        }

        $data = $request->except(['photo']);

        if (empty($data['matricule'])) {
            $data['matricule'] = Student::generateMatricule();
        }

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')
                ->store('students/photos', 'public');
        }

        try {
            $student = DB::transaction(function () use ($request, $data, $class, $academicYear) {
                // 1. Vérifier la capacité AVANT de créer l'élève
                try {
                    $this->enrollments->assertClassHasCapacity($class);
                } catch (\InvalidArgumentException $e) {
                    throw new \Exception('Classe pleine : ' . $e->getMessage());
                }

                // 2. Créer l'élève
                $student = Student::create($data);
                AuditLog::log('created', $student, [], $student->toArray());

                // 3. Vérifier qu'il n'existe pas d'inscription dupliquée
                try {
                    $this->enrollments->assertNoDuplicateEnrollment(
                        $student,
                        $request->academic_year_id
                    );
                } catch (\InvalidArgumentException $e) {
                    // Annuler la création de l'élève si inscription dupliquée
                    $student->delete();
                    throw new \Exception('Inscription dupliquée : ' . $e->getMessage());
                }

                // 4. Créer l'inscription
                $enrollment = StudentEnrollment::create([
                    'student_id'              => $student->id,
                    'academic_year_id'        => $request->academic_year_id,
                    'class_group_id'          => $request->class_group_id,
                    'enrollment_date'         => $request->enrollment_date,
                    'is_repeating'            => $request->boolean('is_repeating'),
                    'previous_class_group_id' => $request->boolean('is_repeating')
                        ? $request->class_group_id
                        : $request->previous_class_group_id,
                    'previous_class_label'    => $request->previous_class_label,
                    'origin_school'           => $request->origin_school,
                    'status'                  => StudentEnrollment::STATUS_ACTIVE,
                ]);
                AuditLog::log('enrolled', $enrollment);

                return $student;
            });
        } catch (\Exception $e) {
            // Nettoyer la photo si créée mais que la transaction a échoué
            if (!empty($data['photo']) && Storage::disk('public')->exists($data['photo'])) {
                Storage::disk('public')->delete($data['photo']);
            }
            
            return back()
                ->withInput()
                ->with('error', 'Erreur lors de l\'inscription : ' . $e->getMessage());
        }

        return redirect()
            ->route('students.show', $student)
            ->with('success',
                "{$student->full_name} ajouté(e) et inscrit(e) avec succès. "
                . "Matricule : {$student->matricule}");
    }

    // ── DÉTAIL ────────────────────────────────────────────────────────────
    public function show(Student $student)
    {
        $student->load([
            'enrollments' => fn($q) => $q
                ->with(['classGroup.level.section', 'academicYear'])
                ->orderByDesc('created_at'),
        ]);

        $activeYear       = $this->enrollments->activeYear();
        $activeEnrollment = $activeYear
            ? $this->enrollments->activeEnrollmentForYear($student, $activeYear->id)
            : null;
        $previousEnrollment = ($activeYear && ! $activeEnrollment)
            ? $this->enrollments->previousEnrollmentForRenewal($student, $activeYear)
            : null;
        $isEditable = $this->enrollments->isEditableInActiveYear($student);
        $canEnroll  = $this->enrollments->canEnrollInActiveYear($student);

        return view('students.show', compact(
            'student', 'activeEnrollment', 'previousEnrollment',
            'activeYear', 'isEditable', 'canEnroll'
        ));
    }

    // ── FORMULAIRE MODIFICATION ───────────────────────────────────────────
    public function edit(Student $student)
    {
        if (! $this->enrollments->activeYear()) {
            return redirect()->route('students.show', $student)
                ->with('error', 'Aucune année scolaire active. Modification impossible.');
        }

        if (! $this->enrollments->isEditableInActiveYear($student)) {
            return redirect()->route('students.show', $student)
                ->with('error', 'Cet élève est rattaché à une année clôturée. Aucune modification autorisée.');
        }

        return view('students.edit', compact('student'));
    }

    // ── MISE À JOUR ───────────────────────────────────────────────────────
    public function update(UpdateStudentRequest $request, Student $student)
    {
        $data = $request->except('photo');

        if ($request->hasFile('photo')) {
            if ($student->photo) {
                Storage::disk('public')->delete($student->photo);
            }
            $data['photo'] = $request->file('photo')
                ->store('students/photos', 'public');
        }

        $old = $student->toArray();
        $student->update($data);
        AuditLog::log('updated', $student, $old, $student->toArray());

        return redirect()
            ->route('students.show', $student)
            ->with('success', "Fiche de {$student->full_name} mise à jour.");
    }

    // ── SUPPRESSION PHOTO ─────────────────────────────────────────────────
    public function deletePhoto(Student $student)
    {
        if ($student->photo) {
            Storage::disk('public')->delete($student->photo);
            $student->update(['photo' => null]);
        }
        return back()->with('success', 'Photo supprimée.');
    }

    // ── SUPPRESSION ───────────────────────────────────────────────────────
    public function destroy(Student $student)
    {
        if ($student->enrollments()->count() > 0) {
            return back()->with('error',
                "Impossible de supprimer {$student->full_name} : "
                . "il/elle a des inscriptions.");
        }

        $name = $student->full_name;
        $studentId = $student->id;
        
        if ($student->photo) {
            Storage::disk('public')->delete($student->photo);
        }

        $student->delete();
        AuditLog::log('deleted', null, ['name' => $name, 'id' => $studentId], []);

        return redirect()->route('students.index')
            ->with('success', "Élève {$name} supprimé(e).");
    }

    // ── FORMULAIRE INSCRIPTION ────────────────────────────────────────────
    public function enroll(Student $student)
    {
        $activeYear = AcademicYear::active();

        if (! $activeYear) {
            return redirect()->route('students.show', $student)
                ->with('error', 'Aucune année scolaire active. Renouvellement impossible.');
        }

        if (! $this->enrollments->canEnrollInActiveYear($student)) {
            return redirect()->route('students.show', $student)
                ->with('error',
                    "{$student->full_name} est déjà inscrit(e) pour {$activeYear->label}.");
        }

        $previousEnrollment = $this->enrollments
            ->previousEnrollmentForRenewal($student, $activeYear);

        $existingEnrollment = $this->enrollments
            ->activeEnrollmentForYear($student, $activeYear->id);

        $repeatClasses = collect();
        $promotionClasses = collect();

        if ($previousEnrollment?->classGroup?->level) {
            $level = $previousEnrollment->classGroup->level;
            $repeatClasses    = $this->enrollments->classesForRepeat($level, $activeYear);
            $promotionClasses = $this->enrollments->classesForPromotion($level, $activeYear);
        }

        $allClasses = ClassGroup::with('level.section', 'academicYear')
            ->whereHas('academicYear', fn ($q) =>
                $q->where('id', '!=', $activeYear->id)
            )
            ->orderBy('name')->get();

        $sectionsJson = Section::with(['levels' => fn ($q) =>
            $q->orderBy('order_index')
        ])->orderBy('id')->get()->map(function ($s) {
            return [
                'id'     => $s->id,
                'name'   => $s->name,
                'levels' => $s->levels->map(fn ($l) => [
                    'id'   => $l->id,
                    'name' => $l->name,
                ])->values()->toArray(),
            ];
        })->values()->toArray();

        $classesJson = [];
        $classes = ClassGroup::where('academic_year_id', $activeYear->id)
            ->with(['level.section'])
            ->get();

        foreach ($classes as $c) {
            $classesJson[] = [
                'id'             => $c->id,
                'full_name'      => $c->full_name,
                'level_id'       => $c->level_id,
                'section_id'     => $c->level?->section_id,
                'max_students'   => $c->max_students,
                'students_count' => $c->studentEnrollments()
                    ->where('status', StudentEnrollment::STATUS_ACTIVE)
                    ->count(),
                'is_repeat'      => $repeatClasses->contains('id', $c->id),
                'is_promotion'   => $promotionClasses->contains('id', $c->id),
            ];
        }

        return view('students.enroll', compact(
            'student', 'activeYear', 'existingEnrollment', 'previousEnrollment',
            'allClasses', 'sectionsJson', 'classesJson',
            'repeatClasses', 'promotionClasses'
        ));
    }

    // ── ENREGISTREMENT INSCRIPTION ────────────────────────────────────────
    public function storeEnrollment(StoreEnrollmentRequest $request,
                                    Student $student)
    {
        $activeYear = AcademicYear::active();

        if (! $activeYear || (int) $request->academic_year_id !== $activeYear->id) {
            return back()->with('error',
                'Le renouvellement ne peut se faire que pour l\'année scolaire active.');
        }

        $class = ClassGroup::findOrFail($request->class_group_id);

        if ($class->academic_year_id !== $activeYear->id) {
            return back()->with('error',
                'La classe sélectionnée n\'appartient pas à l\'année active.');
        }

        try {
            DB::transaction(function () use ($request, $student, $class, $activeYear) {
                $this->enrollments->assertNoDuplicateEnrollment(
                    $student,
                    $activeYear->id
                );
                // ATTENTION: Limite de capacité RETIRÉE par choix du client (point 2 du feedback)
                // $this->enrollments->assertClassHasCapacity($class);

                $previousClassId = $request->previous_class_group_id;

                if (! $previousClassId) {
                    $prev = $this->enrollments
                        ->previousEnrollmentForRenewal($student, $activeYear);
                    $previousClassId = $prev?->class_group_id;
                }

                $enrollment = StudentEnrollment::create([
                    'student_id'              => $student->id,
                    'academic_year_id'        => $activeYear->id,
                    'class_group_id'          => $request->class_group_id,
                    'enrollment_date'         => $request->enrollment_date,
                    'is_repeating'            => $request->boolean('is_repeating'),
                    'previous_class_group_id' => $previousClassId,
                    'origin_school'           => $request->origin_school,
                    'status'                  => StudentEnrollment::STATUS_ACTIVE,
                ]);

                AuditLog::log('enrolled', $enrollment);
            });
        } catch (\InvalidArgumentException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('students.show', $student)
            ->with('success',
                "{$student->full_name} inscrit(e) en {$class->full_name} "
                . "pour {$activeYear->label}.");
    }

    // ── TRANSFERT ─────────────────────────────────────────────────────────
    public function transfer(Request $request, StudentEnrollment $enrollment)
    {
        $request->validate([
            'new_class_id'    => ['required', 'exists:class_groups,id'],
            'transfer_reason' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $newClass = ClassGroup::findOrFail($request->new_class_id);

            DB::transaction(function () use ($request, $enrollment, $newClass) {
                $this->enrollments->assertClassHasCapacity($newClass);

                $enrollment->update([
                    'class_group_id'       => $newClass->id,
                    'transfer_date'        => now()->toDateString(),
                    'transfer_destination' => $newClass->full_name,
                ]);

                AuditLog::log('transferred', $enrollment);
            });
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success',
            "{$enrollment->student->full_name} transféré(e) "
            . "en {$newClass->full_name}.");
    }

    // ── CHANGEMENT DE STATUT ──────────────────────────────────────────────
    public function updateStatus(Request $request,
                                  StudentEnrollment $enrollment)
    {
        $request->validate([
            'status' => ['required',
                         'in:active,transferred,withdrawn,excluded'],
        ]);

        $enrollment->update(['status' => $request->status]);
        AuditLog::log('status_changed', $enrollment);

        $labels = [
            'active'      => 'Actif(ve)',
            'transferred' => 'Transféré(e)',
            'withdrawn'   => 'Retiré(e)',
            'excluded'    => 'Exclu(e)',
        ];

        return back()->with('success',
            "Statut de {$enrollment->student->full_name} : "
            . $labels[$request->status]);
    }
}