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
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete()
                  ->comment('Nullable : tous les membres n\'ont pas forcément un compte');
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->enum('gender', ['M', 'F']);
            $table->date('date_of_birth')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email', 191)->nullable();
            $table->string('photo', 255)->nullable()
                  ->comment('Chemin vers le fichier image, pas l\'image elle-même');
            $table->enum('diploma', [
                'BEPC', 'BAC', 'Licence',
                'Master', 'Doctorat', 'Autre'
            ])->nullable();
            $table->date('start_date')->nullable()
                  ->comment('Date d\'entrée dans l\'établissement');
            $table->enum('contract_type', [
                'permanent', 'vacataire', 'stagiaire'
            ])->default('permanent');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
