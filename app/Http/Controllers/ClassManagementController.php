<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClassGroupRequest;
use App\Http\Requests\UpdateClassGroupRequest;
use App\Models\AcademicYear;
use App\Models\AuditLog;
use App\Models\ClassGroup;
use App\Models\Level;
use App\Models\Section;
use App\Models\Staff;
use App\Models\TeacherAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClassManagementController extends Controller
{
    private function isClassAdmin(): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        return $user->hasAnyRole(['super-admin', 'directeur', 'censeur', 'fondateur']);
    }

    private function isTeacherView(): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        return ! $this->isClassAdmin() && $user->hasRole('enseignant');
    }

    /** IDs des classes où l'enseignant est affecté pour l'année donnée */
    private function teacherClassIds(?AcademicYear $year): array
    {
        if (! $year) {
            return [];
        }

        /** @var \App\Models\User $user */
        $user  = Auth::user();
        $staff = $user->staff;

        if (! $staff) {
            return [];
        }

        return TeacherAssignment::where('staff_id', $staff->id)
            ->where('academic_year_id', $year->id)
            ->whereHas('classSubject')
            ->with('classSubject:id,class_group_id')
            ->get()
            ->pluck('classSubject.class_group_id')
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    // ── LISTE ─────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $activeYear   = AcademicYear::active();
        $isTeacher    = $this->isTeacherView();
        $teacherIds   = $isTeacher ? $this->teacherClassIds($activeYear) : [];

        if ($isTeacher) {
            $selectedYear = $activeYear;
        } else {
            $selectedYearId = $request->input('year_id');
            $selectedYear   = $selectedYearId
                ? AcademicYear::find($selectedYearId)
                : $activeYear;
        }

        $years = AcademicYear::orderByDesc('start_date')->get();

        $sectionsQuery = Section::with([
            'levels' => fn ($q) => $q->orderBy('order_index'),
        ]);

        $classGroups = collect();

        if ($selectedYear) {
            $classesQuery = ClassGroup::where('academic_year_id', $selectedYear->id)
                ->with(['level.section', 'titularStaff', 'studentEnrollments'])
                ->withCount([
                    'studentEnrollments' => fn ($q) =>
                        $q->where('status', 'active'),
                    'classSubjects',
                ]);

            if ($isTeacher) {
                if (empty($teacherIds)) {
                    $sections = collect();
                } else {
                    $classesQuery->whereIn('id', $teacherIds);
                    $classGroups = $classesQuery->get()->groupBy('level.section.id');

                    $sectionIds = $classGroups->keys()->filter()->values();
                    $sections   = $sectionsQuery->whereIn('id', $sectionIds)->get();
                }
            } else {
                $classGroups = $classesQuery->get()->groupBy('level.section.id');
                $sections    = $sectionsQuery->get();
            }
        } else {
            $sections = $isTeacher ? collect() : $sectionsQuery->get();
        }

        if (! isset($sections)) {
            $sections = collect();
        }

        $stats = [
            'total_classes'  => $classGroups->flatten()->count(),
            'total_students' => $classGroups->flatten()->sum('student_enrollments_count'),
            'sections_used'  => $classGroups->keys()->count(),
        ];

        return view('classes.index', compact(
            'sections', 'classGroups', 'selectedYear',
            'years', 'stats', 'activeYear', 'isTeacher'
        ));
    }

    // ── FORMULAIRE CRÉATION ───────────────────────────────────────────────
    public function create(Request $request)
    {
        $activeYear = AcademicYear::active();

        if (!$activeYear) {
            return redirect()->route('classes.index')
                ->with('error',
                    'Aucune année scolaire active. '
                    . 'Veuillez en activer une avant de créer des classes.');
        }

        $sections  = Section::with([
            'levels' => fn($q) => $q->orderBy('order_index'),
        ])->get();

        $staffList = Staff::where('is_active', true)
                          ->orderBy('last_name')
                          ->get();

        $selectedSectionId = $request->input('section_id');
        $selectedLevelId   = $request->input('level_id');

        return view('classes.create', compact(
            'activeYear', 'sections', 'staffList',
            'selectedSectionId', 'selectedLevelId'
        ));
    }

    // ── ENREGISTREMENT ────────────────────────────────────────────────────
    public function store(StoreClassGroupRequest $request)
    {
        // Verifier unicite annee + niveau + serie + sous-groupe.
        $data = $request->validated();
        $level = Level::findOrFail($data['level_id']);
        $data['series'] = $this->normalizeOptionalClassPart($data['series'] ?? '');
        $data['sub_group'] = $this->normalizeOptionalClassPart($data['sub_group'] ?? '');
        $data['name'] = ClassGroup::composeName(
            $level->name,
            $data['series'],
            $data['sub_group']
        );

        $exists = $this->classCombinationExists(
            (int) $data['academic_year_id'],
            (int) $data['level_id'],
            $data['series'],
            $data['sub_group']
        );

        if ($exists) {
            return back()
                ->withInput()
                ->with('error',
                    'Une classe avec ce nom existe déjà dans ce niveau '
                    . 'pour cette année scolaire.');
        }

        $classGroup = ClassGroup::create($data);

        AuditLog::log('created', $classGroup, [], $classGroup->toArray());

        return redirect()
            ->route('classes.show', $classGroup)
            ->with('success',
                "Classe « {$classGroup->full_name} » créée avec succès.");
    }

    // ── DÉTAIL ────────────────────────────────────────────────────────────
    public function show(ClassGroup $classGroup)
    {
        $classGroup->load([
            'level.section',
            'academicYear',
            'titularStaff',
            'classSubjects.subject.category',
            'classSubjects.teacherAssignments.staff',
            'studentEnrollments' => fn($q) =>
                $q->where('status', 'active')->with('student'),
        ]);

        $stats = [
            'students'     => $classGroup->studentEnrollments->count(),
            'subjects'     => $classGroup->classSubjects->count(),
            'boys'         => $classGroup->studentEnrollments
                                ->filter(fn($e) =>
                                    $e->student?->gender === 'M')->count(),
            'girls'        => $classGroup->studentEnrollments
                                ->filter(fn($e) =>
                                    $e->student?->gender === 'F')->count(),
        ];

        return view('classes.show',
            compact('classGroup', 'stats'));
    }

    // ── FORMULAIRE MODIFICATION ───────────────────────────────────────────
    public function edit(ClassGroup $classGroup)
    {
        // Vérifier si l'année est clôturée
        if ($classGroup->academicYear->isClosed()) {
            return redirect()
                ->route('classes.show', $classGroup)
                ->with('error',
                    'Cette classe appartient à une année clôturée '
                    . 'et ne peut plus être modifiée.');
        }

        $classGroup->load(['level.section', 'academicYear']);

        $sections  = Section::with([
            'levels' => fn($q) => $q->orderBy('order_index'),
        ])->get();

        $staffList = Staff::where('is_active', true)
                          ->orderBy('last_name')
                          ->get();

        return view('classes.edit',
            compact('classGroup', 'sections', 'staffList'));
    }

    // ── MISE À JOUR ───────────────────────────────────────────────────────
    public function update(UpdateClassGroupRequest $request,
                           ClassGroup $classGroup)
    {
        if ($classGroup->academicYear->isClosed()) {
            return back()->with('error',
                'Année clôturée — modification impossible.');
        }

        // Verifier unicite annee + niveau + serie + sous-groupe.
        $data = $request->validated();
        $level = Level::findOrFail($data['level_id']);
        $data['series'] = $this->normalizeOptionalClassPart($data['series'] ?? '');
        $data['sub_group'] = $this->normalizeOptionalClassPart($data['sub_group'] ?? '');
        $data['name'] = ClassGroup::composeName(
            $level->name,
            $data['series'],
            $data['sub_group']
        );

        $exists = $this->classCombinationExists(
            (int) $classGroup->academic_year_id,
            (int) $data['level_id'],
            $data['series'],
            $data['sub_group'],
            $classGroup->id
        );

        if ($exists) {
            return back()->withInput()
                ->with('error',
                    'Une classe avec ce nom existe déjà dans ce niveau.');
        }

        $old = $classGroup->toArray();
        $classGroup->update($data);
        AuditLog::log('updated', $classGroup, $old, $classGroup->toArray());

        return redirect()
            ->route('classes.show', $classGroup)
            ->with('success',
                "Classe « {$classGroup->full_name} » mise à jour.");
    }

    // ── SUPPRESSION ───────────────────────────────────────────────────────
    public function destroy(ClassGroup $classGroup)
    {
        if ($classGroup->academicYear->isClosed()) {
            return back()->with('error',
                'Impossible de supprimer une classe d\'une année clôturée.');
        }

        if ($classGroup->studentEnrollments()->count() > 0) {
            return back()->with('error',
                'Impossible de supprimer cette classe : '
                . 'elle contient des élèves inscrits.');
        }

        $name = $classGroup->full_name;

        // Supprimer les matières et verrous liés
        $classGroup->classSubjects()->delete();
        $classGroup->gradeLocks()->delete();
        $classGroup->delete();

        AuditLog::log('deleted', null, ['name' => $name], []);

        return redirect()
            ->route('classes.index')
            ->with('success', "Classe « {$name} » supprimée.");
    }

    // ── GESTION DES NIVEAUX ───────────────────────────────────────────────
    public function storeLevel(Request $request, Section $section)
    {
        $request->validate([
            'name'          => ['required', 'string', 'max:100'],
            'order_index'   => ['required', 'integer', 'min:1'],
            'is_exam_class' => ['boolean'],
        ]);

        $exists = Level::where('section_id', $section->id)
                       ->where('name', $request->name)->exists();

        if ($exists) {
            return back()->with('error',
                'Ce niveau existe déjà dans cette section.');
        }

        Level::create([
            'section_id'    => $section->id,
            'name'          => $request->name,
            'order_index'   => $request->order_index,
            'is_exam_class' => $request->boolean('is_exam_class'),
        ]);

        return back()->with('success',
            "Niveau « {$request->name} » ajouté.");
    }

    public function updateLevel(Request $request, Level $level)
    {
        $request->validate([
            'name'          => ['required', 'string', 'max:100'],
            'order_index'   => ['required', 'integer', 'min:1'],
            'is_exam_class' => ['boolean'],
        ]);

        $old = $level->toArray();
        $level->update([
            'name'          => $request->name,
            'order_index'   => $request->order_index,
            'is_exam_class' => $request->boolean('is_exam_class'),
        ]);

        AuditLog::log('updated', $level, $old, $level->toArray());

        return back()->with('success',
            "Niveau « {$level->name} » mis à jour.");
    }

    public function destroyLevel(Level $level)
    {
        if ($level->classGroups()->count() > 0) {
            return back()->with('error',
                'Impossible de supprimer ce niveau : '
                . 'il contient des classes.');
        }

        $name = $level->name;
        $level->delete();

        return back()->with('success',
            "Niveau « {$name} » supprimé.");
    }

    private function normalizeOptionalClassPart(?string $value): string
    {
        return trim((string) $value);
    }

    private function classCombinationExists(
        int $academicYearId,
        int $levelId,
        string $series,
        string $subGroup,
        ?int $exceptId = null
    ): bool {
        return ClassGroup::where('academic_year_id', $academicYearId)
            ->where('level_id', $levelId)
            ->where('series', $series)
            ->where('sub_group', $subGroup)
            ->when($exceptId, fn ($query) => $query->where('id', '!=', $exceptId))
            ->exists();
    }

    // ── MISE À JOUR D'UNE SECTION ─────────────────────────────────────────
    public function updateSection(Request $request, Section $section)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'language' => ['required', 'in:fr,en'],
        ]);

        $section->update([
            'name'     => $request->name,
            'language' => $request->language,
        ]);

        return back()->with('success',
            "Section « {$section->name} » mise à jour.");
    }
}
