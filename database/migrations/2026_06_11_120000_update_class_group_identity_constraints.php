<?php

use App\Models\ClassGroup;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('class_groups')->whereNull('series')->update(['series' => '']);
        DB::table('class_groups')->whereNull('sub_group')->update(['sub_group' => '']);

        DB::table('class_groups')
            ->join('levels', 'class_groups.level_id', '=', 'levels.id')
            ->select(
                'class_groups.id',
                'class_groups.name',
                'levels.name as level_name',
                'class_groups.series',
                'class_groups.sub_group'
            )
            ->orderBy('class_groups.id')
            ->get()
            ->each(function ($classGroup) {
                $series = trim((string) $classGroup->series);
                $subGroup = trim((string) $classGroup->sub_group);

                if ($series === '' && $subGroup === '' && Str::startsWith($classGroup->name, $classGroup->level_name)) {
                    $suffix = trim(Str::after($classGroup->name, $classGroup->level_name));

                    if ($suffix !== '' && mb_strlen($suffix) <= 10) {
                        $subGroup = $suffix;
                    }
                }

                DB::table('class_groups')
                    ->where('id', $classGroup->id)
                    ->update([
                        'name' => ClassGroup::composeName(
                            $classGroup->level_name,
                            $series,
                            $subGroup
                        ),
                        'series' => $series,
                        'sub_group' => $subGroup,
                    ]);
            });

        Schema::table('class_groups', function (Blueprint $table) {
            $table->dropUnique(['academic_year_id', 'level_id', 'name']);
            $table->unique(
                ['academic_year_id', 'level_id', 'series', 'sub_group'],
                'class_groups_year_level_series_sub_group_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('class_groups', function (Blueprint $table) {
            $table->dropUnique('class_groups_year_level_series_sub_group_unique');
            $table->unique(['academic_year_id', 'level_id', 'name']);
        });
    }
};
