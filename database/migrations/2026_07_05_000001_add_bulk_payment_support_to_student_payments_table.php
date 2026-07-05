<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('student_payments', function (Blueprint $table) {
            $table->foreignId('parent_payment_id')
                ->nullable()
                ->after('student_enrollment_id')
                ->constrained('student_payments')
                ->nullOnDelete();
            $table->boolean('is_bulk')->default(false)->after('parent_payment_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_payments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('parent_payment_id');
            $table->dropColumn('is_bulk');
        });
    }
};
