<?php

namespace Tests\Feature;

use App\Http\Controllers\StaffController;
use App\Models\Staff;
use App\Models\StaffPaySlip;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use Tests\TestCase;

class StaffSalaryPrintTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('staff_positions');
        Schema::dropIfExists('timetable_slots');
        Schema::dropIfExists('teacher_assignments');
        Schema::dropIfExists('class_subjects');
        Schema::dropIfExists('school_phones');
        Schema::dropIfExists('school_settings');
        Schema::dropIfExists('users');
        Schema::dropIfExists('staff');

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('guard_name')->default('web');
            $table->timestamps();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('guard_name')->default('web');
            $table->timestamps();
        });

        Schema::create('model_has_roles', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
        });

        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('email', 191)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('contract_type')->default('permanent');
            $table->unsignedBigInteger('monthly_salary')->nullable();
            $table->unsignedBigInteger('hourly_rate')->nullable();
            $table->unsignedBigInteger('period_rate')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('staff_positions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('staff_id');
            $table->string('position');
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });

        Schema::create('school_settings', function (Blueprint $table) {
            $table->id();
            $table->string('full_name')->nullable();
            $table->string('short_name')->nullable();
            $table->string('postal_box')->nullable();
            $table->timestamps();
        });

        Schema::create('school_phones', function (Blueprint $table) {
            $table->id();
            $table->string('number')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });

        Schema::create('academic_years', function (Blueprint $table) {
            $table->id();
            $table->string('label')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name_fr')->nullable();
            $table->string('name_en')->nullable();
            $table->timestamps();
        });

        Schema::create('class_subjects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('class_group_id')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
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
            $table->unsignedTinyInteger('period_index')->default(1);
            $table->unsignedTinyInteger('periods_count')->default(1);
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->timestamps();
        });

        Schema::create('staff_pay_slips', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('staff_id');
            $table->decimal('amount_received', 12, 2)->nullable();
            $table->string('period')->nullable();
            $table->timestamps();
        });
    }

    public function test_print_salary_list_renders_staff_summary(): void
    {
        $staff = Staff::create([
            'first_name' => 'Jean',
            'last_name' => 'Dupont',
            'email' => 'jean@example.com',
            'phone' => '0700000000',
            'contract_type' => 'permanent',
            'monthly_salary' => 250000,
            'hourly_rate' => null,
            'period_rate' => null,
            'is_active' => true,
        ]);

        $controller = new StaffController();
        $response = $controller->printSalaryList(new Request());

        $this->assertInstanceOf(View::class, $response);
        $content = $response->render();
        $this->assertStringContainsString('FICHE DE SALAIRES', $content);
        $this->assertStringContainsString($staff->full_name, $content);
        $this->assertStringContainsString('Permanent', $content);
        $this->assertStringContainsString('250,000', $content);
    }

    public function test_pay_slip_renders_employee_payslip(): void
    {
        $staff = Staff::create([
            'first_name' => 'Marie',
            'last_name' => 'Kouassi',
            'email' => 'marie@example.com',
            'phone' => '0711111111',
            'contract_type' => 'vacataire',
            'monthly_salary' => null,
            'hourly_rate' => 5000,
            'period_rate' => null,
            'is_active' => true,
        ]);

        $controller = new StaffController();
        $response = $controller->paySlip($staff);

        $this->assertInstanceOf(View::class, $response);
        $content = $response->render();
        $this->assertStringContainsString('Préparation du bulletin de paie', $content);
        $this->assertStringContainsString($staff->full_name, $content);
        $this->assertStringContainsString('Vacataire', $content);
        $this->assertStringContainsString('amount_received', $content);
    }

    public function test_preview_pay_slip_persists_amount_and_period(): void
    {
        $staff = Staff::create([
            'first_name' => 'Alice',
            'last_name' => 'Nguessan',
            'email' => 'alice@example.com',
            'phone' => '0722222222',
            'contract_type' => 'permanent',
            'monthly_salary' => 350000,
            'hourly_rate' => null,
            'period_rate' => null,
            'is_active' => true,
        ]);

        $controller = new StaffController();
        $request = new Request([
            'amount_received' => 310000,
            'period' => '2026-07',
        ]);

        $response = $controller->storePaySlip($request, $staff);

            $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
        $this->assertSame(1, StaffPaySlip::count());
        $this->assertSame(310000.0, StaffPaySlip::first()->amount_received);
        $this->assertSame('2026-07', StaffPaySlip::first()->period);
    }

    public function test_passage_planning_keeps_permanent_staff_visible_every_day_and_restricts_other_contracts_to_scheduled_days(): void
    {
        $user = \App\Models\User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('secret123'),
            'is_active' => true,
        ]);
        $this->actingAs($user);

        $year = \App\Models\AcademicYear::create([
            'label' => '2026-2027',
            'start_date' => '2026-09-01',
            'end_date' => '2027-06-30',
            'is_active' => true,
        ]);

        $permanent = Staff::create([
            'first_name' => 'Paul',
            'last_name' => 'Permanent',
            'email' => 'paul@example.com',
            'phone' => '0100000001',
            'contract_type' => 'permanent',
            'is_active' => true,
        ]);

        $vacataire = Staff::create([
            'first_name' => 'Vero',
            'last_name' => 'Vacataire',
            'email' => 'vero@example.com',
            'phone' => '0100000002',
            'contract_type' => 'vacataire',
            'is_active' => true,
        ]);

        $semi = Staff::create([
            'first_name' => 'Sonia',
            'last_name' => 'Semi',
            'email' => 'sonia@example.com',
            'phone' => '0100000003',
            'contract_type' => 'semi_permanent',
            'is_active' => true,
        ]);

        $permanentPosition = \App\Models\StaffPosition::create([
            'staff_id' => $permanent->id,
            'position' => 'enseignant',
            'is_primary' => true,
        ]);

        $vacatairePosition = \App\Models\StaffPosition::create([
            'staff_id' => $vacataire->id,
            'position' => 'enseignant',
            'is_primary' => true,
        ]);

        $semiPosition = \App\Models\StaffPosition::create([
            'staff_id' => $semi->id,
            'position' => 'enseignant',
            'is_primary' => true,
        ]);

        $subject = \App\Models\Subject::create([
            'name_fr' => 'Mathématiques',
            'name_en' => 'Mathematics',
        ]);

        $classSubject = \App\Models\ClassSubject::create([
            'class_group_id' => null,
            'subject_id' => $subject->id,
            'is_active' => true,
        ]);

        \App\Models\TeacherAssignment::create([
            'academic_year_id' => $year->id,
            'staff_id' => $vacataire->id,
            'class_subject_id' => $classSubject->id,
        ]);

        \App\Models\TimetableSlot::create([
            'academic_year_id' => $year->id,
            'class_subject_id' => $classSubject->id,
            'class_group_id' => null,
            'day_of_week' => 1,
            'period_index' => 1,
            'periods_count' => 1,
            'start_time' => '08:00:00',
            'end_time' => '09:00:00',
        ]);

        $response = (new StaffController())->passagePlanning(new Request(['day' => 1, 'contract' => null]));
        $content = $response->render();

        $this->assertStringContainsString($permanent->full_name, $content);
        $this->assertStringContainsString($vacataire->full_name, $content);
        $this->assertStringContainsString('Personnels programmés', $content);

        $response2 = (new StaffController())->passagePlanning(new Request(['day' => 2, 'contract' => 'vacataire']));
        $content2 = $response2->render();
        $this->assertStringNotContainsString($vacataire->full_name, $content2);

        $response3 = (new StaffController())->passagePlanning(new Request(['day' => 1, 'contract' => 'semi_permanent']));
        $content3 = $response3->render();
        $this->assertStringNotContainsString($semi->full_name, $content3);
    }
}
