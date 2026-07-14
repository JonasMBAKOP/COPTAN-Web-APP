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
        Schema::table('staff', function (Blueprint $table) {
            $table->unsignedBigInteger('monthly_salary')->nullable()->after('contract_type');
            $table->unsignedBigInteger('hourly_rate')->nullable()->after('monthly_salary');
            $table->unsignedBigInteger('period_rate')->nullable()->after('hourly_rate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->dropColumn(['monthly_salary', 'hourly_rate', 'period_rate']);
        });
    }
};
