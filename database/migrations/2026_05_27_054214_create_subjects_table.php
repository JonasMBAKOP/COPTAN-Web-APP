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
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_category_id')
                  ->constrained('subject_categories')
                  ->cascadeOnDelete();
            $table->string('code', 20)->unique()
                  ->comment('Ex: MATH, FRAN, EPS, ANG');
            $table->string('name_fr', 100)
                  ->comment('Ex: Mathématiques');
            $table->string('name_en', 100)->nullable()
                  ->comment('Ex: Mathematics');
            $table->enum('type', [
                'general',
                'technical',
                'language',
                'sport',
                'other'
            ])->default('general');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
