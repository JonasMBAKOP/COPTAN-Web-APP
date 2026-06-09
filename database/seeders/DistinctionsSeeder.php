<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DistinctionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $distinctions = [
            [
                'code'        => 'HONOR_ROLL',
                'label_fr'    => 'Tableau d\'honneur',
                'label_en'    => 'Honor Roll',
                'type'        => 'positive',
                'order_index' => 1,
            ],
            [
                'code'        => 'ENCOURAGEMENT',
                'label_fr'    => 'Encouragement',
                'label_en'    => 'Encouragement',
                'type'        => 'positive',
                'order_index' => 2,
            ],
            [
                'code'        => 'CONGRATULATION',
                'label_fr'    => 'Félicitation',
                'label_en'    => 'Congratulation',
                'type'        => 'positive',
                'order_index' => 3,
            ],
            [
                'code'        => 'WARNING',
                'label_fr'    => 'Avertissement',
                'label_en'    => 'Warning',
                'type'        => 'negative',
                'order_index' => 4,
            ],
            [
                'code'        => 'SERIOUS_WARNING',
                'label_fr'    => 'Blâme',
                'label_en'    => 'Serious Warning',
                'type'        => 'negative',
                'order_index' => 5,
            ],
            [
                'code'        => 'SUSPENSION',
                'label_fr'    => 'Consigne',
                'label_en'    => 'Suspension',
                'type'        => 'negative',
                'order_index' => 6,
            ],
            [
                'code'        => 'EXCLUSION',
                'label_fr'    => 'Exclusion',
                'label_en'    => 'Exclusion',
                'type'        => 'negative',
                'order_index' => 7,
            ],
        ];

        foreach ($distinctions as $distinction) {
            DB::table('distinctions')->insertOrIgnore([
                ...$distinction,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('✅ Distinctions et sanctions créées.');
    }
}
