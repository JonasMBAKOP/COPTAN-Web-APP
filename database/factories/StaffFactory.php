<?php

namespace Database\Factories;

use App\Models\Staff;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Staff>
 */
class StaffFactory extends Factory
{
    protected $model = Staff::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'gender' => $this->faker->randomElement(['M', 'F']),
            'date_of_birth' => $this->faker->dateTimeBetween('-60 years', '-20 years')->format('Y-m-d'),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'diploma' => $this->faker->randomElement(['BAC', 'Licence', 'Master']),
            'start_date' => now()->subYears(2)->toDateString(),
            'contract_type' => 'permanent',
            'monthly_salary' => 200000,
            'hourly_rate' => 5000,
            'period_rate' => 2000,
            'is_active' => true,
        ];
    }
}
