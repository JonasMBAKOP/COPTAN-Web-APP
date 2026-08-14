<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('infirmary_visits')
            ->where(function ($query) {
                $query->whereNull('recorded_by_name')
                    ->orWhere('recorded_by_name', '');
            })
            ->whereNull('recorded_by_staff_id')
            ->update([
                'recorded_by_name' => 'Non renseigne (consultation anterieure)',
            ]);
    }

    public function down(): void
    {
        DB::table('infirmary_visits')
            ->where('recorded_by_name', 'Non renseigne (consultation anterieure)')
            ->update(['recorded_by_name' => null]);
    }
};