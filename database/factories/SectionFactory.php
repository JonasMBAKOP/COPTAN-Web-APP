<?php

namespace Database\Factories;

use App\Models\Section;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Section>
 */
class SectionFactory extends Factory
{
    protected $model = Section::class;

    public function definition(): array
    {
        $languages = ['fr', 'en'];

        return [
            'name' => $this->faker->unique()->word() . ' Section',
            'code' => $this->faker->unique()->bothify('???'),
            'language' => $this->faker->randomElement($languages),
        ];
    }
}
