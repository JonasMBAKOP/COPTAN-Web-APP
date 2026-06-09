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
        Schema::create('fee_structures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')
                  ->constrained('academic_years')
                  ->cascadeOnDelete();
            $table->foreignId('class_group_id')
                  ->constrained('class_groups')
                  ->cascadeOnDelete();
            $table->decimal('total_amount', 10, 0)
                  ->comment('Montant total annuel en FCFA');
            $table->timestamps();

            // Un seul barème par classe par année
            $table->unique(['academic_year_id', 'class_group_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fee_structures');
    }
};
