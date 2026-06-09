<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AppreciationScalesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $scales = [
            [
                'code'        => 'CNA',
                'label_fr'    => 'Compétences Non Acquises',
                'label_en'    => 'Competences Not Acquired',
                'min_grade'   => 0.00,
                'max_grade'   => 9.99,
                'order_index' => 1,
            ],
            [
                'code'        => 'CMA',
                'label_fr'    => 'Compétences Moyennement Acquises',
                'label_en'    => 'Competences Averagely Acquired',
                'min_grade'   => 10.00,
                'max_grade'   => 12.99,
                'order_index' => 2,
            ],
            [
                'code'        => 'CA',
                'label_fr'    => 'Compétences Acquises',
                'label_en'    => 'Competences Acquired',
                'min_grade'   => 13.00,
                'max_grade'   => 14.99,
                'order_index' => 3,
            ],
            [
                'code'        => 'CBA',
                'label_fr'    => 'Compétences Bien Acquises',
                'label_en'    => 'Competences Well Acquired',
                'min_grade'   => 15.00,
                'max_grade'   => 16.99,
                'order_index' => 4,
            ],
            [
                'code'        => 'CTBA',
                'label_fr'    => 'Compétences Très Bien Acquises',
                'label_en'    => 'Competences Very Well Acquired',
                'min_grade'   => 17.00,
                'max_grade'   => 20.00,
                'order_index' => 5,
            ],
        ];

        foreach ($scales as $scale) {
            DB::table('appreciation_scales')->insertOrIgnore([
                ...$scale,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('✅ Barème des appréciations créé.');
    }
}
