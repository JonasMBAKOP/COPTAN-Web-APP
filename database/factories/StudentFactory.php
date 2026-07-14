<?php

namespace Database\Factories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        return [
            'matricule' => 'CP' . now()->year . str_pad((string) $this->faker->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'gender' => $this->faker->randomElement(['M', 'F']),
            'date_of_birth' => $this->faker->dateTimeBetween('-20 years', '-5 years')->format('Y-m-d'),
            'place_of_birth' => $this->faker->city(),
            'birth_certificate_number' => $this->faker->unique()->numerify('BC####'),
            'nationality' => 'Camerounaise',
            'father_name' => $this->faker->name('male'),
            'father_phone' => $this->faker->phoneNumber(),
            'mother_name' => $this->faker->name('female'),
            'mother_phone' => $this->faker->phoneNumber(),
            'guardian_name' => $this->faker->name(),
            'guardian_phone' => $this->faker->phoneNumber(),
            'guardian_relationship' => 'Tuteur',
            'address' => $this->faker->address(),
        ];
    }
}
