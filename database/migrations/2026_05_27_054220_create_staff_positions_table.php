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
        Schema::create('staff_positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')
                  ->constrained('staff')
                  ->cascadeOnDelete();
            $table->enum('position', [
                'enseignant',
                'censeur',
                'prefet_des_etudes',
                'econome',
                'surveillant_general',
                'directeur',
                'fondateur',
                'secretaire',
                'autre'
            ]);
            $table->boolean('is_primary')->default(false)
                  ->comment('Poste principal du membre du personnel');
            $table->timestamps();

            $table->unique(['staff_id', 'position']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_positions');
    }
};
