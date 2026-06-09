<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SectionsAndLevelsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ── SECTIONS ──────────────────────────────────────────────────────────
        $sections = [
            ['name' => 'Enseignement Général',   'code' => 'ESG',  'language' => 'fr'],
            ['name' => 'Enseignement Technique', 'code' => 'EST',  'language' => 'fr'],
            ['name' => 'Anglophone',            'code' => 'ANG', 'language' => 'en'],
        ];

        foreach ($sections as $section) {
            DB::table('sections')->insertOrIgnore([
                ...$section,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $esg = DB::table('sections')->where('code', 'ESG')->value('id');
        $est = DB::table('sections')->where('code', 'EST')->value('id');
        $ang = DB::table('sections')->where('code', 'ANG')->value('id');

        // ── NIVEAUX ───────────────────────────────────────────────────────────

        // Francophone Général
        $levelsESG = [
            ['name' => '6ème',      'order_index' => 1, 'is_exam_class' => false],
            ['name' => '5ème',      'order_index' => 2, 'is_exam_class' => false],
            ['name' => '4ème',      'order_index' => 3, 'is_exam_class' => false],
            ['name' => '3ème',      'order_index' => 4, 'is_exam_class' => true],
            ['name' => '2nde',      'order_index' => 5, 'is_exam_class' => false],
            ['name' => '1ère',      'order_index' => 6, 'is_exam_class' => true],
            ['name' => 'Terminale', 'order_index' => 7, 'is_exam_class' => true],
        ];

        // Francophone Technique
        $levelsEST = [
            ['name' => '1ère Année', 'order_index' => 1, 'is_exam_class' => false],
            ['name' => '2ème Année', 'order_index' => 2, 'is_exam_class' => false],
            ['name' => '3ème Année', 'order_index' => 3, 'is_exam_class' => false],
            ['name' => '4ème Année', 'order_index' => 4, 'is_exam_class' => true],
            ['name' => '2nde',       'order_index' => 5, 'is_exam_class' => false],
            ['name' => '1ère',       'order_index' => 6, 'is_exam_class' => true],
            ['name' => 'Terminale',  'order_index' => 7, 'is_exam_class' => true],
        ];

        // Anglophone
        $levelsANG = [
            ['name' => 'Form 1',      'order_index' => 1, 'is_exam_class' => false],
            ['name' => 'Form 2',      'order_index' => 2, 'is_exam_class' => false],
            ['name' => 'Form 3',      'order_index' => 3, 'is_exam_class' => false],
            ['name' => 'Form 4',      'order_index' => 4, 'is_exam_class' => false],
            ['name' => 'Form 5',      'order_index' => 5, 'is_exam_class' => true],
            ['name' => 'Lower Sixth', 'order_index' => 6, 'is_exam_class' => false],
            ['name' => 'Upper Sixth', 'order_index' => 7, 'is_exam_class' => true],
        ];

        foreach ($levelsESG as $level) {
            DB::table('levels')->insertOrIgnore([
                'section_id'    => $esg,
                'name'          => $level['name'],
                'order_index'   => $level['order_index'],
                'is_exam_class' => $level['is_exam_class'],
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }

        foreach ($levelsEST as $level) {
            DB::table('levels')->insertOrIgnore([
                'section_id'    => $est,
                'name'          => $level['name'],
                'order_index'   => $level['order_index'],
                'is_exam_class' => $level['is_exam_class'],
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }

        foreach ($levelsANG as $level) {
            DB::table('levels')->insertOrIgnore([
                'section_id'    => $ang,
                'name'          => $level['name'],
                'order_index'   => $level['order_index'],
                'is_exam_class' => $level['is_exam_class'],
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }

        $this->command->info('✅ Sections et niveaux créés.');
    }
}
