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
        Schema::create('censeur_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('staff')->onDelete('cascade');
            $table->foreignId('section_id')->constrained('sections')->onDelete('cascade');
            $table->string('cycle'); // '1er' ou '2nd'
            $table->timestamps();

            $table->unique(['staff_id', 'section_id', 'cycle'], 'censeur_sec_cyc_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('censeur_assignments');
    }
};
