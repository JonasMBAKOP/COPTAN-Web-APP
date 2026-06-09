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
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('author_id')
                  ->constrained('users')
                  ->cascadeOnDelete();
            $table->string('title', 200);
            $table->text('content');
            $table->enum('category', [
                'pedagogique',
                'administratif',
                'financier',
                'evenement'
            ]);
            $table->json('target_roles')->nullable()
                  ->comment('Ex: ["directeur","enseignant"] — NULL = tout le monde');
            $table->boolean('is_pinned')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
