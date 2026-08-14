<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('staff')) {
            return;
        }

        DB::statement('ALTER TABLE `staff` MODIFY COLUMN `contract_type` ENUM("permanent", "vacataire", "semi_permanent", "stagiaire") NOT NULL DEFAULT "permanent"');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('staff')) {
            return;
        }

        DB::statement('ALTER TABLE `staff` MODIFY COLUMN `contract_type` ENUM("permanent", "vacataire", "stagiaire") NOT NULL DEFAULT "permanent"');
    }
};
