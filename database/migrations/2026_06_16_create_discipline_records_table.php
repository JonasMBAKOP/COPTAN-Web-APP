<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discipline_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_year_id')->constrained('academic_years')->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('class_id')->constrained()->onDelete('cascade');
            $table->foreignId('reported_by')->constrained('staff', 'id')->onDelete('cascade');
            $table->date('incident_date');
            $table->enum('incident_type', [
                'retard',
                'insolence',
                'bagarre',
                'fraude',
                'absenteisme',
                'degradation',
                'tenue_incorrecte',
                'autre'
            ]);
            $table->text('description');
            $table->enum('sanction_type', [
                'avertissement',
                'blame',
                'retenue',
                'renvoi_temporaire',
                'exclusion_definitive',
                'aucune'
            ])->default('aucune');
            $table->unsignedTinyInteger('sanction_days')->nullable()->comment('Durée en jours pour renvoi temporaire');
            $table->date('sanction_start')->nullable();
            $table->date('sanction_end')->nullable();
            $table->enum('status', ['ouvert', 'resolu', 'classe'])->default('ouvert');
            $table->text('notes_internes')->nullable();
            $table->boolean('convocation_parent')->default(false);
            $table->date('convocation_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discipline_records');
    }
};