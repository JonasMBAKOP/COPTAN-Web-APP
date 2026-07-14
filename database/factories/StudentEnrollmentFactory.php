<?php

namespace Database\Factories;

use App\Models\AcademicYear;
use App\Models\ClassGroup;
use App\Models\Student;
use App\Models\StudentEnrollment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StudentEnrollment>
 */
class StudentEnrollmentFactory extends Factory
{
    protected $model = StudentEnrollment::class;

    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'class_group_id' => ClassGroup::factory(),
            'academic_year_id' => AcademicYear::factory(),
            'enrollment_date' => now()->subMonth()->toDateString(),
            'is_repeating' => false,
            'status' => 'active',
        ];
    }
}
