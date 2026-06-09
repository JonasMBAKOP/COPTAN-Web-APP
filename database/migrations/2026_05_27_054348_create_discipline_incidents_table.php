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
        Schema::create('discipline_incidents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_enrollment_id')
                  ->constrained('student_enrollments')
                  ->cascadeOnDelete();
            $table->date('incident_date');
            $table->time('incident_time')->nullable();
            $table->enum('location', [
                'classroom', 'courtyard',
                'corridor', 'cafeteria', 'other'
            ])->nullable();
            $table->string('incident_type', 100);
            $table->text('description');
            $table->enum('sanction_type', [
                'observation',
                'warning',
                'detention',
                'temporary_suspension',
                'definitive_exclusion'
            ]);
            $table->tinyInteger('sanction_duration_days')
                  ->unsigned()->nullable()
                  ->comment('Durée en jours pour retenue/renvoi temporaire');
            $table->foreignId('decided_by')
                  ->constrained('users')
                  ->cascadeOnDelete();
            $table->boolean('parent_convoked')->default(false);
            $table->date('convocation_date')->nullable();
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->foreignId('reported_by')
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
        Schema::dropIfExists('discipline_incidents');
    }
};
