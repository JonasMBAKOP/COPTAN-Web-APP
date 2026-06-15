<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGradeRequest;
use App\Models\AcademicYear;
use App\Models\AuditLog;
use App\Models\ClassGroup;
use App\Models\ClassSubject;
use App\Models\Grade;
use App\Models\GradeLock;
use App\Models\Section;
use App\Models\Sequence;
use App\Models\StudentEnrollment;
use App\Models\TeacherAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class GradeController extends Controller
{
    // ── HELPERS ───────────────────────────────────────────────────────────

    private function activeYear(): ?AcademicYear
    {
        return AcademicYear::active();
    }

    private function currentUser()
    {
        /** @var \App\Models\User $authUser */
        $user = Auth::user();

        return Auth::user();
    }

    private function isAdmin(): bool
    {
        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();
        return $authUser->hasAnyRole([
            'super-admin', 'directeur', 'censeur'
        ]);
    }

    /** Sections où l'user enseigne (ou toutes si admin) */
    private function teacherSections(?AcademicYear $year): \Illuminate\Support\Collection
    {
        if ($this->isAdmin() || !$year) {
            return Section::orderBy('id')->get();
        }

        $user  = $this->currentUser();
        $staff = $user->staff;
        if (!$staff) return collect();

        $sectionIds = TeacherAssignment::where('staff_id', $staff->id)
            ->where('academic_year_id', $year->id)
            ->with('classSubject.classGroup.level.section')
            ->get()
            ->pluck('classSubject.classGroup.level.section_id')
            ->unique();

        return Section::whereIn('id', $sectionIds)
            ->orderBy('id')->get();
    }

    /** Matières enseignées dans une section par ce user */
    // private function teacherSubjectsInSection(
    //     int $sectionId, ?AcademicYear $year
    // ): \Illuminate\Support\Collection {
    //     if (!$year) return collect();

    //     if ($this->isAdmin()) {
    //         return ClassSubject::whereHas('classGroup.level',
    //             fn($q) => $q->where('section_id', $sectionId)
    //         )->where('class_group_id', function($q) use ($year) {
    //             $q->select('id')->from('class_groups')
    //               ->where('academic_year_id', $year->id);
    //         })->where('is_active', true)
    //           ->with('subject')
    //           ->get()
    //           ->pluck('subject')
    //           ->unique('id')
    //           ->sortBy('name_fr')
    //           ->values();
    //     }

    //     $staff = $this->currentUser()->staff;
    //     if (!$staff) return collect();

    //     return TeacherAssignment::where('staff_id', $staff->id)
    //         ->where('academic_year_id', $year->id)
    //         ->with([
    //             'classSubject.subject',
    //             'classSubject.classGroup.level',
    //         ])
    //         ->get()
    //         ->filter(fn($ta) =>
    //             $ta->classSubject?->classGroup?->level?->section_id === $sectionId
    //             && $ta->classSubject?->classGroup?->academic_year_id === $year->id
    //         )
    //         ->pluck('classSubject.subject')
    //         ->filter()
    //         ->unique('id')
    //         ->sortBy('name_fr')
    //         ->values();
    // }
    private function teacherSubjectsInSection(
        int $sectionId, ?AcademicYear $year
    ): \Illuminate\Support\Collection {
        if (!$year) return collect();

        if ($this->isAdmin()) {
            // FIX : whereHas au lieu d'un sous-requête = qui retournait plusieurs lignes
            return ClassSubject::where('is_active', true)
                ->whereHas('classGroup', fn($q) =>
                    $q->where('academic_year_id', $year->id)
                    ->whereHas('level', fn($q2) =>
                        $q2->where('section_id', $sectionId)
                    )
                )
                ->with('subject')
                ->get()
                ->pluck('subject')
                ->filter()
                ->unique('id')
                ->sortBy('name_fr')
                ->values();
        }

        $staff = $this->currentUser()->staff;
        if (!$staff) return collect();

        return TeacherAssignment::where('staff_id', $staff->id)
            ->where('academic_year_id', $year->id)
            ->whereHas('classSubject.classGroup', fn($q) =>
                $q->where('academic_year_id', $year->id)
                ->whereHas('level', fn($q2) =>
                    $q2->where('section_id', $sectionId)
                )
            )
            ->with('classSubject.subject')
            ->get()
            ->pluck('classSubject.subject')
            ->filter()
            ->unique('id')
            ->sortBy('name_fr')
            ->values();
    }

    /** Classes où ce user enseigne un sujet donné dans une section */
    private function teacherClassesForSubject(
        int $sectionId, int $subjectId, ?AcademicYear $year
    ): \Illuminate\Support\Collection {
        if (!$year) return collect();

        $query = ClassGroup::where('academic_year_id', $year->id)
            ->whereHas('level', fn($q) => $q->where('section_id', $sectionId))
            ->whereHas('classSubjects', fn($q) =>
                $q->where('subject_id', $subjectId)->where('is_active', true)
            )
            ->with('level.section')
            ->orderBy('name');

        if (!$this->isAdmin()) {
            $staff = $this->currentUser()->staff;
            if (!$staff) return collect();

            $query->whereHas('classSubjects', fn($q) =>
                $q->where('subject_id', $subjectId)
                  ->where('is_active', true)
                  ->whereHas('teacherAssignments', fn($q2) =>
                      $q2->where('staff_id', $staff->id)
                         ->where('academic_year_id', $year->id)
                  )
            );
        }

        return $query->get();
    }

    // // ── LISTE DES CLASSES PAR SÉQUENCE ───────────────────────────────────
    // public function index(Request $request)
    // {
    //     $activeYear = AcademicYear::active();

    //     if (!$activeYear) {
    //         return view('grades.index', [
    //             'sections'   => collect(),
    //             'sequences'  => collect(),
    //             'activeYear' => null,
    //             'gradeLocks' => collect(),
    //             'gradeCounts'=> collect(),
    //         ]);
    //     }

    //     $sequences = Sequence::where('academic_year_id', $activeYear->id)
    //         ->with('trimester')
    //         ->orderBy('number')
    //         ->get();

    //     $sections = Section::with([
    //         'levels.classGroups' => fn($q) =>
    //             $q->where('academic_year_id', $activeYear->id)
    //               ->withCount([
    //                   'studentEnrollments as enrolled' => fn($q2) =>
    //                       $q2->where('status', 'active'),
    //                   'classSubjects as subjects_count' => fn($q2) =>
    //                       $q2->where('is_active', true),
    //               ])
    //               ->orderBy('name'),
    //     ])->orderBy('id')->get();

    //     // Verrous de notes (par class_group_id et sequence_id)
    //     $gradeLocks = GradeLock::where('is_locked', true)
    //         ->pluck('sequence_id', 'class_group_id')
    //         ->groupBy(fn($seqId, $cgId) => $cgId);

    //     // Réindexer proprement
    //     $locks = GradeLock::where('is_locked', true)
    //         ->get()
    //         ->groupBy('class_group_id');

    //     // Nombre de notes saisies par class_group + sequence
    //     $gradeCounts = Grade::select('class_subjects.class_group_id', 'grades.sequence_id')
    //         ->selectRaw('COUNT(*) as cnt')
    //         ->join('class_subjects',
    //             'class_subjects.id', '=', 'grades.class_subject_id')
    //         ->whereNotNull('grades.grade')
    //         ->orWhere('grades.is_absent', true)
    //         ->groupBy('class_subjects.class_group_id', 'grades.sequence_id')
    //         ->get()
    //         ->groupBy('class_group_id');

    //     return view('grades.index', compact(
    //         'sections', 'sequences', 'activeYear', 'locks', 'gradeCounts'
    //     ));
    // }

    public function index(Request $request)
    {
        $activeYear = $this->activeYear();
        $sections   = $activeYear
            ? Section::with([
                'levels.classGroups' => fn($q) =>
                    $q->where('academic_year_id', $activeYear->id)
                      ->withCount([
                          'studentEnrollments as enrolled' => fn($q2) =>
                              $q2->where('status', 'active'),
                          'classSubjects as subjects_count' => fn($q2) =>
                              $q2->where('is_active', true),
                      ])
                      ->orderBy('name'),
            ])->orderBy('id')->get()
            : collect();

        $sequences = $activeYear
            ? Sequence::where('academic_year_id', $activeYear->id)
                ->with('trimester')->orderBy('number')->get()
            : collect();

        // Sélection de section (filtre)
        $selectedSectionId = $request->input('section_id',
            $sections->first()?->id);
        $selectedSection   = $sections->find($selectedSectionId);

        // Verrous
        $locks = GradeLock::where('is_locked', true)
            ->get()->groupBy('class_group_id');

        // Nombres de notes saisies
        $gradeCounts = Grade::selectRaw(
            'class_subjects.class_group_id,
             grades.sequence_id,
             COUNT(*) as cnt'
        )->join('class_subjects',
            'class_subjects.id', '=', 'grades.class_subject_id')
         ->where(fn($q) =>
             $q->whereNotNull('grades.grade')
               ->orWhere('grades.is_absent', true)
         )
         ->groupBy('class_subjects.class_group_id', 'grades.sequence_id')
         ->get()->groupBy('class_group_id');

        return view('grades.index', compact(
            'activeYear', 'sections', 'sequences',
            'selectedSection', 'selectedSectionId',
            'locks', 'gradeCounts'
        ));
    }

    // ══ PAGE NOTES (consultation enseignant) ══════════════════════════════

    public function notes(Request $request)
    {
        $activeYear = $this->activeYear();
        $sections   = $this->teacherSections($activeYear);

        $selectedSectionId  = $request->input('section_id');
        $selectedSubjectId  = $request->input('subject_id');
        $selectedClassId    = $request->input('class_id');
        $selectedSequenceId = $request->input('sequence_id');

        $sequences = $activeYear
            ? Sequence::where('academic_year_id', $activeYear->id)
                ->with('trimester')->orderBy('number')->get()
            : collect();

        // Matières disponibles dans la section
        $subjects = $selectedSectionId
            ? $this->teacherSubjectsInSection((int)$selectedSectionId, $activeYear)
            : collect();

        // Classes disponibles
        $classes = ($selectedSectionId && $selectedSubjectId)
            ? $this->teacherClassesForSubject(
                (int)$selectedSectionId, (int)$selectedSubjectId, $activeYear)
            : collect();

        // Notes si tout est sélectionné
        $enrollments   = collect();
        $grades        = collect();
        $classSubject  = null;
        $selectedClass = null;
        $sequence      = null;

        if ($selectedClassId && $selectedSubjectId && $selectedSequenceId) {
            $selectedClass = ClassGroup::find($selectedClassId);
            $sequence      = Sequence::find($selectedSequenceId);

            $classSubject = ClassSubject::where([
                'class_group_id' => $selectedClassId,
                'subject_id'     => $selectedSubjectId,
                'is_active'      => true,
            ])->with('subject')->first();

            if ($classSubject) {
                $enrollments = StudentEnrollment::where([
                    'class_group_id'   => $selectedClassId,
                    'academic_year_id' => $activeYear?->id,
                    'status'           => 'active',
                ])->with('student')
                  ->get()
                  ->sortBy('student.last_name');

                $grades = Grade::whereIn(
                    'student_enrollment_id', $enrollments->pluck('id')
                )->where([
                    'class_subject_id' => $classSubject->id,
                    'sequence_id'      => $selectedSequenceId,
                ])->get()->keyBy('student_enrollment_id');
            }
        }

        return view('grades.notes', compact(
            'activeYear', 'sections', 'sequences', 'subjects', 'classes',
            'selectedSectionId', 'selectedSubjectId',
            'selectedClassId', 'selectedSequenceId',
            'enrollments', 'grades', 'classSubject',
            'selectedClass', 'sequence'
        ));
    }

    // // ── FORMULAIRE DE SAISIE ─────────────────────────────────────────────
    // public function entry(ClassGroup $classGroup, Sequence $sequence)
    // {
    //     $classGroup->load([
    //         'level.section',
    //         'academicYear',
    //         'classSubjects' => fn($q) =>
    //             $q->where('is_active', true)
    //               ->with([
    //                   'subject',
    //                   'teacherAssignments' => fn($q2) =>
    //                       $q2->where('academic_year_id', $classGroup->academic_year_id)
    //                          ->with('staff'),
    //               ])
    //               ->orderBy('subject_id'),
    //     ]);

    //     // Vérifier si verrouillé
    //     $lock = GradeLock::where([
    //         'class_group_id' => $classGroup->id,
    //         'sequence_id'    => $sequence->id,
    //     ])->first();
    //     $isLocked = $lock?->is_locked ?? false;

    //     // Élèves inscrits
    //     $enrollments = StudentEnrollment::where([
    //         'class_group_id'   => $classGroup->id,
    //         'academic_year_id' => $classGroup->academic_year_id,
    //         'status'           => 'active',
    //     ])->with('student')
    //       ->get()
    //       ->sortBy('student.last_name');

    //     // Notes existantes indexées par [enrollment_id][class_subject_id]
    //     $existingGrades = Grade::whereIn(
    //         'student_enrollment_id', $enrollments->pluck('id')
    //     )->where('sequence_id', $sequence->id)
    //      ->get()
    //      ->keyBy(fn($g) => $g->student_enrollment_id . '_' . $g->class_subject_id);

    //     // Vérifier permissions enseignant
    //     /** @var \App\Models\User $user */
    //     $user = Auth::user();
    //     $canEditAll = $user->hasAnyRole(['super-admin','directeur','censeur']);

    //     // Matières que l'enseignant est assigné à enseigner
    //     $mySubjectIds = collect();
    //     if (!$canEditAll && $user->hasRole('enseignant') && $user->staff) {
    //         $mySubjectIds = TeacherAssignment::where([
    //             'staff_id'         => $user->staff->id,
    //             'academic_year_id' => $classGroup->academic_year_id,
    //         ])->whereHas('classSubject', fn($q) =>
    //             $q->where('class_group_id', $classGroup->id)
    //         )->with('classSubject')
    //          ->get()
    //          ->pluck('classSubject.id');
    //     }

    //     // Calcul de progression
    //     $totalCells = $enrollments->count() * $classGroup->classSubjects->count();
    //     $filledCells = $existingGrades->filter(
    //         fn($g) => $g->grade !== null || $g->is_absent
    //     )->count();

    //     return view('grades.entry', compact(
    //         'classGroup', 'sequence', 'lock', 'isLocked',
    //         'enrollments', 'existingGrades',
    //         'canEditAll', 'mySubjectIds',
    //         'totalCells', 'filledCells'
    //     ));
    // }

    // ══ SAISIE DES NOTES ═══════════════════════════════════════════════════

    public function entry(Request $request)
    {
        $activeYear = $this->activeYear();
        $sections   = $this->teacherSections($activeYear);

        $selectedSectionId  = $request->input('section_id');
        $selectedSubjectId  = $request->input('subject_id');
        $selectedClassId    = $request->input('class_id');
        $selectedSequenceId = $request->input('sequence_id');

        $sequences = $activeYear
            ? Sequence::where('academic_year_id', $activeYear->id)
                ->with('trimester')->orderBy('number')->get()
            : collect();

        $subjects = $selectedSectionId
            ? $this->teacherSubjectsInSection((int)$selectedSectionId, $activeYear)
            : collect();

        $classes = ($selectedSectionId && $selectedSubjectId)
            ? $this->teacherClassesForSubject(
                (int)$selectedSectionId, (int)$selectedSubjectId, $activeYear)
            : collect();

        // Si tout sélectionné → charger les données
        $enrollments   = collect();
        $grades        = collect();
        $classSubject  = null;
        $selectedClass = null;
        $sequence      = null;
        $isLocked      = false;
        $lock          = null;

        $readyToShow = $selectedClassId && $selectedSubjectId
                    && $selectedSequenceId && $activeYear;

        if ($readyToShow) {
            $selectedClass = ClassGroup::with('level.section', 'academicYear')
                ->find($selectedClassId);
            $sequence      = Sequence::with('trimester')
                ->find($selectedSequenceId);

            $classSubject = ClassSubject::where([
                'class_group_id' => $selectedClassId,
                'subject_id'     => $selectedSubjectId,
                'is_active'      => true,
            ])->with('subject')->first();

            if ($classSubject && $selectedClass && $sequence) {
                $lock = GradeLock::where([
                    'class_group_id' => $selectedClassId,
                    'sequence_id'    => $selectedSequenceId,
                ])->first();
                $isLocked = $lock?->is_locked ?? false;

                $enrollments = StudentEnrollment::where([
                    'class_group_id'   => $selectedClassId,
                    'academic_year_id' => $activeYear->id,
                    'status'           => 'active',
                ])->with('student')
                  ->get()
                  ->sortBy('student.last_name');

                $grades = Grade::whereIn(
                    'student_enrollment_id', $enrollments->pluck('id')
                )->where([
                    'class_subject_id' => $classSubject->id,
                    'sequence_id'      => $selectedSequenceId,
                ])->get()->keyBy('student_enrollment_id');
            }
        }

        return view('grades.entry', compact(
            'activeYear', 'sections', 'sequences', 'subjects', 'classes',
            'selectedSectionId', 'selectedSubjectId',
            'selectedClassId', 'selectedSequenceId',
            'enrollments', 'grades', 'classSubject',
            'selectedClass', 'sequence',
            'readyToShow', 'isLocked', 'lock'
        ));
    }

    // // ── ENREGISTREMENT DES NOTES ─────────────────────────────────────────
    // public function save(StoreGradeRequest $request,
    //                      ClassGroup $classGroup,
    //                      Sequence $sequence)
    // {
    //     // Vérifier si verrouillé
    //     $lock = GradeLock::where([
    //         'class_group_id' => $classGroup->id,
    //         'sequence_id'    => $sequence->id,
    //         'is_locked'      => true,
    //     ])->exists();

    //     if ($lock) {
    //         return back()->with('error',
    //             'Les notes de cette séquence sont verrouillées.');
    //     }

    //     /** @var \App\Models\User $user */
    //     $user = Auth::user();
    //     $canEditAll = $user->hasAnyRole(['super-admin','directeur','censeur']);

    //     $grades = $request->input('grades', []);
    //     $saved  = 0;

    //     foreach ($grades as $key => $data) {
    //         // Clé = {enrollment_id}_{class_subject_id}
    //         [$enrollmentId, $classSubjectId] = explode('_', $key);

    //         // Vérifier autorisation enseignant
    //         if (!$canEditAll) {
    //             $isAssigned = TeacherAssignment::where([
    //                 'staff_id'         => $user->staff?->id,
    //                 'academic_year_id' => $classGroup->academic_year_id,
    //                 'class_subject_id' => $classSubjectId,
    //             ])->exists();

    //             if (!$isAssigned) continue;
    //         }

    //         $isAbsent = !empty($data['is_absent']);
    //         $grade    = $isAbsent ? null : (
    //             isset($data['grade']) && $data['grade'] !== ''
    //                 ? (float) $data['grade']
    //                 : null
    //         );

    //         Grade::updateOrCreate(
    //             [
    //                 'student_enrollment_id' => (int) $enrollmentId,
    //                 'class_subject_id'      => (int) $classSubjectId,
    //                 'sequence_id'           => $sequence->id,
    //             ],
    //             [
    //                 'grade'      => $grade,
    //                 'is_absent'  => $isAbsent,
    //                 'entered_by' => $user->id,
    //                 'entered_at' => now(),
    //                 'updated_by' => $user->id,
    //             ]
    //         );
    //         $saved++;
    //     }

    //     AuditLog::log('grades_saved', $classGroup);

    //     return back()->with('success',
    //         "{$saved} note(s) enregistrée(s) pour "
    //         . "{$classGroup->full_name} — {$sequence->label}.");
    // }

    // ══ ENREGISTREMENT (FIX BD) ════════════════════════════════════════════

    public function save(Request $request)
    {
        $request->validate([
            'sequence_id'      => ['required', 'exists:sequences,id'],
            'class_subject_id' => ['required', 'exists:class_subjects,id'],
            'class_group_id'   => ['required', 'exists:class_groups,id'],
        ]);

        // Vérifier verrou
        $isLocked = GradeLock::where([
            'class_group_id' => $request->class_group_id,
            'sequence_id'    => $request->sequence_id,
            'is_locked'      => true,
        ])->exists();

        if ($isLocked) {
            return back()->with('error',
                'Ces notes sont verrouillées. Modification impossible.');
        }

        // Vérifier permission enseignant
        $user = $this->currentUser();
        if (!$this->isAdmin()) {
            $staff = $user->staff;
            if (!$staff) abort(403);

            $isAssigned = TeacherAssignment::where([
                'staff_id'         => $staff->id,
                'class_subject_id' => $request->class_subject_id,
            ])->whereHas('academicYear', fn($q) => $q->where('is_active', true))
              ->exists();

            if (!$isAssigned) abort(403, 'Non assigné à cette matière.');
        }

        $gradesInput = $request->input('grades', []);
        $absentInput = $request->input('absent', []);
        $saved       = 0;
        $errors      = 0;

        foreach ($gradesInput as $enrollmentId => $grade) {
            $enrollmentId = (int)$enrollmentId;
            if ($enrollmentId <= 0) continue;

            $isAbsent = array_key_exists($enrollmentId, $absentInput);
            $gradeVal = null;

            if (!$isAbsent && $grade !== '' && $grade !== null) {
                $v = (float)$grade;
                if ($v < 0 || $v > 20) { $errors++; continue; }
                $gradeVal = round($v * 4) / 4; // arrondi 0.25
            }

            Grade::updateOrCreate(
                [
                    'student_enrollment_id' => $enrollmentId,
                    'class_subject_id'      => (int)$request->class_subject_id,
                    'sequence_id'           => (int)$request->sequence_id,
                ],
                [
                    'grade'      => $gradeVal,
                    'is_absent'  => $isAbsent,
                    'entered_by' => $user->id,
                    'entered_at' => now(),
                    'updated_by' => $user->id,
                ]
            );
            $saved++;
        }

        AuditLog::log('grades_saved', ClassGroup::find($request->class_group_id));

        $msg = "{$saved} note(s) enregistrée(s).";
        if ($errors > 0) $msg .= " {$errors} note(s) invalide(s) ignorée(s).";

        return redirect()->back()->with('success', $msg);
    }

    // ══ DÉTAIL (clic sur % dans Vue Globale) ══════════════════════════════

    public function detail(Request $request,
                           ClassGroup $classGroup,
                           Sequence $sequence)
    {
        $classGroup->load([
            'level.section', 'academicYear',
            'classSubjects' => fn($q) =>
                $q->where('is_active', true)->with('subject')->orderBy('subject_id'),
        ]);

        $enrollments = StudentEnrollment::where([
            'class_group_id'   => $classGroup->id,
            'academic_year_id' => $classGroup->academic_year_id,
            'status'           => 'active',
        ])->with('student')
          ->get()
          ->sortBy('student.last_name');

        // Filtre matière optionnel
        $filterSubjectId = $request->input('subject_id');
        $subjects = $classGroup->classSubjects;
        $displaySubjects = $filterSubjectId
            ? $subjects->where('subject_id', $filterSubjectId)
            : $subjects;

        // Charger toutes les notes de cette séquence pour cette classe
        $allGrades = Grade::whereIn(
            'student_enrollment_id', $enrollments->pluck('id')
        )->where('sequence_id', $sequence->id)
         ->whereIn('class_subject_id', $subjects->pluck('id'))
         ->get()
         ->groupBy('student_enrollment_id')
         ->map(fn($g) => $g->keyBy('class_subject_id'));

        $lock = GradeLock::where([
            'class_group_id' => $classGroup->id,
            'sequence_id'    => $sequence->id,
        ])->first();

        return view('grades.detail', compact(
            'classGroup', 'sequence', 'enrollments',
            'subjects', 'displaySubjects', 'filterSubjectId',
            'allGrades', 'lock'
        ));
    }

    // // ── VERROUILLER / DÉVERROUILLER ──────────────────────────────────────
    // public function toggleLock(Request $request,
    //                            ClassGroup $classGroup,
    //                            Sequence $sequence)
    // {
    //     // Seuls censeur/directeur/admin peuvent verrouiller
    //     /** @var \App\Models\User|null $user */
    //     $user = Auth::user();
    //     if (!$user->hasAnyRole([
    //         'super-admin','directeur','censeur'
    //     ])) {
    //         abort(403, 'Permission insuffisante.');
    //     }

    //     $lock = GradeLock::firstOrCreate([
    //         'class_group_id' => $classGroup->id,
    //         'sequence_id'    => $sequence->id,
    //     ]);

    //     $lock->update([
    //         'is_locked' => !$lock->is_locked,
    //         'locked_by' => Auth::id(),
    //         'locked_at' => $lock->is_locked ? null : now(),
    //     ]);

    //     $msg = $lock->is_locked
    //         ? "Notes de {$classGroup->full_name} verrouillées."
    //         : "Notes de {$classGroup->full_name} déverrouillées.";

    //     AuditLog::log('grades_' . ($lock->is_locked ? 'locked' : 'unlocked'),
    //                   $classGroup);

    //     return back()->with('success', $msg);
    // }

    // ══ VERROU ══════════════════════════════════════════════════════════════

    public function toggleLock(ClassGroup $classGroup, Sequence $sequence)
    {
        if (!$this->isAdmin()) abort(403);

        $lock = GradeLock::firstOrCreate([
            'class_group_id' => $classGroup->id,
            'sequence_id'    => $sequence->id,
        ], ['is_locked' => false]);

        $lock->update([
            'is_locked' => !$lock->is_locked,
            'locked_by' => Auth::id(),
            'locked_at' => $lock->is_locked ? null : now(),
        ]);

        AuditLog::log(
            'grades_' . ($lock->is_locked ? 'locked' : 'unlocked'),
            $classGroup
        );

        return back()->with('success',
            $lock->is_locked
                ? "Notes de {$classGroup->full_name} verrouillées."
                : "Notes de {$classGroup->full_name} déverrouillées."
        );
    }

    // // ══ API AJAX ═══════════════════════════════════════════════════════════

    // public function apiSubjects(Request $request)
    // {
    //     $sectionId  = (int)$request->input('section_id');
    //     $activeYear = $this->activeYear();

    //     $subjects = $this->teacherSubjectsInSection($sectionId, $activeYear)
    //         ->map(fn($s) => ['id' => $s->id, 'name' => $s->name_fr, 'code' => $s->code]);

    //     return response()->json(['subjects' => $subjects->values()]);
    // }

    // public function apiClasses(Request $request)
    // {
    //     $sectionId  = (int)$request->input('section_id');
    //     $subjectId  = (int)$request->input('subject_id');
    //     $activeYear = $this->activeYear();

    //     $classes = $this->teacherClassesForSubject($sectionId, $subjectId, $activeYear)
    //         ->map(fn($c) => [
    //             'id'        => $c->id,
    //             'full_name' => $c->full_name,
    //             'enrolled'  => $c->studentEnrollments()
    //                 ->where('status', 'active')->count(),
    //         ]);

    //     return response()->json(['classes' => $classes->values()]);
    // }

    // ── API AJAX (avec gestion d'erreur) ──────────────────────────────────────

    public function apiSubjects(Request $request)
    {
        try {
            $sectionId  = (int)$request->input('section_id', 0);
            $activeYear = $this->activeYear();

            if (!$sectionId || !$activeYear) {
                return response()->json(['subjects' => []]);
            }

            $subjects = $this->teacherSubjectsInSection($sectionId, $activeYear)
                ->map(fn($s) => [
                    'id'   => $s->id,
                    'name' => $s->name_fr,
                    'code' => $s->code ?? '',
                ]);

            return response()->json(['subjects' => $subjects->values()]);

        } catch (\Throwable $e) {
            \Log::error('apiSubjects error: ' . $e->getMessage());
            return response()->json(['subjects' => [], 'error' => $e->getMessage()], 500);
        }
    }

    public function apiClasses(Request $request)
    {
        try {
            $sectionId  = (int)$request->input('section_id', 0);
            $subjectId  = (int)$request->input('subject_id', 0);
            $activeYear = $this->activeYear();

            if (!$sectionId || !$subjectId || !$activeYear) {
                return response()->json(['classes' => []]);
            }

            $classes = $this->teacherClassesForSubject($sectionId, $subjectId, $activeYear)
                ->map(fn($c) => [
                    'id'        => $c->id,
                    'full_name' => $c->full_name,
                    'enrolled'  => $c->studentEnrollments()
                        ->where('status', 'active')->count(),
                ]);

            return response()->json(['classes' => $classes->values()]);

        } catch (\Throwable $e) {
            \Log::error('apiClasses error: ' . $e->getMessage());
            return response()->json(['classes' => [], 'error' => $e->getMessage()], 500);
        }
    }
}