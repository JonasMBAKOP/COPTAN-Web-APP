<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('timetable_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('period_duration_minutes')->default(60);
            $table->unsignedTinyInteger('max_periods_per_day')->default(8);
            $table->json('day_configs')->nullable();
            $table->json('breaks')->nullable();
            $table->timestamps();
        });

        Schema::table('timetable_slots', function (Blueprint $table) {
            $table->unsignedTinyInteger('period_index')->nullable()->after('day_of_week');
            $table->unsignedTinyInteger('periods_count')->default(1)->after('period_index');
        });
    }

    public function down(): void
    {
        Schema::table('timetable_slots', function (Blueprint $table) {
            $table->dropColumn(['period_index', 'periods_count']);
        });

        Schema::dropIfExists('timetable_settings');
    }
};
