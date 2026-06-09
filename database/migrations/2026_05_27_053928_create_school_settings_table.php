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
        Schema::create('school_settings', function (Blueprint $table) {
            $table->id();
            $table->string('full_name', 200);
            $table->string('short_name', 50);
            $table->string('logo', 255)->nullable();
            $table->text('address')->nullable();
            $table->string('postal_box', 20)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('region', 100)->nullable();
            $table->string('email', 191)->nullable();
            $table->string('website', 200)->nullable();
            $table->string('motto', 255)->nullable();
            $table->string('order_type', 100)->nullable()
                  ->comment('Privé Laïc, Catholique, Protestant...');
            $table->string('ministry', 200)->nullable()
                  ->default('Ministère des Enseignements Secondaires');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_settings');
    }
};
