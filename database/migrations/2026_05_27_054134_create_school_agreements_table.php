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
        Schema::create('school_agreements', function (Blueprint $table) {
            $table->id();
            $table->string('number', 100);
            $table->enum('cycle', [
                'premier_cycle',
                'second_cycle',
                'autre'
            ]);
            $table->string('label', 200)->nullable()
                  ->comment('Description optionnelle du numéro d\'agrément');
            $table->date('issued_date')->nullable()
                  ->comment('Date de délivrance');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_agreements');
    }
};
