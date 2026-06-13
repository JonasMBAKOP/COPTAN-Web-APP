<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\ClassGroup;
use App\Models\Level;
use App\Models\Student;
use App\Models\StudentEnrollment;
use Illuminate\Support\Collection;

class EnrollmentService
{
    public function activeYear(): ?AcademicYear
    {
        return AcademicYear::active();
    }

    public function activeEnrollmentForYear(Student $student, ?int $yearId = null): ?StudentEnrollment
    {
        $yearId ??= $this->activeYear()?->id;

        if (! $yearId) {
            return null;
        }

        return $student->enrollments()
            ->where('academic_year_id', $yearId)
            ->where('status', StudentEnrollment::STATUS_ACTIVE)
            ->with(['classGroup.level.section', 'academicYear'])
            ->first();
    }

    /**
     * Dernière inscription antérieure à l'année cible (pour le renouvellement).
     */
    public function previousEnrollmentForRenewal(
        Student $student,
        AcademicYear $targetYear
    ): ?StudentEnrollment {
        return $student->enrollments()
            ->with(['classGroup.level.section', 'academicYear'])
            ->whereHas('academicYear', fn ($q) =>
                $q->where('start_date', '<', $targetYear->start_date)
            )
            ->get()
            ->sortByDesc(fn (StudentEnrollment $e) => $e->academicYear->start_date)
            ->first();
    }

    /**
     * Clôture toutes les inscriptions actives d'une année scolaire.
     * Appelé à la fermeture de l'année ou lors de l'activation d'une nouvelle.
     */
    public function finalizeYearEnrollments(AcademicYear $year): int
    {
        return StudentEnrollment::where('academic_year_id', $year->id)
            ->where('status', StudentEnrollment::STATUS_ACTIVE)
            ->update(['status' => StudentEnrollment::STATUS_INACTIVE]);
    }

    public function reactivateYearEnrollments(AcademicYear $year): int
    {
        return StudentEnrollment::where('academic_year_id', $year->id)
            ->where('status', StudentEnrollment::STATUS_INACTIVE)
            ->update(['status' => StudentEnrollment::STATUS_ACTIVE]);
    }

    public function hasActiveEnrollment(Student $student, int $yearId): bool
    {
        return StudentEnrollment::where([
            'student_id'       => $student->id,
            'academic_year_id' => $yearId,
            'status'           => StudentEnrollment::STATUS_ACTIVE,
        ])->exists();
    }

    public function activeStudentsCount(ClassGroup $class): int
    {
        return StudentEnrollment::where([
            'class_group_id' => $class->id,
            'status'         => StudentEnrollment::STATUS_ACTIVE,
        ])->count();
    }

    public function assertClassHasCapacity(ClassGroup $class): void
    {
        if ($this->activeStudentsCount($class) >= $class->max_students) {
            throw new \InvalidArgumentException(
                "La classe {$class->full_name} est complète "
                . "({$class->max_students} élèves maximum)."
            );
        }
    }

    public function assertNoDuplicateEnrollment(Student $student, int $yearId): void
    {
        if ($this->hasActiveEnrollment($student, $yearId)) {
            $existing = $this->activeEnrollmentForYear($student, $yearId);

            throw new \InvalidArgumentException(
                "{$student->full_name} est déjà inscrit(e) en "
                . "{$existing?->classGroup?->full_name} pour cette année scolaire."
            );
        }
    }

    public function canEnrollInActiveYear(Student $student): bool
    {
        $year = $this->activeYear();

        return $year !== null
            && ! $this->hasActiveEnrollment($student, $year->id);
    }

    public function isEditableInActiveYear(Student $student): bool
    {
        $year = $this->activeYear();

        if (! $year) {
            return false;
        }

        $enrollment = $this->activeEnrollmentForYear($student, $year->id);

        return $enrollment === null || $enrollment->academicYear->is_active;
    }

    /**
     * Classes de l'année active pour un redoublement (même niveau).
     */
    public function classesForRepeat(Level $level, AcademicYear $year): Collection
    {
        return ClassGroup::where('academic_year_id', $year->id)
            ->where('level_id', $level->id)
            ->with('level.section')
            ->orderBy('name')
            ->get();
    }

    /**
     * Classes de l'année active pour une promotion (niveau suivant).
     */
    public function classesForPromotion(Level $previousLevel, AcademicYear $year): Collection
    {
        $nextLevel = Level::where('section_id', $previousLevel->section_id)
            ->where('order_index', '>', $previousLevel->order_index)
            ->orderBy('order_index')
            ->first();

        if (! $nextLevel) {
            return collect();
        }

        return ClassGroup::where('academic_year_id', $year->id)
            ->where('level_id', $nextLevel->id)
            ->with('level.section')
            ->orderBy('name')
            ->get();
    }

    public function pendingRenewalCount(AcademicYear $year): int
    {
        if (! $year->is_active) {
            return 0;
        }

        return Student::query()
            ->whereDoesntHave('enrollments', fn ($q) =>
                $q->where('academic_year_id', $year->id)
                  ->where('status', StudentEnrollment::STATUS_ACTIVE)
            )
            ->whereHas('enrollments', fn ($q) =>
                $q->whereHas('academicYear', fn ($y) =>
                    $y->where('start_date', '<', $year->start_date)
                )
            )
            ->count();
    }
}
