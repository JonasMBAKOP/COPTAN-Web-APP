<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubjectCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name_fr'     => 'Matières Littéraires',
                'name_en'     => 'Literary Subjects',
                'order_index' => 1,
            ],
            [
                'name_fr'     => 'Matières Scientifiques',
                'name_en'     => 'Scientific Subjects',
                'order_index' => 2,
            ],
            [
                'name_fr'     => 'Autres Matières',
                'name_en'     => 'Other Subjects',
                'order_index' => 3,
            ],
        ];

        foreach ($categories as $category) {
            DB::table('subject_categories')->insertOrIgnore([
                ...$category,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('✅ Catégories de matières créées.');
    }
}
