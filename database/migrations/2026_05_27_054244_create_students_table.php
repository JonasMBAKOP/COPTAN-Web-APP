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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('matricule', 20)->unique()
                  ->comment('Ex: CP-2025-0001 — Généré automatiquement');
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->enum('gender', ['M', 'F']);
            $table->date('date_of_birth');
            $table->string('place_of_birth', 100)->nullable();
            $table->string('nationality', 100)->default('Camerounaise');
            $table->string('photo', 255)->nullable()
                  ->comment('Chemin vers le fichier image');
            // Contacts familiaux
            $table->string('father_name', 200)->nullable();
            $table->string('father_phone', 20)->nullable();
            $table->string('mother_name', 200)->nullable();
            $table->string('mother_phone', 20)->nullable();
            $table->string('guardian_name', 200)->nullable();
            $table->string('guardian_phone', 20)->nullable();
            $table->string('guardian_relationship', 50)->nullable()
                  ->comment('Ex: Oncle, Tuteur légal...');
            $table->text('address')->nullable();
            $table->timestamps();
            $table->softDeletes()
                  ->comment('Jamais supprimé physiquement — désactivation uniquement');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
