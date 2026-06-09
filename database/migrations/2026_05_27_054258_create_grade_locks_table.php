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
        Schema::create('grade_locks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_group_id')
                  ->constrained('class_groups')
                  ->cascadeOnDelete();
            $table->foreignId('sequence_id')
                  ->constrained('sequences')
                  ->cascadeOnDelete();
            $table->boolean('is_locked')->default(false);
            $table->foreignId('locked_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->timestamp('locked_at')->nullable();
            $table->timestamps();

            // Un seul verrou par classe par séquence
            $table->unique(['class_group_id', 'sequence_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grade_locks');
    }
};
