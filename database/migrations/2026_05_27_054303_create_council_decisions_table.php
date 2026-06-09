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
        Schema::create('council_decisions', function (Blueprint $table) {
            $table->id();
            $table->string('label_fr', 200);
            $table->string('label_en', 200)->nullable();
            $table->boolean('exam_classes_only')->default(false)
                  ->comment('TRUE = uniquement pour les classes d\'examen');
            $table->boolean('is_active')->default(true);
            $table->tinyInteger('order_index')->unsigned()->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('council_decisions');
    }
};
