<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Permission\Models\Role;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Staff>
 */
class StaffFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'role_id' => Role::firstOrCreate(['name' => 'staff', 'guard_name' => 'web'])->id,
            'department_id' => null,
            'contact_number' => $this->faker->phoneNumber(),
            'hire_date' => $this->faker->date(),
        ];
    }
}
