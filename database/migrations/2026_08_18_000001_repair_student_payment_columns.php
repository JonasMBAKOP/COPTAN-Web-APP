<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Repair payment columns for deployments where an older migration was
     * recorded as run before the schema change reached the production DB.
     */
    public function up(): void
    {
        if (! Schema::hasTable('student_payments')) {
            return;
        }

        if (! Schema::hasColumn('student_payments', 'parent_payment_id')) {
            Schema::table('student_payments', function (Blueprint $table): void {
                $table->foreignId('parent_payment_id')
                    ->nullable()
                    ->after('student_enrollment_id')
                    ->constrained('student_payments')
                    ->nullOnDelete();
            });
        }

        if (! Schema::hasColumn('student_payments', 'is_bulk')) {
            Schema::table('student_payments', function (Blueprint $table): void {
                $table->boolean('is_bulk')->default(false)->after('parent_payment_id');
            });
        }

        if (! Schema::hasColumn('student_payments', 'scholarship_amount')) {
            Schema::table('student_payments', function (Blueprint $table): void {
                $table->integer('scholarship_amount')->default(0)->after('amount_paid');
            });
        }

        if (! Schema::hasColumn('student_payments', 'snapshot_total_due')) {
            Schema::table('student_payments', function (Blueprint $table): void {
                $table->integer('snapshot_total_due')->nullable()->after('amount_paid');
            });
        }

        if (! Schema::hasColumn('student_payments', 'snapshot_total_paid')) {
            Schema::table('student_payments', function (Blueprint $table): void {
                $table->integer('snapshot_total_paid')->nullable()->after('snapshot_total_due');
            });
        }

        if (! Schema::hasColumn('student_payments', 'snapshot_total_remaining')) {
            Schema::table('student_payments', function (Blueprint $table): void {
                $table->integer('snapshot_total_remaining')->nullable()->after('snapshot_total_paid');
            });
        }
    }

    /**
     * This is a repair migration. It intentionally leaves existing columns
     * untouched when rolled back, preventing accidental data loss.
     */
    public function down(): void
    {
        // Intentionally empty.
    }
};
