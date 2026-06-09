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
        Schema::create('class_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_group_id')
                  ->constrained('class_groups')
                  ->cascadeOnDelete();
            $table->foreignId('subject_id')
                  ->constrained('subjects')
                  ->cascadeOnDelete();
            $table->tinyInteger('coefficient')->unsigned()->default(1)
                  ->comment('Entre 1 et 9');
            $table->decimal('hours_per_week', 4, 1)->nullable()
                  ->comment('Nombre d\'heures par semaine');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['class_group_id', 'subject_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_subjects');
    }
};
