<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\ClassGroup;
use App\Models\Level;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentDocumentWordExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_listes_export_word_returns_docx_file(): void
    {
        $user = User::factory()->create();
        $year = AcademicYear::create([
            'label' => '2024-2025',
            'start_date' => '2024-09-01',
            'end_date' => '2025-07-31',
            'is_active' => true,
        ]);

        $section = Section::create(['name' => 'Primaire', 'code' => 'PRI', 'language' => 'fr']);
        $level = Level::create(['section_id' => $section->id, 'name' => 'CP1', 'order_index' => 1]);
        $class = ClassGroup::create([
            'academic_year_id' => $year->id,
            'level_id' => $level->id,
            'name' => 'A',
        ]);

        $student = Student::create([
            'matricule' => 'CP20240001',
            'first_name' => 'Jean',
            'last_name' => 'Dupont',
            'gender' => 'M',
            'date_of_birth' => '2015-01-01',
        ]);

        StudentEnrollment::create([
            'student_id' => $student->id,
            'class_group_id' => $class->id,
            'academic_year_id' => $year->id,
            'status' => StudentEnrollment::STATUS_ACTIVE,
            'enrollment_date' => '2024-09-01',
        ]);

        $response = $this->actingAs($user)
            ->withoutMiddleware()
            ->get(route('students.documents.lists.word', ['year_id' => $year->id, 'scope' => 'class', 'class_id' => $class->id]));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        $response->assertHeader('Content-Disposition');
    }

    public function test_effectifs_export_word_returns_docx_file(): void
    {
        $user = User::factory()->create();
        $year = AcademicYear::create([
            'label' => '2024-2025',
            'start_date' => '2024-09-01',
            'end_date' => '2025-07-31',
            'is_active' => true,
        ]);

        $section = Section::create(['name' => 'Primaire', 'code' => 'PRI', 'language' => 'fr']);
        $level = Level::create(['section_id' => $section->id, 'name' => 'CP1', 'order_index' => 1]);
        $class = ClassGroup::create([
            'academic_year_id' => $year->id,
            'level_id' => $level->id,
            'name' => 'A',
        ]);

        $student = Student::create([
            'matricule' => 'CP20240002',
            'first_name' => 'Anne',
            'last_name' => 'Martin',
            'gender' => 'F',
            'date_of_birth' => '2015-02-01',
        ]);

        StudentEnrollment::create([
            'student_id' => $student->id,
            'class_group_id' => $class->id,
            'academic_year_id' => $year->id,
            'status' => StudentEnrollment::STATUS_ACTIVE,
            'enrollment_date' => '2024-09-01',
        ]);

        $response = $this->actingAs($user)
            ->withoutMiddleware()
            ->get(route('students.documents.enrollment-totals-report.word', ['year_id' => $year->id, 'scope' => 'section', 'section_id' => $section->id]));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        $response->assertHeader('Content-Disposition');
    }
}
