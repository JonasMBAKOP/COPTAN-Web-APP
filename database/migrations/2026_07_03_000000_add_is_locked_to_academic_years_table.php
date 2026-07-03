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
        Schema::table('academic_years', function (Blueprint $table) {
            if (! Schema::hasColumn('academic_years', 'is_locked')) {
                $table->boolean('is_locked')->default(false)
                      ->after('is_active')
                      ->comment('Flag manuel pour indiquer qu\'une année est clôturée.');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('academic_years', function (Blueprint $table) {
            if (Schema::hasColumn('academic_years', 'is_locked')) {
                $table->dropColumn('is_locked');
            }
        });
    }
};
