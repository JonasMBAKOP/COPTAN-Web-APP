<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\ClassGroup;
use App\Models\Level;
use App\Models\Staff;
use Illuminate\Database\Seeder;

class ClassGroupSeeder extends Seeder
{
    public function run(): void
    {
        $activeYear = AcademicYear::where('is_active', true)->first();

        if (!$activeYear) {
            return;
        }

        // Récupérer les niveaux
        $levels = Level::with('section')->get();
        $staff = Staff::where('is_active', true)->get();

        if ($staff->isEmpty()) {
            return;
        }

        // Pour chaque niveau, créer 2-3 classes
        foreach ($levels as $level) {
            $numClasses = rand(2, 3);
            $classNames = ['A', 'B', 'C'];

            for ($i = 0; $i < $numClasses; $i++) {
                ClassGroup::firstOrCreate(
                    [
                        'academic_year_id' => $activeYear->id,
                        'level_id' => $level->id,
                        'name' => $level->name . ' ' . $classNames[$i],
                    ],
                    [
                        'academic_year_id' => $activeYear->id,
                        'level_id' => $level->id,
                        'name' => $level->name . ' ' . $classNames[$i],
                        'max_students' => rand(40, 60),
                        'titular_staff_id' => $staff->random()->id,
                        'room' => 'Room ' . rand(101, 210),
                    ]
                );
            }
        }
    }
}
