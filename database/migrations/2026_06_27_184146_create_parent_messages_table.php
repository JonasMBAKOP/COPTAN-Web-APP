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
        Schema::create('parent_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users');
            $table->string('subject')->nullable();
            $table->text('body');
            $table->enum('channel', ['sms', 'whatsapp', 'both'])->default('both');
            $table->enum('target_type', ['all', 'selected', 'class', 'bulletin']);
            $table->foreignId('class_group_id')->nullable()->constrained();
            $table->integer('total_recipients')->default(0);
            $table->integer('sent_count')->default(0);
            $table->integer('failed_count')->default(0);
            $table->enum('status', ['pending', 'sending', 'completed', 'failed'])->default('pending');
            $table->timestamps();
        });

        Schema::create('parent_message_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_message_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained();
            $table->string('phone_number');
            $table->string('recipient_type')->nullable(); // père/mère/tuteur
            $table->enum('sms_status', ['pending','sent','failed','skipped'])->default('pending');
            $table->enum('whatsapp_status', ['pending','sent','failed','skipped'])->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parent_messages');
    }
};
