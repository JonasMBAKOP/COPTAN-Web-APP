<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ManualInsolvable;

$rows = ManualInsolvable::latest()->take(10)->with(['enrollment.student','year','recorder'])->get()->map(function($m){
    return [
        'id' => $m->id,
        'enrollment_id' => $m->student_enrollment_id,
        'student' => optional($m->enrollment->student)->full_name ?? null,
        'enrollment_academic_year_id' => optional($m->enrollment)->academic_year_id ?? null,
        'year_id' => $m->academic_year_id,
        'year_label' => optional($m->year)->label ?? null,
        'total_due' => $m->total_due,
        'total_paid' => $m->total_paid,
        'remaining' => $m->remaining,
        'selected_installments' => $m->selected_installments,
        'created_at' => $m->created_at?->toDateTimeString() ?? null,
        'recorded_by' => optional($m->recorder)->name ?? null,
    ];
});

echo json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
