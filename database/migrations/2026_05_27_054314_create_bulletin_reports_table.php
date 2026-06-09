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
        Schema::create('bulletin_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_enrollment_id')
                  ->constrained('student_enrollments')
                  ->cascadeOnDelete();
            $table->enum('type', ['sequential', 'trimestrial', 'annual']);
            $table->foreignId('sequence_id')
                  ->nullable()
                  ->constrained('sequences')
                  ->nullOnDelete()
                  ->comment('Renseigné uniquement pour les bulletins séquentiels');
            $table->foreignId('trimester_id')
                  ->nullable()
                  ->constrained('trimesters')
                  ->nullOnDelete()
                  ->comment('Renseigné pour les bulletins trimestriels');
            $table->foreignId('academic_year_id')
                  ->constrained('academic_years')
                  ->cascadeOnDelete();
            // Statistiques figées à la génération
            $table->decimal('average_general', 5, 2)->nullable()
                  ->comment('Moyenne générale de l\'élève');
            $table->smallInteger('rank')->unsigned()->nullable()
                  ->comment('Rang de l\'élève dans la classe');
            $table->smallInteger('class_size')->unsigned()->nullable()
                  ->comment('Effectif de la classe');
            $table->decimal('class_average', 5, 2)->nullable()
                  ->comment('Moyenne générale de la classe');
            $table->decimal('highest_average', 5, 2)->nullable()
                  ->comment('Moyenne la plus haute de la classe');
            $table->decimal('lowest_average', 5, 2)->nullable()
                  ->comment('Moyenne la plus basse de la classe');
            // Absences
            $table->decimal('justified_absences', 5, 1)->default(0);
            $table->decimal('unjustified_absences', 5, 1)->default(0);
            // Décision et distinction
            $table->foreignId('council_decision_id')
                  ->nullable()
                  ->constrained('council_decisions')
                  ->nullOnDelete();
            $table->foreignId('distinction_id')
                  ->nullable()
                  ->constrained('distinctions')
                  ->nullOnDelete();
            $table->text('general_observation')->nullable();
            // Statut de publication
            $table->boolean('is_published')->default(false);
            $table->timestamp('generated_at')->nullable();
            $table->foreignId('generated_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->timestamps();

            $table->unique(
                ['student_enrollment_id', 'type', 'sequence_id', 'trimester_id'],
                'br_unique_enrollment_type_seq_trim'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bulletin_reports');
    }
};
