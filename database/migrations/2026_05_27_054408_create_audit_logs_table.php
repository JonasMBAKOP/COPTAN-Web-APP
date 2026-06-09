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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete()
                  ->comment('NULL si action système automatique');
            $table->string('action', 50)
                  ->comment('created, updated, deleted, login, logout, locked_grades...');
            $table->string('model_type', 100)->nullable()
                  ->comment('Ex: Student, Grade, Payment...');
            $table->unsignedBigInteger('model_id')->nullable()
                  ->comment('ID de l\'enregistrement concerné');
            $table->json('old_values')->nullable()
                  ->comment('Valeurs avant modification');
            $table->json('new_values')->nullable()
                  ->comment('Valeurs après modification');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
