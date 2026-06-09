<?php

namespace Database\Seeders;

use App\Models\Level;
use App\Models\Section;
use Illuminate\Database\Seeder;

class LevelSeeder extends Seeder
{
    public function run(): void
    {
        // Français levels
        $frenchLevels = [
            ['name' => '6ème', 'order_index' => 1, 'is_exam_class' => false],
            ['name' => '5ème', 'order_index' => 2, 'is_exam_class' => false],
            ['name' => '4ème', 'order_index' => 3, 'is_exam_class' => false],
            ['name' => '3ème', 'order_index' => 4, 'is_exam_class' => true],
            ['name' => '2nde', 'order_index' => 5, 'is_exam_class' => false],
            ['name' => '1ère', 'order_index' => 6, 'is_exam_class' => false],
            ['name' => 'Terminale', 'order_index' => 7, 'is_exam_class' => true],
        ];

        $englishLevels = [
            ['name' => 'Form 1', 'order_index' => 1, 'is_exam_class' => false],
            ['name' => 'Form 2', 'order_index' => 2, 'is_exam_class' => false],
            ['name' => 'Form 3', 'order_index' => 3, 'is_exam_class' => false],
            ['name' => 'Form 4', 'order_index' => 4, 'is_exam_class' => true],
            ['name' => 'Form 5', 'order_index' => 5, 'is_exam_class' => false],
            ['name' => 'Form 6', 'order_index' => 6, 'is_exam_class' => true],
        ];

        // Récupérer les sections
        $scientificFr = Section::where('code', 'SCI')->where('language', 'fr')->first();
        $literaryFr = Section::where('code', 'LIT')->where('language', 'fr')->first();
        $scientificEn = Section::where('code', 'SCI')->where('language', 'en')->first();

        // Ajouter les niveaux français
        if ($scientificFr) {
            foreach ($frenchLevels as $level) {
                Level::firstOrCreate(
                    ['section_id' => $scientificFr->id, 'name' => $level['name']],
                    array_merge(['section_id' => $scientificFr->id], $level)
                );
            }
        }

        if ($literaryFr) {
            foreach ($frenchLevels as $level) {
                Level::firstOrCreate(
                    ['section_id' => $literaryFr->id, 'name' => $level['name']],
                    array_merge(['section_id' => $literaryFr->id], $level)
                );
            }
        }

        // Ajouter les niveaux anglais
        if ($scientificEn) {
            foreach ($englishLevels as $level) {
                Level::firstOrCreate(
                    ['section_id' => $scientificEn->id, 'name' => $level['name']],
                    array_merge(['section_id' => $scientificEn->id], $level)
                );
            }
        }
    }
}
