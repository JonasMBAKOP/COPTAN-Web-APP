<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }

        if (Schema::hasTable('class_subjects')) {
            DB::statement("ALTER TABLE class_subjects MODIFY coefficient DECIMAL(3,1) UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Coefficient entre 0.5 et 9';");
        }

        if (Schema::hasTable('bulletin_subject_details')) {
            DB::statement("ALTER TABLE bulletin_subject_details MODIFY coefficient DECIMAL(3,1) UNSIGNED NOT NULL COMMENT 'Coefficient copié au moment de la génération';");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }

        if (Schema::hasTable('bulletin_subject_details')) {
            DB::statement("ALTER TABLE bulletin_subject_details MODIFY coefficient TINYINT UNSIGNED NOT NULL COMMENT 'Coefficient copié au moment de la génération';");
        }

        if (Schema::hasTable('class_subjects')) {
            DB::statement("ALTER TABLE class_subjects MODIFY coefficient TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Entre 1 et 9';");
        }
    }
};
