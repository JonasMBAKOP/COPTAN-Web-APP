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
        Schema::create('appreciation_scales', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique()
                  ->comment('CNA, CMA, CA, CBA, CTBA');
            $table->string('label_fr', 100);
            $table->string('label_en', 100)->nullable();
            $table->decimal('min_grade', 5, 2);
            $table->decimal('max_grade', 5, 2);
            $table->tinyInteger('order_index')->unsigned();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appreciation_scales');
    }
};
