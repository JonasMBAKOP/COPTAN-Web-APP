<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('discipline_records', function (Blueprint $table) {
            $table->foreignId('academic_year_id')->nullable()->after('id');
            $table->foreignId('student_enrollment_id')->nullable()->after('academic_year_id');
            $table->foreignId('class_group_id')->nullable()->after('student_enrollment_id');
        });

        foreach (DB::table('discipline_records')->cursor() as $record) {
            $enrollment = DB::table('student_enrollments')
                ->where('student_id', $record->student_id)
                ->where('class_group_id', $record->class_id)
                ->where('academic_year_id', $record->school_year_id)
                ->first();

            if ($enrollment) {
                DB::table('discipline_records')
                    ->where('id', $record->id)
                    ->update([
                        'academic_year_id'      => $record->school_year_id,
                        'student_enrollment_id' => $enrollment->id,
                        'class_group_id'        => $record->class_id,
                    ]);
            }
        }

        Schema::table('discipline_records', function (Blueprint $table) {
            $table->foreign('academic_year_id')
                  ->references('id')->on('academic_years')
                  ->cascadeOnDelete();
            $table->foreign('student_enrollment_id')
                  ->references('id')->on('student_enrollments')
                  ->cascadeOnDelete();
            $table->foreign('class_group_id')
                  ->references('id')->on('class_groups')
                  ->cascadeOnDelete();
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE discipline_records MODIFY academic_year_id BIGINT UNSIGNED NOT NULL');
            DB::statement('ALTER TABLE discipline_records MODIFY student_enrollment_id BIGINT UNSIGNED NOT NULL');
            DB::statement('ALTER TABLE discipline_records MODIFY class_group_id BIGINT UNSIGNED NOT NULL');
        }

        if (Schema::hasColumn('discipline_records', 'school_year_id')
            && Schema::hasColumn('discipline_records', 'student_id')
            && Schema::hasColumn('discipline_records', 'class_id')) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            Schema::table('discipline_records', function (Blueprint $table) {
                $table->dropColumn(['school_year_id', 'student_id', 'class_id']);
            });
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }
    }

    public function down(): void
    {
        Schema::table('discipline_records', function (Blueprint $table) {
            $table->foreignId('school_year_id')->nullable()->after('id')->constrained('academic_years')->nullOnDelete();
            $table->foreignId('student_id')->nullable()->after('school_year_id')->constrained('students')->nullOnDelete();
            $table->foreignId('class_id')->nullable()->after('student_id')->constrained('class_groups')->nullOnDelete();
        });

        foreach (DB::table('discipline_records')->cursor() as $record) {
            DB::table('discipline_records')
                ->where('id', $record->id)
                ->update([
                    'school_year_id' => $record->academic_year_id,
                    'student_id'     => DB::table('student_enrollments')->where('id', $record->student_enrollment_id)->value('student_id'),
                    'class_id'       => $record->class_group_id,
                ]);
        }

        if (Schema::hasColumn('discipline_records', 'academic_year_id')
            && Schema::hasColumn('discipline_records', 'student_enrollment_id')
            && Schema::hasColumn('discipline_records', 'class_group_id')) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            Schema::table('discipline_records', function (Blueprint $table) {
                $table->dropColumn(['academic_year_id', 'student_enrollment_id', 'class_group_id']);
            });
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }
    }
};
