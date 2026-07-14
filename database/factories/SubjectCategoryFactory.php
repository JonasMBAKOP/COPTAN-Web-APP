<?php

namespace Database\Factories;

use App\Models\SubjectCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SubjectCategory>
 */
class SubjectCategoryFactory extends Factory
{
    protected $model = SubjectCategory::class;

    public function definition(): array
    {
        return [
            'name_fr' => $this->faker->word(),
            'name_en' => $this->faker->word(),
            'order_index' => $this->faker->numberBetween(1, 10),
        ];
    }
}
