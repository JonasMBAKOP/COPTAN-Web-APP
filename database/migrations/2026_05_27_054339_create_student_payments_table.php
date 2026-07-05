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
        Schema::create('student_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_enrollment_id')
                  ->constrained('student_enrollments')
                  ->cascadeOnDelete();
            $table->foreignId('fee_installment_id')
                  ->nullable()
                  ->constrained('fee_installments')
                  ->cascadeOnDelete();
            $table->decimal('amount_paid', 10, 0)
                  ->comment('Montant payé en FCFA');
            $table->date('payment_date');
            $table->enum('payment_method', [
                'cash',
                'orange_money',
                'mtn_momo',
                'bank_transfer',
                'other'
            ]);
            $table->string('reference', 100)->nullable()
                  ->comment('Référence Orange Money, MTN MoMo, virement...');
            $table->string('receipt_number', 30)->nullable()->unique()
                  ->comment('Ex: RCP-2025-00001 — Généré automatiquement');
            $table->foreignId('recorded_by')
                  ->constrained('users')
                  ->cascadeOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_payments');
    }
};
