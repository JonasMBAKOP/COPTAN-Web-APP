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
        Schema::table('school_settings', function (Blueprint $table) {
            $table->string('full_name_en')->nullable()->after('full_name');
            $table->string('motto_en')->nullable()->after('motto');
            $table->string('address_en')->nullable()->after('address');
            $table->string('ministry_en')->nullable()->after('ministry');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('school_settings', function (Blueprint $table) {
            $table->dropColumn(['full_name_en', 'motto_en', 'address_en', 'ministry_en']);
        });
    }
};
