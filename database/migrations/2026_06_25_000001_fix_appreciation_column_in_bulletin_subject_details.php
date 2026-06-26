<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Fix: la colonne 'appreciation' de bulletin_subject_details était VARCHAR(10),
 * ce qui est trop court pour les labels comme "Compétences Acquises".
 *
 * La stratégie adoptée est de stocker le CODE court (ex: 'CA', 'CNA')
 * dans cette colonne. Mais on l'agrandit à 20 caractères comme filet
 * de sécurité, au cas où les codes seraient personnalisés.
 *
 * Pour bulletin_reports.general_observation, la colonne est déjà TEXT → OK.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bulletin_subject_details', function (Blueprint $table) {
            // On agrandit appreciation : VARCHAR(10) → VARCHAR(20)
            // Cela permet des codes jusqu'à 20 caractères.
            $table->string('appreciation', 20)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('bulletin_subject_details', function (Blueprint $table) {
            $table->string('appreciation', 10)->nullable()->change();
        });
    }
};
