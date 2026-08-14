<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;
use App\Http\Controllers\FinanceController;
use App\Models\AcademicYear;

 $request = Request::create('/finances/insolvables', 'GET', ['year_id' => 2]);
 $controller = new FinanceController();
 $ref = new ReflectionClass(FinanceController::class);
 $method = $ref->getMethod('buildInsolvencyRows');
 $method->setAccessible(true);
 $selectedYear = AcademicYear::find(2);
 $rows = $method->invokeArgs($controller, [$selectedYear, null, null, null]);
// For debugging: also show manualEnrollmentIds and enrollment ids
// Re-run logic from controller to inspect intermediate values
use App\Models\ManualInsolvable;
$manualEnrollmentIds = ManualInsolvable::where(function($q) use ($selectedYear) {
    $q->where('academic_year_id', $selectedYear->id)
      ->orWhereNull('academic_year_id')
      ->orWhereHas('enrollment', fn($e) => $e->where('academic_year_id', $selectedYear->id));
})->pluck('student_enrollment_id')->unique()->filter()->values();

// Load enrollments
$enrollments = \App\Models\StudentEnrollment::query()
    ->active()
    ->where('academic_year_id', $selectedYear->id)
    ->with(['student','classGroup.level.section','classGroup.feeStructures.installments'])
    ->get();

if ($manualEnrollmentIds->isNotEmpty()) {
    $manualEnrollments = \App\Models\StudentEnrollment::with(['student','classGroup.level.section','classGroup.feeStructures.installments'])
        ->whereIn('id', $manualEnrollmentIds)
        ->get();
    $enrollments = $enrollments->merge($manualEnrollments)->unique('id')->values();
}

echo "manualEnrollmentIds: " . json_encode($manualEnrollmentIds->values()) . PHP_EOL;
echo "enrollments_ids_after_merge: " . json_encode($enrollments->pluck('id')->values()) . PHP_EOL;

foreach ($enrollments as $en) {
    $feeStructure = $en->classGroup()->with('feeStructures.installments')->first()?->feeStructures->first();
    $feeTotal = $feeStructure?->installments->sum('amount') ?? 0;
    $totalPaid = \App\Models\StudentPayment::visible()->where('student_enrollment_id', $en->id)->sum('amount_paid') + \App\Models\StudentPayment::visible()->where('student_enrollment_id', $en->id)->sum('scholarship_amount');
    echo "enrollment: {$en->id}, student: " . optional($en->student)->full_name . ", feeTotal: {$feeTotal}, totalPaid: {$totalPaid}" . PHP_EOL;
}

echo json_encode($rows->map(function($r){
    return [
        'enrollment_id' => $r['enrollment']->id,
        'student' => $r['enrollment']->student->full_name,
        'total_due' => $r['total_due'],
        'total_paid' => $r['total_paid'],
        'remaining' => $r['remaining'],
        'manual' => $r['manual'] ?? false,
    ];
})->values(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
