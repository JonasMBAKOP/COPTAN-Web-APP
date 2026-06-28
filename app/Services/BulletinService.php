<?php

namespace App\Services;

use App\Models\AppreciationScale;
use App\Models\BulletinReport;
use App\Models\BulletinSubjectDetail;
use App\Models\ClassGroup;
use App\Models\Grade;
use App\Models\GradeLock;
use App\Models\Sequence;
use App\Models\StudentEnrollment;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BulletinService
{
    public const TYPE_SEQUENTIAL = 'sequential';

    public function isSequenceLocked(ClassGroup $classGroup, Sequence $sequence): bool
    {
        return GradeLock::where([
            'class_group_id' => $classGroup->id,
            'sequence_id'    => $sequence->id,
            'is_locked'      => true,
        ])->exists();
    }

    /**
     * @return array{count: int, class_average: ?float}
     */
    public function generateSequential(
        ClassGroup $classGroup,
        Sequence $sequence,
        int $generatedBy
    ): array {
        $classGroup->load([
            'level.section',
            'academicYear',
            'classSubjects' => fn ($q) =>
                $q->where('is_active', true)
                  ->with('subject.category')
                  ->orderBy('subject_id'),
        ]);

        $enrollments = StudentEnrollment::where([
            'class_group_id'   => $classGroup->id,
            'academic_year_id' => $classGroup->academic_year_id,
            'status'           => 'active',
        ])->with('student')->get();

        $allGrades = Grade::whereIn(
            'student_enrollment_id', $enrollments->pluck('id')
        )->where('sequence_id', $sequence->id)
         ->get()
         ->keyBy(fn ($g) => $g->student_enrollment_id . '_' . $g->class_subject_id);

        $averages     = $this->computeGeneralAverages($enrollments, $classGroup->classSubjects, $allGrades);
        $ranks        = $this->computeRanks($averages);
        $subjectRanks = $this->computeSubjectRanks($enrollments, $classGroup->classSubjects, $allGrades);

        $validAvgs    = collect($averages)->filter(fn ($v) => $v !== null);
        $classAverage = $validAvgs->count() > 0 ? round($validAvgs->avg(), 2) : null;
        $highestAvg   = $validAvgs->max();
        $lowestAvg    = $validAvgs->min();
        $classSize    = $enrollments->count();

        // ── Pré-charger toutes les échelles d'appréciation (évite N requêtes en boucle) ──
        $appreciationScales = AppreciationScale::orderBy('min_grade')->get();

        DB::transaction(function () use (
            $enrollments, $classGroup, $sequence, $allGrades,
            $averages, $ranks, $subjectRanks,
            $classAverage, $highestAvg, $lowestAvg, $classSize,
            $generatedBy, $appreciationScales
        ) {
            foreach ($enrollments as $enrollment) {
                $avg  = $averages[$enrollment->id] ?? null;
                $rank = $ranks[$enrollment->id] ?? null;

                [$unjustified, $justified] = $this->absenceHours($enrollment, $sequence);

                // ── Appréciation générale : on stocke le label complet dans general_observation ──
                // bulletin_reports.general_observation est de type TEXT → OK pour le label long
                $appreciation = $avg !== null
                    ? $this->findAppreciation($appreciationScales, (float) $avg)
                    : null;

                $bulletin = BulletinReport::updateOrCreate(
                    [
                        'student_enrollment_id' => $enrollment->id,
                        'sequence_id'           => $sequence->id,
                        'type'                  => self::TYPE_SEQUENTIAL,
                    ],
                    [
                        'trimester_id'         => $sequence->trimester_id,
                        'academic_year_id'     => $classGroup->academic_year_id,
                        'average_general'      => $avg,
                        'rank'                 => $rank,
                        'class_size'           => $classSize,
                        'class_average'        => $classAverage,
                        'highest_average'      => $highestAvg,
                        'lowest_average'       => $lowestAvg,
                        'unjustified_absences' => $unjustified,
                        'justified_absences'   => $justified,
                        // On stocke le LABEL complet ici (colonne TEXT)
                        'general_observation'  => $appreciation?->label_fr,
                        'generated_at'         => now(),
                        'generated_by'         => $generatedBy,
                    ]
                );

                $bulletin->subjectDetails()->delete();

                // ── Pré-charger les enseignants de la classe en une seule requête ──
                $teacherMap = $this->loadTeacherMap($classGroup);

                $subjectOrder = 1;
                foreach ($classGroup->classSubjects as $cs) {
                    $key   = $enrollment->id . '_' . $cs->id;
                    $grade = $allGrades->get($key);

                    $subjectAvg = null;
                    if ($grade && ! $grade->is_absent && $grade->grade !== null) {
                        $subjectAvg = (float) $grade->grade;
                    }

                    // Appréciation matière : on stocke le CODE court (ex: 'CA')
                    // La colonne appreciation est VARCHAR(20) → suffit pour les codes
                    $subjectAppreciation = $subjectAvg !== null
                        ? $this->findAppreciation($appreciationScales, $subjectAvg)
                        : null;

                    $total = $subjectAvg !== null
                        ? round($subjectAvg * $cs->coefficient, 2)
                        : null;

                    BulletinSubjectDetail::create([
                        'bulletin_report_id' => $bulletin->id,
                        'class_subject_id'   => $cs->id,
                        'subject_order'      => $subjectOrder++,
                        'coefficient'        => $cs->coefficient,
                        // Nom de l'enseignant depuis la map pré-chargée
                        'teacher_name'       => $teacherMap[$cs->id] ?? null,
                        'seq_grade'          => $subjectAvg,
                        'average'            => $subjectAvg,
                        'total'              => $total,
                        'rank_in_subject'    => $subjectRanks[$key] ?? null,
                        // On stocke le CODE court (CNA, CMA, CA, CBA, CTBA)
                        // VARCHAR(20) est suffisant, pas de troncature possible
                        'appreciation'       => $subjectAppreciation?->code,
                    ]);
                }
            }
        });

        return [
            'count'         => $enrollments->count(),
            'class_average' => $classAverage,
        ];
    }

    /**
     * Trouve l'appréciation correspondant à une note depuis une collection pré-chargée.
     * Évite une requête SQL par note (anti-N+1).
     */
    private function findAppreciation(Collection $scales, float $grade): ?AppreciationScale
    {
        return $scales->first(
            fn (AppreciationScale $s) => $grade >= (float) $s->min_grade
                                      && $grade <= (float) $s->max_grade
        );
    }

    /**
     * Pré-charge une map [class_subject_id => teacher_full_name] pour la classe.
     * Permet d'éviter N requêtes en boucle sur les matières.
     *
     * @return array<int, string|null>
     */
    private function loadTeacherMap(ClassGroup $classGroup): array
    {
        $map = [];
        foreach ($classGroup->classSubjects as $cs) {
            $staff = $cs->teacherAssignments()
                ->where('academic_year_id', $classGroup->academic_year_id)
                ->with('staff')
                ->first()?->staff;
            $map[$cs->id] = $staff?->full_name;
        }
        return $map;
    }

    /** @return array<int, ?float> */
    private function computeGeneralAverages(
        Collection $enrollments,
        Collection $classSubjects,
        Collection $allGrades
    ): array {
        $averages = [];

        foreach ($enrollments as $enrollment) {
            $totalPoints = 0;
            $totalCoef   = 0;

            foreach ($classSubjects as $cs) {
                $key   = $enrollment->id . '_' . $cs->id;
                $grade = $allGrades->get($key);

                if ($grade && $grade->grade !== null && ! $grade->is_absent) {
                    $totalPoints += $grade->grade * $cs->coefficient;
                    $totalCoef   += $cs->coefficient;
                } elseif ($grade && $grade->is_absent) {
                    $totalCoef += $cs->coefficient;
                }
            }

            $averages[$enrollment->id] = $totalCoef > 0
                ? round($totalPoints / $totalCoef, 2)
                : null;
        }

        return $averages;
    }

    /** @param array<int, ?float> $averages */
    /** @return array<int, int> */
    private function computeRanks(array $averages): array
    {
        $sorted = collect($averages)->filter(fn ($v) => $v !== null)->sortDesc();
        $ranks  = [];
        $rank   = 1;
        $prev   = null;
        $same   = 1;

        foreach ($sorted as $avg) {
            if ($prev !== null && $avg < $prev) {
                $rank += $same;
                $same  = 1;
            } elseif ($prev !== null && $avg === $prev) {
                $same++;
            }

            foreach ($averages as $enrollId => $a) {
                if ($a == $avg && ! isset($ranks[$enrollId])) {
                    $ranks[$enrollId] = $rank;
                }
            }

            $prev = $avg;
        }

        return $ranks;
    }

    /** @return array<string, int> */
    private function computeSubjectRanks(
        Collection $enrollments,
        Collection $classSubjects,
        Collection $allGrades
    ): array {
        $ranks = [];

        foreach ($classSubjects as $cs) {
            $scores = [];

            foreach ($enrollments as $enrollment) {
                $key   = $enrollment->id . '_' . $cs->id;
                $grade = $allGrades->get($key);

                if ($grade && ! $grade->is_absent && $grade->grade !== null) {
                    $scores[$enrollment->id] = (float) $grade->grade;
                }
            }

            $sorted = collect($scores)->sortDesc();
            $rank   = 1;
            $prev   = null;
            $same   = 1;

            foreach ($sorted as $enrollmentId => $score) {
                if ($prev !== null && $score < $prev) {
                    $rank += $same;
                    $same  = 1;
                } elseif ($prev !== null && $score === $prev) {
                    $same++;
                }

                $ranks[$enrollmentId . '_' . $cs->id] = $rank;
                $prev = $score;
            }
        }

        return $ranks;
    }

    /** @return array{0: float, 1: float} */
    private function absenceHours(StudentEnrollment $enrollment, Sequence $sequence): array
    {
        $makeQuery = function () use ($enrollment, $sequence) {
            $query = $enrollment->absences();

            if ($sequence->start_date && $sequence->end_date) {
                $query->whereBetween('absence_date', [
                    $sequence->start_date,
                    $sequence->end_date,
                ]);
            }

            return $query;
        };

        return [
            (float) $makeQuery()->where('is_justified', false)->sum('hours'),
            (float) $makeQuery()->where('is_justified', true)->sum('hours'),
        ];
    }
}
