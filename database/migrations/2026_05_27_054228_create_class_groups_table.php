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
        Schema::create('class_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')
                  ->constrained('academic_years')
                  ->cascadeOnDelete();
            $table->foreignId('level_id')
                  ->constrained('levels')
                  ->cascadeOnDelete();
            $table->string('name', 50)
                  ->comment('Ex: 3ème B, Tle C, Form 3A');
            $table->string('sub_group', 10)->nullable()
                  ->comment('Ex: A, B, C — NULL si pas de sous-groupe');
            $table->string('series', 20)->nullable()
                  ->comment('Ex: C, D, A4, F3, Sciences — NULL si pas de série');
            $table->tinyInteger('max_students')->unsigned()->default(60);
            $table->foreignId('titular_staff_id')
                  ->nullable()
                  ->constrained('staff')
                  ->nullOnDelete()
                  ->comment('Titulaire de classe');
            $table->string('room', 50)->nullable();
            $table->timestamps();

            $table->unique(['academic_year_id', 'level_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_groups');
    }
};
