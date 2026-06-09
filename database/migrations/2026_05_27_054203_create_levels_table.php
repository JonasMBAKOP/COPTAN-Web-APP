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
        Schema::create('levels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')
                  ->constrained('sections')
                  ->cascadeOnDelete();
            $table->string('name', 100)
                  ->comment('Ex: 6ème, Form 1, 1ère Année');
            $table->tinyInteger('order_index')->unsigned()
                  ->comment('Ordre d\'affichage dans la section');
            $table->boolean('is_exam_class')->default(false)
                  ->comment('Tle, Upper Sixth, 4ème Année technique...');
            $table->timestamps();

            $table->unique(['section_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('levels');
    }
};
