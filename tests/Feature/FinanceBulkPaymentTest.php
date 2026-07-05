<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\ClassGroup;
use App\Models\FeeInstallment;
use App\Models\FeeStructure;
use App\Models\Level;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\StudentPayment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinanceBulkPaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_bulk_payment_creates_allocations_and_redirects_to_receipt(): void
    {
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

        $academicYear = AcademicYear::create([
            'label' => '2025-2026',
            'start_date' => '2025-09-01',
            'end_date' => '2026-06-30',
            'is_active' => true,
        ]);

        $section = Section::create(['name' => 'Science', 'code' => 'SCI']);
        $level = Level::create([
            'section_id' => $section->id,
            'name' => '6ème',
            'cycle' => '1er',
            'order_index' => 1,
        ]);
        $classGroup = ClassGroup::create([
            'academic_year_id' => $academicYear->id,
            'level_id' => $level->id,
            'name' => '6ème A',
            'max_students' => 100,
        ]);

        $feeStructure = FeeStructure::create([
            'academic_year_id' => $academicYear->id,
            'class_group_id' => $classGroup->id,
            'total_amount' => 126000,
        ]);

        $installmentMedical = FeeInstallment::create([
            'fee_structure_id' => $feeStructure->id,
            'installment_number' => 1,
            'label' => 'Carnet médical',
            'amount' => 1000,
        ]);
        $installmentInscription = FeeInstallment::create([
            'fee_structure_id' => $feeStructure->id,
            'installment_number' => 2,
            'label' => 'Inscription',
            'amount' => 55000,
        ]);
        $installmentOne = FeeInstallment::create([
            'fee_structure_id' => $feeStructure->id,
            'installment_number' => 3,
            'label' => 'Tranche 1',
            'amount' => 50000,
        ]);
        $installmentTwo = FeeInstallment::create([
            'fee_structure_id' => $feeStructure->id,
            'installment_number' => 4,
            'label' => 'Tranche 2',
            'amount' => 20000,
        ]);

        $student = Student::create([
            'first_name' => 'Alice',
            'last_name' => 'Durand',
            'gender' => 'F',
            'date_of_birth' => '2015-05-10',
            'matricule' => 'MAT-001',
        ]);
        $enrollment = StudentEnrollment::create([
            'student_id' => $student->id,
            'class_group_id' => $classGroup->id,
            'academic_year_id' => $academicYear->id,
            'status' => 'active',
            'enrollment_date' => '2025-09-01',
        ]);

        $user = User::factory()->create();
        $user->givePermissionTo('manage-finances');

        $response = $this->actingAs($user)->withoutMiddleware()->post(route('finances.bulk-pay', $enrollment), [
            'amount_paid' => 80000,
            'payment_date' => '2025-09-15',
            'payment_method' => 'cash',
        ]);

        $response->assertRedirect();

        $payments = StudentPayment::where('student_enrollment_id', $enrollment->id)
            ->orderBy('id')
            ->get();

        $this->assertCount(4, $payments);
        $this->assertSame(80000, (int) $payments->sum('amount_paid'));

        $bulkPayment = $payments->firstWhere('is_bulk', true);
        $this->assertNotNull($bulkPayment);
        $this->assertSame(80000, (int) $bulkPayment->amount_paid);

        $allocations = $payments->where('is_bulk', false)->sortBy('fee_installment_id');
        $this->assertCount(3, $allocations);
        $this->assertSame($installmentMedical->id, $allocations[0]->fee_installment_id);
        $this->assertSame(1000, (int) $allocations[0]->amount_paid);
        $this->assertSame($installmentInscription->id, $allocations[1]->fee_installment_id);
        $this->assertSame(55000, (int) $allocations[1]->amount_paid);
        $this->assertSame($installmentOne->id, $allocations[2]->fee_installment_id);
        $this->assertSame(24000, (int) $allocations[2]->amount_paid);
        $this->assertSame('Paiement en bloc', $bulkPayment->notes);
        $this->assertSame(1, StudentPayment::where('receipt_number', $bulkPayment->receipt_number)->count());
    }

    public function test_finance_index_counts_bulk_payments_once_in_summary_totals(): void
    {
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

        $academicYear = AcademicYear::create([
            'label' => '2025-2026',
            'start_date' => '2025-09-01',
            'end_date' => '2026-06-30',
            'is_active' => true,
        ]);

        $section = Section::create(['name' => 'Science', 'code' => 'SCI']);
        $level = Level::create([
            'section_id' => $section->id,
            'name' => '6ème',
            'cycle' => '1er',
            'order_index' => 1,
        ]);
        $classGroup = ClassGroup::create([
            'academic_year_id' => $academicYear->id,
            'level_id' => $level->id,
            'name' => '6ème A',
            'max_students' => 100,
        ]);

        $feeStructure = FeeStructure::create([
            'academic_year_id' => $academicYear->id,
            'class_group_id' => $classGroup->id,
            'total_amount' => 60000,
        ]);

        FeeInstallment::create([
            'fee_structure_id' => $feeStructure->id,
            'installment_number' => 1,
            'label' => 'Tranche 1',
            'amount' => 30000,
        ]);
        FeeInstallment::create([
            'fee_structure_id' => $feeStructure->id,
            'installment_number' => 2,
            'label' => 'Tranche 2',
            'amount' => 30000,
        ]);

        $student = Student::create([
            'first_name' => 'Bob',
            'last_name' => 'Martin',
            'gender' => 'M',
            'date_of_birth' => '2015-06-10',
            'matricule' => 'MAT-002',
        ]);
        $enrollment = StudentEnrollment::create([
            'student_id' => $student->id,
            'class_group_id' => $classGroup->id,
            'academic_year_id' => $academicYear->id,
            'status' => 'active',
            'enrollment_date' => '2025-09-01',
        ]);

        $user = User::factory()->create();
        $user->givePermissionTo(['view-finances', 'manage-finances']);

        $payload = [
            'amount_paid' => 40000,
            'payment_date' => '2025-09-15',
            'payment_method' => 'cash',
        ];

        $this->actingAs($user)->withoutMiddleware()->post(route('finances.bulk-pay', $enrollment), $payload);

        $response = $this->actingAs($user)->get(route('finances.index', ['year_id' => $academicYear->id]));
        $response->assertOk();
        $response->assertSee('40 000');
        $response->assertDontSee('80 000');

        $response->assertViewHas('stats', function ($stats) {
            return $stats['collected'] === 40000
                && $stats['outstanding'] === 20000
                && $stats['rate'] === 67;
        });
    }
}
