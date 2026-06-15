<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\AppreciationScale;
use App\Models\AuditLog;
use App\Models\BulletinReport;
use App\Models\BulletinSubjectDetail;
use App\Models\ClassGroup;
use App\Models\Grade;
use App\Models\GradeLock;
use App\Models\SchoolSetting;
use App\Models\SchoolPhone;
use App\Models\Section;
use App\Models\Sequence;
use App\Models\StudentEnrollment;
use App\Models\Trimester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BulletinController extends Controller
{
    // ── INDEX : Sélection Classe + Séquence ──────────────────────────────
    public function index(Request $request)
    {
        $activeYear = AcademicYear::active();

        $sequences = collect();
        $sections  = collect();

        if ($activeYear) {
            $sequences = Sequence::where('academic_year_id', $activeYear->id)
                ->with('trimester')
                ->orderBy('number')
                ->get();

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

        // Stats des bulletins générés par classe/séquence
        $generatedStats = BulletinReport::where('type', 'sequence')
            ->selectRaw('sequence_id, student_enrollment_id, COUNT(*) as cnt')
            ->groupBy('sequence_id', 'student_enrollment_id')
            ->get()
            ->groupBy('sequence_id');

        // Nombre de bulletins générés par (class_group_id, sequence_id)
        $bulletinCounts = BulletinReport::selectRaw(
            'bulletin_reports.sequence_id,
             student_enrollments.class_group_id,
             COUNT(*) as cnt'
        )
        ->join('student_enrollments',
            'student_enrollments.id', '=', 'bulletin_reports.student_enrollment_id')
        ->where('bulletin_reports.type', 'sequence')
        ->groupBy('bulletin_reports.sequence_id', 'student_enrollments.class_group_id')
        ->get()
        ->keyBy(fn($row) => $row->class_group_id . '_' . $row->sequence_id);

        return view('bulletins.index', compact(
            'activeYear', 'sequences', 'sections', 'bulletinCounts'
        ));
    }

    // ── GÉNÉRATION DES BULLETINS D'UNE CLASSE ────────────────────────────
    public function generate(Request $request)
    {
        $request->validate([
            'class_group_id' => 'required|exists:class_groups,id',
            'sequence_id'    => 'required|exists:sequences,id',
        ]);

        $classGroup = ClassGroup::with([
            'level.section',
            'academicYear',
            'classSubjects' => fn($q) =>
                $q->where('is_active', true)
                  ->with('subject')
                  ->orderBy('subject_id'),
        ])->findOrFail($request->class_group_id);

        $sequence = Sequence::with('trimester')->findOrFail($request->sequence_id);

        // Vérifier verrou
        $isLocked = GradeLock::where([
            'class_group_id' => $classGroup->id,
            'sequence_id'    => $sequence->id,
            'is_locked'      => true,
        ])->exists();

        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();
        if (!$isLocked && !$authUser->hasAnyRole(['super-admin', 'directeur', 'censeur'])) {
            return back()->with('error',
                'Les notes doivent être verrouillées avant de générer les bulletins.');
        }

        // Élèves actifs de la classe
        $enrollments = StudentEnrollment::where([
            'class_group_id'   => $classGroup->id,
            'academic_year_id' => $classGroup->academic_year_id,
            'status'           => 'active',
        ])->with('student')->get();

        // Toutes les notes de la séquence pour cette classe
        $allGrades = Grade::whereIn(
            'student_enrollment_id', $enrollments->pluck('id')
        )->where('sequence_id', $sequence->id)
         ->get()
         ->keyBy(fn($g) => $g->student_enrollment_id . '_' . $g->class_subject_id);

        // Calcul des moyennes de tous les élèves
        $averages = [];
        foreach ($enrollments as $enrollment) {
            $totalPoints = 0;
            $totalCoef   = 0;

            foreach ($classGroup->classSubjects as $cs) {
                $key   = $enrollment->id . '_' . $cs->id;
                $grade = $allGrades->get($key);
                if ($grade && $grade->grade !== null && !$grade->is_absent) {
                    $totalPoints += $grade->grade * $cs->coefficient;
                    $totalCoef   += $cs->coefficient;
                } elseif ($totalCoef === 0 || $cs->coefficient > 0) {
                    // Absence = 0 au sens du calcul
                    if ($grade && $grade->is_absent) {
                        $totalPoints += 0 * $cs->coefficient;
                        $totalCoef   += $cs->coefficient;
                    }
                }
            }

            $avg = $totalCoef > 0 ? round($totalPoints / $totalCoef, 2) : null;
            $averages[$enrollment->id] = $avg;
        }

        // Calcul des rangs
        $sortedAvgs = collect($averages)->filter()->sortDesc()->values();
        $classAverage  = $sortedAvgs->count() > 0 ? round($sortedAvgs->avg(), 2) : null;
        $highestAvg    = $sortedAvgs->first();
        $lowestAvg     = $sortedAvgs->last();
        $classSize     = $enrollments->count();

        $ranks = [];
        $rank  = 1;
        $prev  = null;
        $sameRankCount = 1;
        foreach ($sortedAvgs as $idx => $avg) {
            if ($prev !== null && $avg < $prev) {
                $rank += $sameRankCount;
                $sameRankCount = 1;
            } elseif ($prev !== null && $avg === $prev) {
                $sameRankCount++;
            }
            foreach ($averages as $enrollId => $a) {
                if ($a == $avg && !isset($ranks[$enrollId])) {
                    $ranks[$enrollId] = $rank;
                }
            }
            $prev = $avg;
        }

        DB::transaction(function () use (
            $enrollments, $classGroup, $sequence,
            $allGrades, $averages, $ranks,
            $classAverage, $highestAvg, $lowestAvg, $classSize
        ) {
            $generatedBy = Auth::id();

            foreach ($enrollments as $enrollment) {
                $avg  = $averages[$enrollment->id] ?? null;
                $rank = $ranks[$enrollment->id] ?? null;

                // Absences pour la séquence (approximation : total de l'année)
                $absences = $enrollment->absences()
                    ->where('is_justified', false)
                    ->sum('hours');
                $justifiedAbs = $enrollment->absences()
                    ->where('is_justified', true)
                    ->sum('hours');

                // Appréciation générale
                $appreciation = $avg !== null
                    ? AppreciationScale::forGrade((float) $avg)
                    : null;

                // Créer ou mettre à jour le bulletin
                $bulletin = BulletinReport::updateOrCreate(
                    [
                        'student_enrollment_id' => $enrollment->id,
                        'sequence_id'           => $sequence->id,
                        'type'                  => 'sequence',
                    ],
                    [
                        'trimester_id'          => $sequence->trimester_id,
                        'academic_year_id'      => $classGroup->academic_year_id,
                        'average_general'       => $avg,
                        'rank'                  => $rank,
                        'class_size'            => $classSize,
                        'class_average'         => $classAverage,
                        'highest_average'       => $highestAvg,
                        'lowest_average'        => $lowestAvg,
                        'unjustified_absences'  => $absences,
                        'justified_absences'    => $justifiedAbs,
                        'general_observation'   => $appreciation?->label_fr,
                        'generated_at'          => now(),
                        'generated_by'          => $generatedBy,
                    ]
                );

                // Supprimer les anciens détails par matière
                $bulletin->subjectDetails()->delete();

                // Recréer les détails par matière
                $subjectOrder = 1;
                foreach ($classGroup->classSubjects as $cs) {
                    $key   = $enrollment->id . '_' . $cs->id;
                    $grade = $allGrades->get($key);

                    $subjectAvg = null;
                    if ($grade && !$grade->is_absent && $grade->grade !== null) {
                        $subjectAvg = (float) $grade->grade;
                    }

                    // Chercher le nom de l'enseignant assigné à cette matière
                    $teacher = $cs->teacherAssignments()
                        ->where('academic_year_id', $classGroup->academic_year_id)
                        ->with('staff')
                        ->first()?->staff;

                    $subjectAppreciation = $subjectAvg !== null
                        ? AppreciationScale::forGrade($subjectAvg)
                        : null;

                    $total = $subjectAvg !== null
                        ? round($subjectAvg * $cs->coefficient, 2)
                        : null;

                    BulletinSubjectDetail::create([
                        'bulletin_report_id' => $bulletin->id,
                        'class_subject_id'   => $cs->id,
                        'subject_order'      => $subjectOrder++,
                        'coefficient'        => $cs->coefficient,
                        'teacher_name'       => $teacher?->full_name,
                        'seq_grade'          => $subjectAvg,  // bulletin séquentiel
                        'average'            => $subjectAvg,
                        'total'              => $total,
                        'appreciation'       => $subjectAppreciation?->code,
                    ]);
                }
            }
        });

        AuditLog::log('bulletins_generated', $classGroup);

        return redirect()
            ->route('bulletins.class', [
                'classGroup' => $classGroup->id,
                'sequence'   => $sequence->id,
            ])
            ->with('success',
                "Bulletins générés pour {$classGroup->full_name} — {$sequence->label}. "
                . count($enrollments) . " bulletin(s) créé(s).");
    }

    // ── LISTE DES BULLETINS D'UNE CLASSE / SÉQUENCE ──────────────────────
    public function classIndex(ClassGroup $classGroup, Sequence $sequence)
    {
        $classGroup->load([
            'level.section',
            'academicYear',
        ]);

        $bulletins = BulletinReport::where([
            'sequence_id' => $sequence->id,
            'type'        => 'sequence',
        ])->whereHas('studentEnrollment', fn($q) =>
            $q->where('class_group_id', $classGroup->id)
        )
        ->with([
            'studentEnrollment.student',
            'distinction',
            'councilDecision',
        ])
        ->orderBy('rank')
        ->get();

        return view('bulletins.class-index', compact(
            'classGroup', 'sequence', 'bulletins'
        ));
    }

    // ── APERÇU / IMPRESSION D'UN BULLETIN ────────────────────────────────
    public function show(BulletinReport $bulletin)
    {
        $bulletin->load([
            'studentEnrollment.student',
            'studentEnrollment.classGroup.level.section',
            'studentEnrollment.classGroup.academicYear',
            'studentEnrollment.classGroup.classSubjects.subject',
            'sequence.trimester',
            'subjectDetails.classSubject.subject',
            'distinction',
            'councilDecision',
            'generatedBy',
        ]);

        $school = SchoolSetting::instance();
        $phones = SchoolPhone::orderByDesc('is_primary')->orderBy('id')->get();

        // Regrouper les matières par catégorie
        $subjectsByCategory = $bulletin->subjectDetails
            ->groupBy(fn($d) =>
                $d->classSubject?->subject?->subjectCategory?->name ?? 'Général'
            );

        return view('bulletins.show', compact(
            'bulletin', 'school', 'phones', 'subjectsByCategory'
        ));
    }

    // ── IMPRESSION DE TOUS LES BULLETINS D'UNE CLASSE ────────────────────
    public function printAll(ClassGroup $classGroup, Sequence $sequence)
    {
        $bulletins = BulletinReport::where([
            'sequence_id' => $sequence->id,
            'type'        => 'sequence',
        ])->whereHas('studentEnrollment', fn($q) =>
            $q->where('class_group_id', $classGroup->id)
        )
        ->with([
            'studentEnrollment.student',
            'studentEnrollment.classGroup.level.section',
            'studentEnrollment.classGroup.academicYear',
            'sequence.trimester',
            'subjectDetails.classSubject.subject',
            'distinction',
            'councilDecision',
        ])
        ->orderBy('rank')
        ->get();

        $school = SchoolSetting::instance();
        $phones = SchoolPhone::orderByDesc('is_primary')->orderBy('id')->get();

        $classGroup->load(['level.section', 'academicYear']);

        return view('bulletins.print-all', compact(
            'bulletins', 'school', 'phones', 'classGroup', 'sequence'
        ));
    }
}
