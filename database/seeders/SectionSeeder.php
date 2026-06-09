<?php

namespace Database\Seeders;

use App\Models\Section;
use Illuminate\Database\Seeder;

class SectionSeeder extends Seeder
{
    public function run(): void
    {
        // Créer les sections par défaut (structure académique camerounaise)
        $sections = [
            ['name' => 'Scientifique', 'code' => 'SCI', 'language' => 'fr'],
            ['name' => 'Littéraire', 'code' => 'LIT', 'language' => 'fr'],
            ['name' => 'Technique', 'code' => 'TECH', 'language' => 'fr'],
            ['name' => 'Scientific', 'code' => 'SCI', 'language' => 'en'],
            ['name' => 'Literary', 'code' => 'LIT', 'language' => 'en'],
        ];

        foreach ($sections as $section) {
            Section::firstOrCreate(
                ['code' => $section['code'], 'language' => $section['language']],
                $section
            );
        }
    }
}
