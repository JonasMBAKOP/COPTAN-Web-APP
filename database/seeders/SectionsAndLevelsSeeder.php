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
            ['name' => 'Anglophone Technique',  'code' => 'EAT', 'language' => 'en'],
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
        $eat = DB::table('sections')->where('code', 'EAT')->value('id');

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
            ['name' => 'Form 1',      'order_index' => 1, 'is_exam_class' => false, 'cycle' => '1er'],
            ['name' => 'Form 2',      'order_index' => 2, 'is_exam_class' => false, 'cycle' => '1er'],
            ['name' => 'Form 3',      'order_index' => 3, 'is_exam_class' => false, 'cycle' => '1er'],
            ['name' => 'Form 4',      'order_index' => 4, 'is_exam_class' => false, 'cycle' => '1er'],
            ['name' => 'Form 5',      'order_index' => 5, 'is_exam_class' => true,  'cycle' => '2nd'],
            ['name' => 'Lower Sixth', 'order_index' => 6, 'is_exam_class' => false, 'cycle' => '2nd'],
            ['name' => 'Upper Sixth', 'order_index' => 7, 'is_exam_class' => true,  'cycle' => '2nd'],
        ];

        // Anglophone Technique
        $levelsEAT = [
            ['name' => 'Form 1',      'order_index' => 1, 'is_exam_class' => false, 'cycle' => '1er'],
            ['name' => 'Form 2',      'order_index' => 2, 'is_exam_class' => false, 'cycle' => '1er'],
            ['name' => 'Form 3',      'order_index' => 3, 'is_exam_class' => false, 'cycle' => '1er'],
            ['name' => 'Form 4',      'order_index' => 4, 'is_exam_class' => false, 'cycle' => '1er'],
            ['name' => 'Form 5',      'order_index' => 5, 'is_exam_class' => true,  'cycle' => '2nd'],
            ['name' => 'Lower Sixth', 'order_index' => 6, 'is_exam_class' => false, 'cycle' => '2nd'],
            ['name' => 'Upper Sixth', 'order_index' => 7, 'is_exam_class' => true,  'cycle' => '2nd'],
        ];

        foreach ($levelsESG as $level) {
            $updated = DB::table('levels')
                ->where('section_id', $esg)
                ->where('name', $level['name'])
                ->update([
                    'order_index'   => $level['order_index'],
                    'is_exam_class' => $level['is_exam_class'],
                    'updated_at'    => now(),
                ]);
            
            if ($updated === 0) {
                DB::table('levels')->insert([
                    'section_id'    => $esg,
                    'name'          => $level['name'],
                    'order_index'   => $level['order_index'],
                    'is_exam_class' => $level['is_exam_class'],
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }
        }

        foreach ($levelsEST as $level) {
            $updated = DB::table('levels')
                ->where('section_id', $est)
                ->where('name', $level['name'])
                ->update([
                    'order_index'   => $level['order_index'],
                    'is_exam_class' => $level['is_exam_class'],
                    'updated_at'    => now(),
                ]);
            
            if ($updated === 0) {
                DB::table('levels')->insert([
                    'section_id'    => $est,
                    'name'          => $level['name'],
                    'order_index'   => $level['order_index'],
                    'is_exam_class' => $level['is_exam_class'],
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }
        }

        foreach ($levelsANG as $level) {
            $updated = DB::table('levels')
                ->where('section_id', $ang)
                ->where('name', $level['name'])
                ->update([
                    'cycle'         => $level['cycle'],
                    'order_index'   => $level['order_index'],
                    'is_exam_class' => $level['is_exam_class'],
                    'updated_at'    => now(),
                ]);
            
            if ($updated === 0) {
                DB::table('levels')->insert([
                    'section_id'    => $ang,
                    'name'          => $level['name'],
                    'cycle'         => $level['cycle'],
                    'order_index'   => $level['order_index'],
                    'is_exam_class' => $level['is_exam_class'],
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }
        }

        foreach ($levelsEAT as $level) {
            $updated = DB::table('levels')
                ->where('section_id', $eat)
                ->where('name', $level['name'])
                ->update([
                    'cycle'         => $level['cycle'],
                    'order_index'   => $level['order_index'],
                    'is_exam_class' => $level['is_exam_class'],
                    'updated_at'    => now(),
                ]);
            
            if ($updated === 0) {
                DB::table('levels')->insert([
                    'section_id'    => $eat,
                    'name'          => $level['name'],
                    'cycle'         => $level['cycle'],
                    'order_index'   => $level['order_index'],
                    'is_exam_class' => $level['is_exam_class'],
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }
        }

        $this->command->info('✅ Sections et niveaux créés.');
    }
}
