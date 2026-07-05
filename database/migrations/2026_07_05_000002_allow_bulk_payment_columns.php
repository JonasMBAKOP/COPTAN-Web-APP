<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('student_payments', function (Blueprint $table) {
            $table->foreignId('fee_installment_id')
                ->nullable()
                ->change();
            $table->string('receipt_number', 30)
                ->nullable()
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('student_payments', function (Blueprint $table) {
            $table->foreignId('fee_installment_id')
                ->nullable(false)
                ->change();
            $table->string('receipt_number', 30)
                ->nullable(false)
                ->change();
        });
    }
};
