<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\ClassGroup;
use App\Models\SchoolPhone;
use App\Models\SchoolSetting;
use App\Models\SchoolAgreement;
use App\Models\Section;
use App\Models\Sequence;
use App\Models\Student;
use App\Models\StudentEnrollment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class StudentDocumentService
{
    public function schoolContext(): array
    {
        return [
            'school' => SchoolSetting::instance(),
            'phones' => SchoolPhone::orderByDesc('is_primary')->orderBy('id')->get(),
            'agreements' => SchoolAgreement::orderBy('cycle')->get(),   //Devin (à voir)
        ];
    }

    public function activeYear(): ?AcademicYear
    {
        return AcademicYear::active();
    }

    public function yearFromRequest(?int $yearId): ?AcademicYear
    {
        if ($yearId) {
            return AcademicYear::find($yearId);
        }

        return $this->activeYear();
    }

    public function enrollmentForStudent(Student $student, ?AcademicYear $year): ?StudentEnrollment
    {
        if (! $year) {
            return null;
        }

        return app(EnrollmentService::class)
            ->activeEnrollmentForYear($student, $year->id)
            ?? $student->enrollments()
                ->where('academic_year_id', $year->id)
                ->with(['classGroup.level.section', 'academicYear', 'previousClassGroup'])
                ->orderByDesc('created_at')
                ->first();
    }

    public function studentsQuery(?AcademicYear $year, array $filters): Builder
    {
        $query = Student::query()
            ->with([
                'enrollments' => fn ($q) => $q
                    ->when($year, fn ($eq) => $eq->where('academic_year_id', $year->id))
                    ->where('status', StudentEnrollment::STATUS_ACTIVE)
                    ->with(['classGroup.level.section', 'academicYear', 'previousClassGroup']),
            ]);

        if ($year) {
            $query->whereHas('enrollments', fn ($q) =>
                $q->where('academic_year_id', $year->id)
                  ->where('status', StudentEnrollment::STATUS_ACTIVE)
            );
        }

        $scope = $filters['scope'] ?? 'class';

        if ($scope === 'class' && ! empty($filters['class_id'])) {
            $query->whereHas('enrollments', fn ($q) =>
                $q->where('class_group_id', $filters['class_id'])
                  ->where('status', StudentEnrollment::STATUS_ACTIVE)
                  ->when($year, fn ($eq) => $eq->where('academic_year_id', $year->id))
            );
        } elseif ($scope === 'section' && ! empty($filters['section_id'])) {
            $query->whereHas('enrollments.classGroup.level', fn ($q) =>
                $q->where('section_id', $filters['section_id'])
            );
        }

        return $query->orderBy('last_name')->orderBy('first_name');
    }

    public function getStudentsForPrint(?AcademicYear $year, array $filters): Collection
    {
        return $this->studentsQuery($year, $filters)->get()
            ->map(function (Student $student) use ($year) {
                $student->setRelation(
                    'printEnrollment',
                    $this->enrollmentForStudent($student, $year)
                );

                return $student;
            })
            ->filter(fn (Student $s) => $s->printEnrollment !== null)
            ->values();
    }

    /**
     * @return array<int, array{section: Section, classes: array<int, array{class: ClassGroup, students: Collection}>}>
     */
    public function getListGroups(?AcademicYear $year, array $filters): array
    {
        if (! $year) {
            return [];
        }

        $sectionsQuery = Section::orderBy('id');

        if (($filters['scope'] ?? 'school') === 'section' && ! empty($filters['section_id'])) {
            $sectionsQuery->where('id', $filters['section_id']);
        }

        $sections = $sectionsQuery->get();
        $groups   = [];

        foreach ($sections as $section) {
            $classesQuery = ClassGroup::where('academic_year_id', $year->id)
                ->whereHas('level', fn ($q) => $q->where('section_id', $section->id))
                ->with('level')
                ->orderBy('level_id')
                ->orderBy('name');

            if (($filters['scope'] ?? '') === 'class' && ! empty($filters['class_id'])) {
                $classesQuery->where('id', $filters['class_id']);
            }

            $classGroups = [];

            foreach ($classesQuery->get() as $class) {
                $students = $this->getStudentsForPrint($year, [
                    'scope'    => 'class',
                    'class_id' => $class->id,
                ]);

                if ($students->isNotEmpty()) {
                    $classGroups[] = [
                        'class'    => $class,
                        'students' => $students,
                    ];
                }
            }

            if ($classGroups !== []) {
                $groups[] = [
                    'section' => $section,
                    'classes' => $classGroups,
                ];
            }
        }

        return $groups;
    }

    /**
     * @return array{sections: array<int, array{section: Section, rows: array<int, array{class: ClassGroup, boys: int, girls: int, total: int}>, totals: array{boys: int, girls: int, total: int}}>, totals: array{boys: int, girls: int, total: int}}
     */
    public function getEnrollmentTotalsReport(?AcademicYear $year, array $filters): array
    {
        $emptyTotals = ['boys' => 0, 'girls' => 0, 'total' => 0];

        if (! $year) {
            return ['sections' => [], 'totals' => $emptyTotals];
        }

        $sectionsQuery = Section::orderBy('id');

        if (($filters['scope'] ?? 'school') === 'section' && ! empty($filters['section_id'])) {
            $sectionsQuery->where('id', $filters['section_id']);
        }

        if (($filters['scope'] ?? '') === 'class' && ! empty($filters['class_id'])) {
            $class = ClassGroup::with('level.section')->find($filters['class_id']);
            $sectionsQuery->when($class?->level?->section_id, fn ($q) =>
                $q->where('id', $class->level->section_id)
            );
        }

        $sections = [];
        $grandTotals = $emptyTotals;

        foreach ($sectionsQuery->get() as $section) {
            $classesQuery = ClassGroup::where('academic_year_id', $year->id)
                ->whereHas('level', fn ($q) => $q->where('section_id', $section->id))
                ->with('level')
                ->orderBy('level_id')
                ->orderBy('name');

            if (($filters['scope'] ?? '') === 'class' && ! empty($filters['class_id'])) {
                $classesQuery->where('id', $filters['class_id']);
            }

            $rows = [];
            $sectionTotals = $emptyTotals;

            foreach ($classesQuery->get() as $class) {
                $enrollments = StudentEnrollment::where('academic_year_id', $year->id)
                    ->where('class_group_id', $class->id)
                    ->where('status', StudentEnrollment::STATUS_ACTIVE)
                    ->with('student:id,gender')
                    ->get();

                $boys = $enrollments->filter(fn ($enrollment) => $enrollment->student?->gender === 'M')->count();
                $girls = $enrollments->filter(fn ($enrollment) => $enrollment->student?->gender === 'F')->count();
                $total = $boys + $girls;

                $rows[] = compact('class', 'boys', 'girls', 'total');

                $sectionTotals['boys'] += $boys;
                $sectionTotals['girls'] += $girls;
                $sectionTotals['total'] += $total;
            }

            if ($rows !== []) {
                $sections[] = [
                    'section' => $section,
                    'rows' => $rows,
                    'totals' => $sectionTotals,
                ];

                $grandTotals['boys'] += $sectionTotals['boys'];
                $grandTotals['girls'] += $sectionTotals['girls'];
                $grandTotals['total'] += $sectionTotals['total'];
            }
        }

        return ['sections' => $sections, 'totals' => $grandTotals];
    }

    public function subjectsForEnrollment(?StudentEnrollment $enrollment): Collection
    {
        if (! $enrollment?->classGroup) {
            return collect();
        }

        return $enrollment->classGroup
            ->classSubjects()
            ->where('is_active', true)
            ->with(['subject.category'])
            ->get()
            ->sortBy(fn ($cs) => $cs->subject?->name_fr ?? $cs->subject?->name_en ?? '');
    }

    public function sequencesForYear(?AcademicYear $year): Collection
    {
        if (! $year) {
            return collect();
        }

        return Sequence::where('academic_year_id', $year->id)
            ->orderBy('number')
            ->get();
    }

    public function subjectLabel($classSubject, ?Section $section = null): string
    {
        $subject = $classSubject->subject;
        if (! $subject) {
            return '—';
        }

        $useEnglish = $section && $section->language === 'en';

        return $useEnglish
            ? ($subject->name_en ?: $subject->name_fr)
            : ($subject->name_fr ?: $subject->name_en);
    }

    public function filterOptions(?AcademicYear $year): array
    {
        $sections = Section::orderBy('id')->get();
        $classes  = $year
            ? ClassGroup::where('academic_year_id', $year->id)
                ->with('level.section')
                ->orderBy('level_id')
                ->orderBy('name')
                ->get()
            : collect();

        return compact('sections', 'classes');
    }

    /**
     * Données classes pour Alpine.js (préparées en PHP, pas en Blade).
     *
     * @return array<int, array{id: int, year_id: int, section_id: int|null, label: string}>
     */
    public function classesJsonForHub(): array
    {
        return ClassGroup::with('level.section')
            ->orderBy('academic_year_id')
            ->orderBy('level_id')
            ->orderBy('name')
            ->get()
            ->map(fn (ClassGroup $c) => [
                'id'         => $c->id,
                'year_id'    => $c->academic_year_id,
                'section_id' => $c->level?->section_id,
                'label'      => $c->full_name . ' (' . ($c->level?->section?->name ?? '') . ')',
            ])
            ->values()
            ->all();
    }

    /**
     * Paramètres d'impression liste à partir des filtres de students.index.
     *
     * @return array<string, int|string>|null
     */
    public function listPrintParamsFromIndexFilters(
        ?AcademicYear $selectedYear,
        ?string $classId,
        ?string $sectionId,
        bool $renewalFilter
    ): ?array {
        if (! $selectedYear || $renewalFilter) {
            return null;
        }

        $params = ['year_id' => $selectedYear->id];

        if ($classId) {
            return array_merge($params, [
                'scope'    => 'class',
                'class_id' => (int) $classId,
            ]);
        }

        if ($sectionId) {
            return array_merge($params, [
                'scope'      => 'section',
                'section_id' => (int) $sectionId,
            ]);
        }

        return array_merge($params, ['scope' => 'school']);
    }
}
