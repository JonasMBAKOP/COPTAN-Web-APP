<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CouncilDecisionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $decisions = [
            [
                'label_fr'          => 'Admis(e) en classe supérieure',
                'label_en'          => 'Promoted to next class',
                'exam_classes_only' => false,
                'is_active'         => true,
                'order_index'       => 1,
            ],
            [
                'label_fr'          => 'Redoublement',
                'label_en'          => 'Repeating the class',
                'exam_classes_only' => false,
                'is_active'         => true,
                'order_index'       => 2,
            ],
            [
                'label_fr'          => 'Redouble si Échec',
                'label_en'          => 'Repeating if Failed',
                'exam_classes_only' => true,
                'is_active'         => true,
                'order_index'       => 3,
            ],
            [
                'label_fr'          => 'Exclusion Définitive',
                'label_en'          => 'Definitive Exclusion',
                'exam_classes_only' => false,
                'is_active'         => true,
                'order_index'       => 4,
            ],
        ];

        foreach ($decisions as $decision) {
            DB::table('council_decisions')->insertOrIgnore([
                ...$decision,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('✅ Décisions du conseil de classe créées.');
    }
}
