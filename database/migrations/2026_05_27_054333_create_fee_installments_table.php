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
        Schema::create('fee_installments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fee_structure_id')
                  ->constrained('fee_structures')
                  ->cascadeOnDelete();
            $table->tinyInteger('installment_number')->unsigned()
                  ->comment('0 = Inscription, 1 = Tranche 1, 2 = Tranche 2...');
            $table->string('label', 50)
                  ->comment('Ex: Inscription, Tranche 1, Tranche 2');
            $table->decimal('amount', 10, 0)
                  ->comment('Montant de cette tranche en FCFA');
            $table->date('due_date_start')->nullable()
                  ->comment('Début de la période de paiement');
            $table->date('due_date_end')->nullable()
                  ->comment('Fin de la période de paiement');
            $table->timestamps();

            $table->unique(['fee_structure_id', 'installment_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fee_installments');
    }
};
