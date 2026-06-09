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
        Schema::create('absences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_enrollment_id')
                  ->constrained('student_enrollments')
                  ->cascadeOnDelete();
            $table->date('absence_date');
            $table->string('period', 20)->nullable()
                  ->comment('Ex: matin, après-midi');
            $table->foreignId('class_subject_id')
                  ->nullable()
                  ->constrained('class_subjects')
                  ->nullOnDelete()
                  ->comment('Matière pendant laquelle l\'absence a eu lieu');
            $table->decimal('hours', 4, 1)->default(1)
                  ->comment('Nombre d\'heures d\'absence');
            $table->boolean('is_justified')->default(false);
            $table->text('justification')->nullable();
            $table->foreignId('recorded_by')
                  ->constrained('users')
                  ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absences');
    }
};
