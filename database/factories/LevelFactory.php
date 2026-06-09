<?php

namespace Database\Factories;

use App\Models\Section;
use Illuminate\Database\Eloquent\Factories\Factory;

class LevelFactory extends Factory
{
    public function definition(): array
    {
        return [
            'section_id' => Section::factory(),
            'name' => $this->faker->unique()->randomElement(['6ème', '5ème', '4ème', '3ème', '2nde', '1ère', 'Terminale']),
            'order_index' => $this->faker->numberBetween(1, 12),
            'is_exam_class' => $this->faker->boolean(30),
        ];
    }
}
