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
use Illuminate\Http\Request;

class ClassManagementController extends Controller
{
    // ── LISTE ─────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        // Année sélectionnée (active par défaut)
        $selectedYearId = $request->input('year_id');
        $activeYear     = AcademicYear::active();
        $selectedYear   = $selectedYearId
            ? AcademicYear::find($selectedYearId)
            : $activeYear;

        $years    = AcademicYear::orderByDesc('start_date')->get();
        $sections = Section::with([
            'levels' => fn($q) => $q->orderBy('order_index'),
        ])->get();

        // Classes de l'année sélectionnée, organisées par section
        $classGroups = collect();
        if ($selectedYear) {
            $classGroups = ClassGroup::where('academic_year_id', $selectedYear->id)
                ->with(['level.section', 'titularStaff', 'studentEnrollments'])
                ->withCount([
                    'studentEnrollments' => fn($q) =>
                        $q->where('status', 'active'),
                    'classSubjects',
                ])
                ->get()
                ->groupBy('level.section.id');
        }

        // Statistiques globales
        $stats = [
            'total_classes'  => $classGroups->flatten()->count(),
            'total_students' => $classGroups->flatten()
                                ->sum('student_enrollments_count'),
            'sections_used'  => $classGroups->keys()->count(),
        ];

        return view('classes.index', compact(
            'sections', 'classGroups', 'selectedYear',
            'years', 'stats', 'activeYear'
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
        // Vérifier unicité du nom dans la même année/niveau
        $exists = ClassGroup::where([
            'academic_year_id' => $request->academic_year_id,
            'level_id'         => $request->level_id,
            'name'             => $request->name,
        ])->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->with('error',
                    'Une classe avec ce nom existe déjà dans ce niveau '
                    . 'pour cette année scolaire.');
        }

        $classGroup = ClassGroup::create($request->validated());

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

        // Vérifier unicité si le nom/niveau a changé
        $exists = ClassGroup::where([
            'academic_year_id' => $classGroup->academic_year_id,
            'level_id'         => $request->level_id,
            'name'             => $request->name,
        ])
        ->where('id', '!=', $classGroup->id)
        ->exists();

        if ($exists) {
            return back()->withInput()
                ->with('error',
                    'Une classe avec ce nom existe déjà dans ce niveau.');
        }

        $old = $classGroup->toArray();
        $classGroup->update($request->validated());
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