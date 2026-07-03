<?php

namespace Database\Factories;

use App\Enums\BillingStatusEnum;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Billing>
 */
class BillingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'bill_no' => strtoupper($this->faker->bothify('????-####')),
            'patient_id' => Patient::factory(),
            'appointment_id' => null,
            'visit_id' => null,
            'medical_order_id' => null,
            'doctor_id' => null,
            'amount' => $this->faker->randomFloat(2, 50, 5000),
            'discount_amount' => 0,
            'status' => BillingStatusEnum::PENDING,
            'billing_date' => now()->toDateString(),
            'notes' => null,
        ];
    }

    public function paid(): static
    {
        return $this->state(fn () => ['status' => BillingStatusEnum::PAID]);
    }

    public function overdue(): static
    {
        return $this->state(fn () => [
            'status' => BillingStatusEnum::OVERDUE,
            'billing_date' => now()->subDays(35)->toDateString(),
        ]);
    }
}
