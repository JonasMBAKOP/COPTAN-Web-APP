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
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_enrollment_id')
                  ->constrained('student_enrollments')
                  ->cascadeOnDelete();
            $table->foreignId('class_subject_id')
                  ->constrained('class_subjects')
                  ->cascadeOnDelete();
            $table->foreignId('sequence_id')
                  ->constrained('sequences')
                  ->cascadeOnDelete();
            $table->decimal('grade', 5, 2)->nullable()
                  ->comment('NULL = note pas encore saisie');
            $table->boolean('is_absent')->default(false)
                  ->comment('TRUE = élève absent à l\'évaluation');
            $table->foreignId('entered_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete()
                  ->comment('Utilisateur qui a saisi la note');
            $table->timestamp('entered_at')->nullable();
            $table->foreignId('updated_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete()
                  ->comment('Utilisateur qui a modifié la note');
            $table->timestamps();

            // Une seule note par élève par matière par séquence
            $table->unique([
                'student_enrollment_id',
                'class_subject_id',
                'sequence_id'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
