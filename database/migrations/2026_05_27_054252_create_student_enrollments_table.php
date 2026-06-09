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
        Schema::create('student_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')
                  ->constrained('students')
                  ->cascadeOnDelete();
            $table->foreignId('class_group_id')
                  ->constrained('class_groups')
                  ->cascadeOnDelete();
            $table->foreignId('academic_year_id')
                  ->constrained('academic_years')
                  ->cascadeOnDelete();
            $table->date('enrollment_date');
            $table->boolean('is_repeating')->default(false)
                  ->comment('Redoublant(e) : Oui / Non');
            $table->foreignId('previous_class_group_id')
                  ->nullable()
                  ->constrained('class_groups')
                  ->nullOnDelete()
                  ->comment('Classe de l\'année précédente');
            $table->string('origin_school', 200)->nullable()
                  ->comment('Établissement d\'origine si vient d\'ailleurs');
            $table->enum('status', [
                'active',
                'transferred_out',
                'withdrawn',
                'inactive'
            ])->default('active');
            $table->date('transfer_date')->nullable();
            $table->string('transfer_destination', 200)->nullable();
            $table->timestamps();

            // Un élève ne peut être inscrit qu'une fois par année
            $table->unique(['student_id', 'academic_year_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_enrollments');
    }
};
