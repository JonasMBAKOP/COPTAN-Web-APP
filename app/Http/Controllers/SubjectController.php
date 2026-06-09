<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssignSubjectsRequest;
use App\Http\Requests\StoreSubjectRequest;
use App\Http\Requests\UpdateSubjectRequest;
use App\Models\AuditLog;
use App\Models\ClassGroup;
use App\Models\ClassSubject;
// use App\Models\Staff;
use App\Models\Subject;
use App\Models\SubjectCategory;
use App\Models\TeacherAssignment;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    // ── CATALOGUE ─────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        // $query = Subject::with('category')
        //                 ->withCount('classSubjects');
        $query = Subject::with([
            'category',
            'classSubjects.classGroup.level.section',
            'classSubjects.teacherAssignments.staff',
        ])->withCount('classSubjects');

        // if ($request->filled('category')) {
        //     $query->where('subject_category_id', $request->category);
        // }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $query->where(fn($q) =>
                $q->where('name_fr', 'like', "%{$request->search}%")
                  ->orWhere('name_en', 'like', "%{$request->search}%")
                  ->orWhere('code',    'like', "%{$request->search}%")
            );
        }

        $subjects   = $query->orderBy('code')->paginate(20)
                            ->withQueryString();
        $categories = SubjectCategory::withCount('subjects')
                                     ->orderBy('order_index')->get();

        // Données par section pour l'onglet "Par Section"
        $sections    = \App\Models\Section::orderBy('id')->get();
        $allSubjects = Subject::with([
            'classSubjects.classGroup.level.section',
        ])->orderBy('name_fr')->get();
    
        $sectionData = $sections->map(function($section) use ($allSubjects) {
            $subs = $allSubjects->filter(function($subject) use ($section) {
                return $subject->classSubjects->some(
                    fn($cs) => $cs->classGroup?->level?->section_id === $section->id
                );
            })->values();
            return ['section' => $section, 'subjects' => $subs];
        });

        // Dernière mise à jour
        $lastAudit = AuditLog::where('model_type', 'Subject')
            ->orderByDesc('created_at')
            ->with('user')
            ->first();
            
        $stats = [
            'total'     => Subject::count(),
            'general'   => Subject::where('type', 'general')->count(),
            'technical' => Subject::where('type', 'technical')->count(),
            'language'  => Subject::where('type', 'language')->count(),
            'sport'     => Subject::where('type', 'sport')->count(),
            'other'     => Subject::where('type', 'other')->count(),
        ];

        return view('subjects.index', compact(
            'subjects', 'categories', 'stats',
            'sections', 'sectionData', 'lastAudit'
        ));
    }

    // ── CRÉATION ──────────────────────────────────────────────────────────
    public function create()
    {
        $categories = SubjectCategory::orderBy('order_index')->get();
        return view('subjects.create', compact('categories'));
    }

    // ── ENREGISTREMENT ────────────────────────────────────────────────────
    public function store(StoreSubjectRequest $request)
    {
        $subject = Subject::create($request->validated());
        AuditLog::log('created', $subject, [], $subject->toArray());

        return redirect()
            ->route('subjects.index')
            ->with('success',
                "Matière « {$subject->name_fr} » créée avec succès.");
    }

    // ── MODIFICATION ──────────────────────────────────────────────────────
    public function edit(Subject $subject)
    {
        $categories = SubjectCategory::orderBy('order_index')->get();
        return view('subjects.edit', compact('subject', 'categories'));
    }

    // ── MISE À JOUR ───────────────────────────────────────────────────────
    public function update(UpdateSubjectRequest $request, Subject $subject)
    {
        $old = $subject->toArray();
        $subject->update($request->validated());
        AuditLog::log('updated', $subject, $old, $subject->toArray());

        return redirect()
            ->route('subjects.index')
            ->with('success',
                "Matière « {$subject->name_fr} » mise à jour.");
    }

    // ── SUPPRESSION ───────────────────────────────────────────────────────
    public function destroy(Subject $subject)
    {
        // Vérifier qu'aucun cours n'y est lié
        $usedInClasses = $subject->classSubjects()
                                 ->whereHas('grades')
                                 ->count();

        if ($usedInClasses > 0) {
            return back()->with('error',
                "Impossible de supprimer « {$subject->name_fr} » : "
                . "des notes y sont associées.");
        }

        $name = $subject->name_fr;

        // Supprimer les attributions sans notes
        $subject->classSubjects()
                ->whereDoesntHave('grades')
                ->delete();

        $subject->delete();
        AuditLog::log('deleted', null, ['name' => $name], []);

        return redirect()
            ->route('subjects.index')
            ->with('success', "Matière « {$name} » supprimée.");
    }

    // ── ATTRIBUTION D'UNE MATIÈRE À UNE CLASSE ────────────────────────────
    public function assign(ClassGroup $classGroup)
    {
        if ($classGroup->academicYear->isClosed()) {
            return redirect()
                ->route('classes.show', $classGroup)
                ->with('error', 'Année clôturée — modification impossible.');
        }

        $classGroup->load([
            'level.section', 'academicYear',
            'classSubjects' => fn($q) => $q->with([
                'subject.category',
                'teacherAssignments' => fn($q2) =>
                    $q2->where('academic_year_id', $classGroup->academic_year_id)
                    ->with('staff'),
            ])->orderBy('subject_id'),
        ]);

        // $categories = SubjectCategory::with([
        //     'subjects' => fn($q) => $q->orderBy('name_fr'),
        // ])->orderBy('order_index')->get();

        // // Matières déjà assignées
        // $assigned = $classGroup->classSubjects->keyBy('subject_id');
        
        // Matières disponibles (pas encore assignées)
        $assignedIds = $classGroup->classSubjects->pluck('subject_id');
        $availableSubjects = Subject::with('category')
            ->whereNotIn('id', $assignedIds)
            ->orderBy('name_fr')
            ->get();

        // Autres classes de la même année (pour copie)
        $otherClasses = ClassGroup::where('academic_year_id',
                                        $classGroup->academic_year_id)
            ->where('id', '!=', $classGroup->id)
            ->with(['level.section', 'classSubjects'])
            // ->with('level.section')
            ->orderBy('name')
            ->get();

        // // Enseignants déjà assignés (class_subject_id → staff_id)
        // $teacherAssignments = \App\Models\TeacherAssignment::where(
        //     'academic_year_id', $classGroup->academic_year_id
        // )
        // ->whereIn(
        //     'class_subject_id',
        //     $classGroup->classSubjects->pluck('id')
        // )
        // ->get()
        // ->keyBy('class_subject_id');

        // Sections + classes pour les filtres
        $sections       = \App\Models\Section::orderBy('id')->get();
        $classesBySection = ClassGroup::where('academic_year_id',
                                            $classGroup->academic_year_id)
            ->with('level.section')
            ->orderBy('name')
            ->get()
            ->groupBy('level.section_id');

        // Liste des enseignants disponibles
        $teachers = \App\Models\Staff::where('is_active', true)
            ->whereHas('positions', fn($q) =>
                $q->where('position', 'enseignant')
            )
            ->orWhere(fn($q) =>
                $q->where('is_active', true)
                ->whereDoesntHave('positions')
            )
            ->orderBy('last_name')
            ->get();

        $totalCoef = $classGroup->classSubjects
            ->where('is_active', true)->sum('coefficient');
        $totalHrs  = $classGroup->classSubjects
            ->where('is_active', true)->sum('hours_per_week');

        // return view('subjects.assign', compact(
        //     'classGroup', 'categories', 'assigned',
        //     'teacherAssignments', 'teachers',
        //     'totalCoef', 'totalHrs'
        // ));
        return view('subjects.assign', compact(
            'classGroup', 'availableSubjects', 'otherClasses',
            'sections', 'classesBySection',
            'teachers', 'totalCoef', 'totalHrs'
        ));
    }

    // ── ENREGISTREMENT DES ATTRIBUTIONS ──────────────────────────────────
    public function saveAssignment(AssignSubjectsRequest $request,
                                ClassGroup $classGroup)
    {
        if ($classGroup->academicYear->isClosed()) {
            return back()->with('error', 'Année clôturée.');
        }

        // Fusionner les données desktop et mobile
        $submittedSubjectIds = collect($request->input('subjects', []))
            ->pluck('subject_id')
            ->toArray();

        // ── GESTION DES MATIÈRES ──────────────────────────────────────────
        // Supprimer celles sans notes, désactiver celles avec notes
        \App\Models\ClassSubject::where('class_group_id', $classGroup->id)
            ->whereNotIn('subject_id', $submittedSubjectIds)
            ->whereDoesntHave('grades')
            ->delete();

        \App\Models\ClassSubject::where('class_group_id', $classGroup->id)
            ->whereNotIn('subject_id', $submittedSubjectIds)
            ->whereHas('grades')
            ->update(['is_active' => false]);

        // Créer ou mettre à jour
        foreach ($request->input('subjects', []) as $item) {
            \App\Models\ClassSubject::updateOrCreate(
                [
                    'class_group_id' => $classGroup->id,
                    'subject_id'     => $item['subject_id'],
                ],
                [
                    'coefficient'    => $item['coefficient'],
                    'hours_per_week' => $item['hours_per_week'] ?: null,
                    'is_active'      => true,
                ]
            );
        }

        // ── GESTION DES ENSEIGNANTS ───────────────────────────────────────
        // Reconstruire la map subject_id → class_subject_id (inclut les nouvelles)
        $classSubjectMap = \App\Models\ClassSubject::where('class_group_id', $classGroup->id)
            ->pluck('id', 'subject_id');

        foreach ($request->input('teachers', []) as $key => $staffId) {
            // Clés "new_{subject_id}" = matières ajoutées via JS
            if (str_starts_with((string)$key, 'new_')) {
                $subjectId      = (int) substr($key, 4);
                $classSubjectId = $classSubjectMap->get($subjectId);
                if (!$classSubjectId) continue;
            } else {
                $classSubjectId = (int) $key;
            }

            if (empty($staffId)) {
                \App\Models\TeacherAssignment::where([
                    'academic_year_id' => $classGroup->academic_year_id,
                    'class_subject_id' => $classSubjectId,
                ])->delete();
            } else {
                \App\Models\TeacherAssignment::updateOrCreate(
                    [
                        'academic_year_id' => $classGroup->academic_year_id,
                        'class_subject_id' => $classSubjectId,
                    ],
                    ['staff_id' => $staffId]
                );
            }
        }

        AuditLog::log('subjects_assigned', $classGroup);

        return redirect()
            ->route('classes.show', $classGroup)
            ->with('success',
                "Matières de « {$classGroup->full_name} » mises à jour.");
    }

    // ── COPIE DEPUIS UNE AUTRE CLASSE ─────────────────────────────────────
    public function copyFromClass(Request $request, ClassGroup $classGroup)
    {
        $request->validate([
            'source_class_id' => ['required', 'exists:class_groups,id',
                                'different:class_group_id'],
        ]);

        $source = ClassGroup::with('classSubjects.teacherAssignments')
                            ->find($request->source_class_id);

        if (!$source) {
            return back()->with('error', 'Classe source introuvable.');
        }

        $copied = 0;
        foreach ($source->classSubjects->where('is_active', true) as $cs) {
            $exists = ClassSubject::where([
                'class_group_id' => $classGroup->id,
                'subject_id'     => $cs->subject_id,
            ])->exists();

            if (!$exists) {
                $newCs = ClassSubject::create([
                    'class_group_id' => $classGroup->id,
                    'subject_id'     => $cs->subject_id,
                    'coefficient'    => $cs->coefficient,
                    'hours_per_week' => $cs->hours_per_week,
                    'is_active'      => true,
                ]);

                // Copier aussi l'enseignant si demandé
                if ($request->boolean('copy_teachers')) {
                    $ta = $cs->teacherAssignments
                        ->where('academic_year_id', $source->academic_year_id)
                        ->first();
                    if ($ta) {
                        TeacherAssignment::updateOrCreate([
                            'academic_year_id' => $classGroup->academic_year_id,
                            'class_subject_id' => $newCs->id,
                        ], ['staff_id' => $ta->staff_id]);
                    }
                }
                $copied++;
            }
        }

        return back()->with('success',
            "{$copied} matière(s) copiée(s) depuis « {$source->full_name} ».");
    }

    // ── GESTION DES CATÉGORIES ────────────────────────────────────────────
    public function storeCategory(Request $request)
    {
        $request->validate([
            'name_fr'     => ['required', 'string', 'max:100'],
            'name_en'     => ['nullable', 'string', 'max:100'],
            'order_index' => ['required', 'integer', 'min:1'],
        ]);

        SubjectCategory::create($request->only(
            'name_fr', 'name_en', 'order_index'
        ));

        return back()->with('success', 'Catégorie ajoutée.');
    }

    public function updateCategory(Request $request,
                                   SubjectCategory $category)
    {
        $request->validate([
            'name_fr'     => ['required', 'string', 'max:100'],
            'name_en'     => ['nullable', 'string', 'max:100'],
            'order_index' => ['required', 'integer', 'min:1'],
        ]);

        $category->update($request->only(
            'name_fr', 'name_en', 'order_index'
        ));

        return back()->with('success',
            "Catégorie « {$category->name_fr} » mise à jour.");
    }

    public function destroyCategory(SubjectCategory $category)
    {
        if ($category->subjects()->count() > 0) {
            return back()->with('error',
                'Impossible de supprimer cette catégorie : '
                . 'elle contient des matières.');
        }

        $name = $category->name_fr;
        $category->delete();

        return back()->with('success',
            "Catégorie « {$name} » supprimée.");
    }
}