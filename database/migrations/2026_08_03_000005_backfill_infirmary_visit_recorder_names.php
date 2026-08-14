<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('infirmary_visits')
            ->join('staff', 'staff.id', '=', 'infirmary_visits.recorded_by_staff_id')
            ->whereNull('infirmary_visits.recorded_by_name')
            ->update([
                'recorded_by_name' => DB::raw("TRIM(CONCAT(COALESCE(staff.last_name, ''), ' ', COALESCE(staff.first_name, '')))"),
            ]);
    }

    public function down(): void
    {
        // Historical recorder names are preserved when rolling back this migration.
    }
};
