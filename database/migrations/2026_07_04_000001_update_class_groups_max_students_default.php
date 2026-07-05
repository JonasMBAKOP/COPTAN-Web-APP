<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('class_groups', function (Blueprint $table) {
            $table->tinyInteger('max_students')->unsigned()->default(100)->change();
        });
    }

    public function down(): void
    {
        Schema::table('class_groups', function (Blueprint $table) {
            $table->tinyInteger('max_students')->unsigned()->default(60)->change();
        });
    }
};
