<?php

namespace Tests\Feature;

use App\Http\Controllers\TimetableController;
use App\Models\AcademicYear;
use App\Models\ClassGroup;
use App\Models\ClassSubject;
use App\Models\Level;
use App\Models\Section;
use App\Models\Staff;
use App\Models\Subject;
use App\Models\TeacherAssignment;
use App\Models\TimetableSlot;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use ReflectionMethod;
use Tests\TestCase;

class TimetableTeacherConflictTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('timetable_slots');
        Schema::dropIfExists('teacher_assignments');
        Schema::dropIfExists('class_subjects');
        Schema::dropIfExists('class_groups');
        Schema::dropIfExists('levels');
        Schema::dropIfExists('sections');
        Schema::dropIfExists('academic_years');
        Schema::dropIfExists('staff');
        Schema::dropIfExists('subjects');

        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('levels', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('section_id');
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('academic_years', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        Schema::create('class_groups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('academic_year_id');
            $table->unsignedBigInteger('level_id');
            $table->string('name')->nullable();
            $table->string('series')->nullable();
            $table->string('sub_group')->nullable();
            $table->timestamps();
        });

        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name_fr');
            $table->timestamps();
        });

        Schema::create('class_subjects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('class_group_id');
            $table->unsignedBigInteger('subject_id');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('teacher_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('academic_year_id');
            $table->unsignedBigInteger('staff_id');
            $table->unsignedBigInteger('class_subject_id');
            $table->timestamps();
        });

        Schema::create('timetable_slots', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('academic_year_id');
            $table->unsignedBigInteger('class_subject_id');
            $table->unsignedBigInteger('class_group_id')->nullable();
            $table->unsignedTinyInteger('day_of_week');
            $table->unsignedTinyInteger('period_index');
            $table->unsignedTinyInteger('periods_count');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->timestamps();
        });
    }

    public function test_same_level_classes_with_different_series_or_subgroups_allow_exception(): void
    {
        $section = Section::create(['name' => 'Secondaire']);
        $level = Level::create(['section_id' => $section->id, 'name' => 'Seconde']);
        $year = AcademicYear::create(['label' => '2025-2026', 'is_active' => true]);

        $teacher = Staff::create(['first_name' => 'Amadou', 'last_name' => 'Diop']);

        $classA = ClassGroup::create([
            'academic_year_id' => $year->id,
            'level_id' => $level->id,
            'name' => 'Seconde A',
            'series' => 'A',
            'sub_group' => '1',
        ]);

        $classB = ClassGroup::create([
            'academic_year_id' => $year->id,
            'level_id' => $level->id,
            'name' => 'Seconde C',
            'series' => 'C',
            'sub_group' => '2',
        ]);

        $classC = ClassGroup::create([
            'academic_year_id' => $year->id,
            'level_id' => $level->id,
            'name' => 'Seconde A',
            'series' => 'A',
            'sub_group' => '1',
        ]);

        $subject = Subject::create(['name_fr' => 'Mathématiques']);

        $classSubjectA = ClassSubject::create(['class_group_id' => $classA->id, 'subject_id' => $subject->id, 'is_active' => true]);
        $classSubjectB = ClassSubject::create(['class_group_id' => $classB->id, 'subject_id' => $subject->id, 'is_active' => true]);
        $classSubjectC = ClassSubject::create(['class_group_id' => $classC->id, 'subject_id' => $subject->id, 'is_active' => true]);

        TeacherAssignment::create(['academic_year_id' => $year->id, 'staff_id' => $teacher->id, 'class_subject_id' => $classSubjectA->id]);
        TeacherAssignment::create(['academic_year_id' => $year->id, 'staff_id' => $teacher->id, 'class_subject_id' => $classSubjectB->id]);
        TeacherAssignment::create(['academic_year_id' => $year->id, 'staff_id' => $teacher->id, 'class_subject_id' => $classSubjectC->id]);

        $existing = TimetableSlot::create([
            'academic_year_id' => $year->id,
            'class_group_id' => $classA->id,
            'class_subject_id' => $classSubjectA->id,
            'day_of_week' => 1,
            'period_index' => 2,
            'periods_count' => 2,
            'start_time' => '08:00:00',
            'end_time' => '09:30:00',
        ]);

        $controller = new TimetableController();
        $method = new ReflectionMethod(TimetableController::class, 'findTeacherConflict');
        $method->setAccessible(true);

        $sameLevelAllowed = $method->invoke(
            $controller,
            $classSubjectB->id,
            1,
            2,
            2,
            $year,
            null,
            $classB->id,
            true
        );

        $sameLevelBlocked = $method->invoke(
            $controller,
            $classSubjectC->id,
            1,
            2,
            2,
            $year,
            null,
            $classC->id,
            true
        );

        $this->assertNull($sameLevelAllowed);
        $this->assertNotNull($sameLevelBlocked);
        $this->assertSame($existing->id, $sameLevelBlocked->id);
    }
}
