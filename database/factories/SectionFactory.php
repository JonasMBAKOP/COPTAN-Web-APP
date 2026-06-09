<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SectionFactory extends Factory
{
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
