<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$kernel = app(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

Artisan::call('migrate:fresh', ['--seed' => true]);

$academicYear = App\Models\AcademicYear::create([
    'label' => '2025-2026',
    'start_date' => '2025-09-01',
    'end_date' => '2026-06-30',
    'is_active' => true,
]);

$section = App\Models\Section::create(['name' => 'Science', 'code' => 'SCI']);
$level = App\Models\Level::create([
    'section_id' => $section->id,
    'name' => '6ème',
    'cycle' => '1er',
    'order_index' => 1,
]);
$classGroup = App\Models\ClassGroup::create([
    'academic_year_id' => $academicYear->id,
    'level_id' => $level->id,
    'name' => '6ème A',
    'max_students' => 100,
]);

$feeStructure = App\Models\FeeStructure::create([
    'academic_year_id' => $academicYear->id,
    'class_group_id' => $classGroup->id,
    'total_amount' => 126000,
]);

$installmentMedical = App\Models\FeeInstallment::create([
    'fee_structure_id' => $feeStructure->id,
    'installment_number' => 1,
    'label' => 'Carnet médical',
    'amount' => 1000,
]);
$installmentInscription = App\Models\FeeInstallment::create([
    'fee_structure_id' => $feeStructure->id,
    'installment_number' => 2,
    'label' => 'Inscription',
    'amount' => 55000,
]);
$installmentOne = App\Models\FeeInstallment::create([
    'fee_structure_id' => $feeStructure->id,
    'installment_number' => 3,
    'label' => 'Tranche 1',
    'amount' => 50000,
]);
$installmentTwo = App\Models\FeeInstallment::create([
    'fee_structure_id' => $feeStructure->id,
    'installment_number' => 4,
    'label' => 'Tranche 2',
    'amount' => 20000,
]);

$student = App\Models\Student::create([
    'first_name' => 'Alice',
    'last_name' => 'Durand',
    'gender' => 'F',
    'date_of_birth' => '2015-05-10',
    'matricule' => 'MAT-001',
]);
$enrollment = App\Models\StudentEnrollment::create([
    'student_id' => $student->id,
    'class_group_id' => $classGroup->id,
    'academic_year_id' => $academicYear->id,
    'status' => 'active',
    'enrollment_date' => '2025-09-01',
]);

$user = App\Models\User::factory()->create();
$user->givePermissionTo('manage-finances');
Auth::login($user);

$request = new Illuminate\Http\Request([
    'amount_paid' => 80000,
    'payment_date' => '2025-09-15',
    'payment_method' => 'cash',
]);

$controller = new App\Http\Controllers\FinanceController();

try {
    $response = $controller->bulkPay($request, $enrollment);

    $payments = App\Models\StudentPayment::where('student_enrollment_id', $enrollment->id)->orderBy('id')->get();

    $output = [];
    $output[] = 'count=' . $payments->count();
    $output[] = 'sum=' . $payments->sum('amount_paid');
    foreach ($payments as $payment) {
        $output[] = $payment->id . '|' . ($payment->is_bulk ? 'bulk' : 'alloc') . '|parent=' . ($payment->parent_payment_id ?? 'null') . '|inst=' . ($payment->fee_installment_id ?? 'null') . '|amount=' . $payment->amount_paid . '|notes=' . $payment->notes . '|receipt=' . $payment->receipt_number;
    }
    file_put_contents('bulk-debug.txt', implode(PHP_EOL, $output));
} catch (Throwable $e) {
    file_put_contents('bulk-debug.txt', get_class($e) . ': ' . $e->getMessage() . PHP_EOL . $e->getTraceAsString());
    throw $e;
}
