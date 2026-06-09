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
        Schema::create('subject_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name_fr', 100)
                  ->comment('Ex: Matières Littéraires');
            $table->string('name_en', 100)->nullable()
                  ->comment('Ex: Literary Subjects');
            $table->tinyInteger('order_index')->unsigned()
                  ->comment('Ordre d\'affichage sur le bulletin');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subject_categories');
    }
};
