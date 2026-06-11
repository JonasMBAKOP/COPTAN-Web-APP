<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_enrollments', function (Blueprint $table) {
            $table->string('previous_class_label', 150)->nullable()
                ->after('previous_class_group_id')
                ->comment('Classe précédente (libellé texte si hors BD)');
        });
    }

    public function down(): void
    {
        Schema::table('student_enrollments', function (Blueprint $table) {
            $table->dropColumn('previous_class_label');
        });
    }
};
