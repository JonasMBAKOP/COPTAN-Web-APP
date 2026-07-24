<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_payments', function (Blueprint $table) {
            $table->integer('scholarship_amount')->default(0)->after('amount_paid')
                  ->comment('Montant de la bourse appliquée au paiement en bloc');
        });
    }

    public function down(): void
    {
        Schema::table('student_payments', function (Blueprint $table) {
            $table->dropColumn('scholarship_amount');
        });
    }
};
