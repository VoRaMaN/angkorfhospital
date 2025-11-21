<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Appointment>
 */
class AppointmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'staff_id' => Staff::factory(),
            'appointment_date_time' => $this->faker->dateTimeBetween('now', '+1 month'),
            'status' => $this->faker->randomElement(['scheduled', 'confirmed', 'arrived', 'in_progress', 'completed', 'cancelled', 'no_show', 'rescheduled']),
            'reason_for_visit' => $this->faker->sentence(),
        ];
    }
}
