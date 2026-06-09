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
        Schema::create('distinctions', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique()
                  ->comment('Ex: HONOR_ROLL, WARNING, SUSPENSION');
            $table->string('label_fr', 100);
            $table->string('label_en', 100)->nullable();
            $table->enum('type', ['positive', 'negative']);
            $table->tinyInteger('order_index')->unsigned()->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distinctions');
    }
};
