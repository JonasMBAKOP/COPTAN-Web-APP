<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\ClassGroup;
use App\Models\Level;
use App\Models\Section;
use App\Models\Sequence;
use App\Models\StudentEnrollment;
use App\Models\Student;
use App\Models\Trimester;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClassManagementDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_class_detail_page_shows_previous_evaluation_and_timetable_sections(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = User::factory()->create();
        $user->givePermissionTo('view-classes');

        $year = AcademicYear::create([
            'label' => '2024-2025',
            'start_date' => '2024-09-01',
            'end_date' => '2025-07-31',
            'is_active' => true,
        ]);

        $section = Section::create([
            'name' => 'Section Française',
            'code' => 'SF',
            'language' => 'fr',
        ]);

        $level = Level::create([
            'section_id' => $section->id,
            'name' => '6ème',
            'order_index' => 1,
            'is_exam_class' => false,
        ]);

        $classGroup = ClassGroup::create([
            'academic_year_id' => $year->id,
            'level_id' => $level->id,
            'name' => '6ème A',
            'max_students' => 40,
        ]);

        $student = Student::create([
            'matricule' => 'STU-001',
            'first_name' => 'Alice',
            'last_name' => 'Durand',
            'gender' => 'F',
            'date_of_birth' => '2010-01-01',
            'place_of_birth' => 'Yaoundé',
            'nationality' => 'Camerounaise',
        ]);

        StudentEnrollment::create([
            'student_id' => $student->id,
            'class_group_id' => $classGroup->id,
            'academic_year_id' => $year->id,
            'status' => 'active',
            'enrollment_date' => '2024-09-01',
        ]);

        $response = $this->actingAs($user)
            ->get(route('classes.show', $classGroup));

        $response->assertOk();
        $response->assertSee('Évaluation précédente');
        $response->assertSee('Emploi du temps de la classe');
        $response->assertSee('Vue d’ensemble');
        $response->assertSee('Annuel');
    }

    public function test_class_detail_page_uses_the_latest_locked_previous_sequence_for_summary(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = User::factory()->create();
        $user->givePermissionTo('view-classes');

        $year = AcademicYear::create([
            'label' => '2025-2026',
            'start_date' => '2025-09-01',
            'end_date' => '2026-07-31',
            'is_active' => true,
        ]);

        $section = Section::create([
            'name' => 'Section Française',
            'code' => 'SFR',
            'language' => 'fr',
        ]);

        $level = Level::create([
            'section_id' => $section->id,
            'name' => '5ème',
            'order_index' => 1,
            'is_exam_class' => false,
        ]);

        $classGroup = ClassGroup::create([
            'academic_year_id' => $year->id,
            'level_id' => $level->id,
            'name' => '5ème A',
            'max_students' => 40,
        ]);

        $trimester = Trimester::create([
            'academic_year_id' => $year->id,
            'number' => 1,
            'label' => 'Trimestre 1',
            'start_date' => '2025-09-01',
            'end_date' => '2025-12-20',
        ]);

        Sequence::create([
            'academic_year_id' => $year->id,
            'trimester_id' => $trimester->id,
            'number' => 1,
            'label' => 'Séquence 1',
            'start_date' => '2025-09-01',
            'end_date' => '2025-09-30',
            'is_grades_locked' => false,
        ]);

        Sequence::create([
            'academic_year_id' => $year->id,
            'trimester_id' => $trimester->id,
            'number' => 2,
            'label' => 'Séquence 2',
            'start_date' => '2025-10-01',
            'end_date' => '2025-10-31',
            'is_grades_locked' => true,
        ]);

        Sequence::create([
            'academic_year_id' => $year->id,
            'trimester_id' => $trimester->id,
            'number' => 3,
            'label' => 'Séquence 3',
            'start_date' => '2025-11-01',
            'end_date' => '2025-11-30',
            'is_grades_locked' => false,
        ]);

        $response = $this->actingAs($user)
            ->get(route('classes.show', $classGroup));

        $response->assertOk();
        $response->assertSee('Séquence 2');
    }
}
