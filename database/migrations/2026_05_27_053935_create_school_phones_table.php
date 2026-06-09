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
        Schema::create('school_phones', function (Blueprint $table) {
            $table->id();
            $table->string('number', 30);
            $table->string('label', 50)->nullable()
                  ->comment('Ex: Secrétariat, Direction, WhatsApp');
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_phones');
    }
};
