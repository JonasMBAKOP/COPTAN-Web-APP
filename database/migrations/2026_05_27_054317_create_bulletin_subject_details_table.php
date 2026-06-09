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
        Schema::create('bulletin_subject_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bulletin_report_id')
                  ->constrained('bulletin_reports')
                  ->cascadeOnDelete();
            $table->foreignId('class_subject_id')
                  ->constrained('class_subjects')
                  ->cascadeOnDelete();
            $table->tinyInteger('subject_order')->nullable()
                  ->comment('Ordre d\'affichage dans la catégorie');

            // Données figées à la génération du bulletin
            $table->tinyInteger('coefficient')->unsigned()
                  ->comment('Coefficient copié au moment de la génération');
            $table->string('teacher_name', 200)->nullable()
                  ->comment('Nom de l\'enseignant figé à la génération');

            // Notes selon le type de bulletin
            $table->decimal('seq_grade', 5, 2)->nullable()
                  ->comment('Bulletin SÉQUENTIEL : note unique de la séquence (DS)');
            $table->decimal('seq1_grade', 5, 2)->nullable()
                  ->comment('Bulletin TRIMESTRIEL : note séquence 1 du trimestre');
            $table->decimal('seq2_grade', 5, 2)->nullable()
                  ->comment('Bulletin TRIMESTRIEL : note séquence 2 du trimestre');
            $table->decimal('trim1_average', 5, 2)->nullable()
                  ->comment('Bulletin ANNUEL : moyenne trimestre 1');
            $table->decimal('trim2_average', 5, 2)->nullable()
                  ->comment('Bulletin ANNUEL : moyenne trimestre 2');
            $table->decimal('trim3_average', 5, 2)->nullable()
                  ->comment('Bulletin ANNUEL : moyenne trimestre 3');

            // Résultats calculés (communs à tous les types)
            $table->decimal('average', 5, 2)->nullable()
                  ->comment('Moyenne de la matière pour la période');
            $table->decimal('total', 7, 2)->nullable()
                  ->comment('average × coefficient');
            $table->smallInteger('rank_in_subject')->unsigned()->nullable()
                  ->comment('Rang de l\'élève dans cette matière');
            $table->string('appreciation', 10)->nullable()
                  ->comment('CNA, CMA, CA, CBA, CTBA');
            $table->timestamps();

            $table->unique(
                ['bulletin_report_id', 'class_subject_id'],
                'bsd_unique_report_subject'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bulletin_subject_details');
    }
};
