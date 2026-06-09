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
        Schema::create('teacher_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')
                  ->constrained('academic_years')
                  ->cascadeOnDelete();
            $table->foreignId('staff_id')
                  ->constrained('staff')
                  ->cascadeOnDelete();
            $table->foreignId('class_subject_id')
                  ->constrained('class_subjects')
                  ->cascadeOnDelete();
            $table->timestamps();

            // Un seul enseignant par matière par classe par année
            $table->unique(['academic_year_id', 'class_subject_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_assignments');
    }
};
