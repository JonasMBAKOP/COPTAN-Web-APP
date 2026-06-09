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
        Schema::create('sequences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')
                  ->constrained('academic_years')
                  ->cascadeOnDelete();
            $table->foreignId('trimester_id')
                  ->constrained('trimesters')
                  ->cascadeOnDelete();
            $table->tinyInteger('number')->unsigned()
                  ->comment('1 à 6');
            $table->string('label', 50)
                  ->comment('Ex: Séquence 1');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_grades_locked')->default(false)
                  ->comment('Verrou global des notes de la séquence');
            $table->timestamps();

            $table->unique(['academic_year_id', 'number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sequences');
    }
};
