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
        Schema::dropIfExists('school_phones');
        Schema::dropIfExists('school_settings');
        Schema::dropIfExists('users');
        Schema::dropIfExists('staff');

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamps();
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
}
