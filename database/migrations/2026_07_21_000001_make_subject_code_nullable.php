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
        if (Schema::hasColumn('subjects', 'code')) {
            $driver = DB::getDriverName();

            if ($driver === 'mysql') {
                DB::statement("ALTER TABLE `subjects` MODIFY COLUMN `code` VARCHAR(20) NULL COMMENT 'Ex: MATH, FRAN, EPS, ANG'");
            } elseif ($driver === 'pgsql') {
                DB::statement("ALTER TABLE subjects ALTER COLUMN code DROP NOT NULL");
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('subjects', 'code')) {
            $driver = DB::getDriverName();

            if ($driver === 'mysql') {
                DB::statement("ALTER TABLE `subjects` MODIFY COLUMN `code` VARCHAR(20) NOT NULL COMMENT 'Ex: MATH, FRAN, EPS, ANG'");
            } elseif ($driver === 'pgsql') {
                DB::statement("ALTER TABLE subjects ALTER COLUMN code SET NOT NULL");
            }
        }
    }
};
