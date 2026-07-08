<?php

use App\Models\StudentPayment;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_payments', function (Blueprint $table) {
            $table->integer('snapshot_total_due')->nullable()->after('amount_paid');
            $table->integer('snapshot_total_paid')->nullable()->after('snapshot_total_due');
            $table->integer('snapshot_total_remaining')->nullable()->after('snapshot_total_paid');
        });

        $payments = StudentPayment::query()
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();

        $runningPaidByEnrollment = [];

        foreach ($payments as $payment) {
            $enrollment = $payment->studentEnrollment()->first();
            if (! $enrollment) {
                continue;
            }

            $feeStructure = $enrollment->classGroup?->feeStructures->first();
            $totalDue = (int) ($feeStructure?->installments->sum('amount') ?? 0);

            if (! isset($runningPaidByEnrollment[$enrollment->id])) {
                $runningPaidByEnrollment[$enrollment->id] = 0;
            }

            if (is_null($payment->parent_payment_id) || $payment->is_bulk) {
                $runningPaidByEnrollment[$enrollment->id] += (int) $payment->amount_paid;
            }

            $payment->forceFill([
                'snapshot_total_due'        => $totalDue,
                'snapshot_total_paid'      => $runningPaidByEnrollment[$enrollment->id],
                'snapshot_total_remaining' => max(0, $totalDue - $runningPaidByEnrollment[$enrollment->id]),
            ])->saveQuietly();
        }
    }

    public function down(): void
    {
        Schema::table('student_payments', function (Blueprint $table) {
            $table->dropColumn(['snapshot_total_due', 'snapshot_total_paid', 'snapshot_total_remaining']);
        });
    }
};
