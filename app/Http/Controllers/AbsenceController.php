<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use App\Models\AcademicYear;
use App\Models\AuditLog;
use App\Models\ClassGroup;
use App\Models\ClassSubject;
use App\Models\Section;
use App\Models\StudentEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AbsenceController extends Controller
{
    // ── VUE GLOBALE DES ABSENCES ─────────────────────────────────────────
    public function index(Request $request)
    {
        $activeYear = AcademicYear::active();

        $sections = collect();
        if ($activeYear) {
            $sections = Section::with([
                'levels.classGroups' => fn($q) =>
                    $q->where('academic_year_id', $activeYear->id)
                      ->withCount([
                          'studentEnrollments as enrolled' => fn($q2) =>
                              $q2->where('status', 'active'),
                      ])
                      ->orderBy('name'),
            ])->orderBy('id')->get();
        }

        // Stats globales des absences
        $totalAbsenceHours = Absence::when($activeYear, fn($q) =>
            $q->whereHas('studentEnrollment', fn($q2) =>
                $q2->where('academic_year_id', $activeYear->id)
            )
        )->sum('hours');

        $unjustifiedHours = Absence::when($activeYear, fn($q) =>
            $q->whereHas('studentEnrollment', fn($q2) =>
                $q2->where('academic_year_id', $activeYear->id)
            )
        )->where('is_justified', false)->sum('hours');

        $justifiedHours = Absence::when($activeYear, fn($q) =>
            $q->whereHas('studentEnrollment', fn($q2) =>
                $q2->where('academic_year_id', $activeYear->id)
            )
        )->where('is_justified', true)->sum('hours');

        // Absences récentes (toutes classes)
        $recentAbsences = Absence::with([
            'studentEnrollment.student',
            'studentEnrollment.classGroup.level.section',
            'classSubject.subject',
            'recordedBy',
        ])
        ->when($activeYear, fn($q) =>
            $q->whereHas('studentEnrollment', fn($q2) =>
                $q2->where('academic_year_id', $activeYear->id)
            )
        )
        ->orderByDesc('absence_date')
        ->orderByDesc('created_at')
        ->take(15)
        ->get();

        // Top absentéistes
        $topAbsentees = StudentEnrollment::where('status', 'active')
            ->when($activeYear, fn($q) =>
                $q->where('academic_year_id', $activeYear->id)
            )
            ->withSum('absences as total_hours', 'hours')
            ->withSum(['absences as unjustified_hours' => fn($q) =>
                $q->where('is_justified', false)
            ], 'hours')
            ->having('total_hours', '>', 0)
            ->orderByDesc('total_hours')
            ->with('student', 'classGroup.level.section')
            ->take(10)
            ->get();

        return view('absences.index', compact(
            'activeYear', 'sections',
            'totalAbsenceHours', 'unjustifiedHours', 'justifiedHours',
            'recentAbsences', 'topAbsentees'
        ));
    }

    // ── SAISIE DES ABSENCES D'UNE CLASSE ─────────────────────────────────
    public function classView(ClassGroup $classGroup)
    {
        $classGroup->load([
            'level.section',
            'academicYear',
            'classSubjects' => fn($q) =>
                $q->where('is_active', true)->with('subject')->orderBy('subject_id'),
        ]);

        $enrollments = StudentEnrollment::where([
            'class_group_id'   => $classGroup->id,
            'academic_year_id' => $classGroup->academic_year_id,
            'status'           => 'active',
        ])->with([
            'student',
            'absences' => fn($q) => $q->orderByDesc('absence_date'),
        ])->get()->sortBy('student.last_name');

        // Absences récentes de la classe (30 derniers jours)
        $recentAbsences = Absence::whereHas('studentEnrollment', fn($q) =>
            $q->where('class_group_id', $classGroup->id)
        )
        ->with([
            'studentEnrollment.student',
            'classSubject.subject',
            'recordedBy',
        ])
        ->orderByDesc('absence_date')
        ->take(50)
        ->get();

        return view('absences.class', compact(
            'classGroup', 'enrollments', 'recentAbsences'
        ));
    }

    // ── ENREGISTREMENT D'UNE ABSENCE ─────────────────────────────────────
    public function store(Request $request, ClassGroup $classGroup)
    {
        $validated = $request->validate([
            'student_enrollment_id' => 'required|exists:student_enrollments,id',
            'absence_date'          => 'required|date',
            'period'                => 'nullable|string|max:50',
            'class_subject_id'      => 'nullable|exists:class_subjects,id',
            'hours'                 => 'required|numeric|min:0.5|max:8',
            'justification'         => 'nullable|string|max:500',
            'is_justified'          => 'boolean',
        ]);

        $validated['recorded_by'] = Auth::id();
        $validated['is_justified'] = $request->boolean('is_justified');

        $absence = Absence::create($validated);
        AuditLog::log('absence_recorded', $absence);

        return back()->with('success', 'Absence enregistrée avec succès.');
    }

    // ── JUSTIFIER / DÉ-JUSTIFIER UNE ABSENCE ─────────────────────────────
    public function justify(Request $request, Absence $absence)
    {
        $validated = $request->validate([
            'is_justified'  => 'required|boolean',
            'justification' => 'nullable|string|max:500',
        ]);

        $absence->update($validated);
        AuditLog::log('absence_updated', $absence);

        $msg = $validated['is_justified']
            ? 'Absence justifiée.'
            : 'Justification retirée.';

        return back()->with('success', $msg);
    }

    // ── SUPPRIMER UNE ABSENCE ─────────────────────────────────────────────
    public function destroy(Absence $absence)
    {
        AuditLog::log('absence_deleted', $absence);
        $absence->delete();

        return back()->with('success', 'Absence supprimée.');
    }
}
