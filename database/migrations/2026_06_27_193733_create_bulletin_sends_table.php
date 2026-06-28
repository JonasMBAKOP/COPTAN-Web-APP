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
        Schema::create('bulletin_sends', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bulletin_report_id')->nullable()->constrained();
            $table->foreignId('student_enrollment_id')->constrained();
            $table->foreignId('sent_by')->constrained('users');
            $table->string('phone_number');
            $table->enum('status', ['pending','sent','failed','no_whatsapp'])->default('pending');
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
        Schema::dropIfExists('bulletin_sends');
    }
};
