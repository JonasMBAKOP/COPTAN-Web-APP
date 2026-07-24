<?php

putenv('APP_ENV=testing');
putenv('DB_CONNECTION=sqlite');
putenv('DB_DATABASE=:memory:');

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;
use App\Models\AcademicYear;
use App\Models\ClassGroup;
use App\Models\FeeInstallment;
use App\Models\FeeStructure;
use App\Models\Level;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\User;
use App\Http\Controllers\FinanceController;

try {
    Artisan::call('migrate:fresh', ['--force' => true]);
    Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\RolesAndPermissionsSeeder']);

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

    $request = Request::create('/finances/students/' . $enrollment->id . '/bulk-pay', 'POST', [
        'amount_paid' => 80000,
        'payment_date' => '2025-09-15',
        'payment_method' => 'cash',
    ]);

    $controller = new FinanceController();
    $response = $controller->bulkPay($request, $enrollment);

    echo 'Response class: ' . get_class($response) . PHP_EOL;
    if (method_exists($response, 'getStatusCode')) {
        echo 'Status: ' . $response->getStatusCode() . PHP_EOL;
    }
    if (method_exists($response, 'getTargetUrl')) {
        echo 'Redirect target: ' . $response->getTargetUrl() . PHP_EOL;
    }

    echo 'Payment count: ' . App\Models\StudentPayment::where('student_enrollment_id', $enrollment->id)->count() . PHP_EOL;
    foreach (App\Models\StudentPayment::where('student_enrollment_id', $enrollment->id)->orderBy('id')->get() as $payment) {
        echo sprintf("Payment %s: amount=%s scholarship=%s is_bulk=%s fee_installment_id=%s parent=%s receipt=%s\n", $payment->id, $payment->amount_paid, $payment->scholarship_amount, $payment->is_bulk ? '1' : '0', $payment->fee_installment_id, $payment->parent_payment_id, $payment->receipt_number);
    }
} catch (Throwable $e) {
    echo 'Exception: ' . get_class($e) . ' - ' . $e->getMessage() . PHP_EOL;
    echo $e->getTraceAsString();
}
