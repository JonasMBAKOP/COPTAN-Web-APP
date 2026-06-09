<?php

namespace Database\Factories;

use App\Models\AcademicYear;
use App\Models\Level;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClassGroupFactory extends Factory
{
    public function definition(): array
    {
        return [
            'academic_year_id' => AcademicYear::factory(),
            'level_id' => Level::factory(),
            'name' => $this->faker->randomElement(['A', 'B', 'C', 'D']) . $this->faker->numberBetween(1, 3),
            'sub_group' => $this->faker->boolean(50) ? $this->faker->bothify('?#') : null,
            'series' => $this->faker->boolean(40) ? $this->faker->randomElement(['C', 'D', 'E', 'S1', 'S2', 'L', 'A']) : null,
            'max_students' => $this->faker->numberBetween(40, 60),
            'titular_staff_id' => Staff::inRandomOrder()->first()?->id ?? Staff::factory(),
            'room' => $this->faker->numerify('Room ###'),
        ];
    }
}
