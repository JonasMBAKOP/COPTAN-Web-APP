<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('staff')
            ->whereNotNull('user_id')
            ->orderBy('id')
            ->each(function (object $staff): void {
                DB::table('users')->where('id', $staff->user_id)->update([
                    'name' => mb_strtoupper(trim($staff->last_name . ' ' . $staff->first_name)),
                ]);
            });
    }

    public function down(): void
    {
        // Name casing is a data normalization and is intentionally irreversible.
    }
};
