<?php

namespace Database\Factories;

use App\Models\Staff;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Patient>
 */
class PatientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->randomElement(['Mr.', 'Mrs.', 'Ms.']),
            'name' => $this->faker->firstName(),
            'surname' => $this->faker->lastName(),
            'khmer_china_name' => $this->faker->name(),
            'khmer_china_surname' => $this->faker->lastName(),
            'date_of_birth_day' => $this->faker->numberBetween(1, 28),
            'date_of_birth_month' => $this->faker->numberBetween(1, 12),
            'date_of_birth_year' => $this->faker->numberBetween(1950, 2005),
            'gender' => $this->faker->randomElement(['Male', 'Female', 'Other']),
            'id_card_or_passport' => $this->faker->bothify('##########'),
            'marital_status' => $this->faker->randomElement(['Single', 'Married', 'Divorced', 'Widowed']),
            'nationality' => $this->faker->country(),
            'religion' => $this->faker->word(),
            'race' => $this->faker->word(),

            // Address
            'address' => $this->faker->streetAddress(),
            'building_village' => $this->faker->word(),
            'moo' => $this->faker->word(),
            'soi' => $this->faker->word(),
            'road' => $this->faker->streetName(),
            'sub_district' => $this->faker->city(),
            'district' => $this->faker->city(),
            'province' => $this->faker->state(),
            'zip_code' => $this->faker->postcode(),

            // Contact
            'home_phone' => $this->faker->phoneNumber(),
            'mobile_phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->safeEmail(),
            'occupation' => $this->faker->jobTitle(),
            'company_name' => $this->faker->company(),
            'company_phone' => $this->faker->phoneNumber(),

            // Emergency Contact
            'emergency_contact_name' => $this->faker->name(),
            'emergency_contact_relationship' => $this->faker->randomElement(['Spouse', 'Parent', 'Sibling', 'Friend', 'Other']),
            'emergency_contact_description_other' => $this->faker->sentence(),
            'emergency_contact_address_same_as_patient' => $this->faker->boolean(),
            'emergency_contact_address' => $this->faker->address(),
            'emergency_contact_road' => $this->faker->streetName(),
            'emergency_contact_sub_district' => $this->faker->city(),
            'emergency_contact_district' => $this->faker->city(),
            'emergency_contact_province' => $this->faker->state(),
            'emergency_contact_zip_code' => $this->faker->postcode(),
            'emergency_contact_home_phone' => $this->faker->phoneNumber(),
            'emergency_contact_mobile_phone' => $this->faker->phoneNumber(),
            'emergency_contact_email' => $this->faker->safeEmail(),

            // Payment
            'payment_method' => $this->faker->randomElement(['Corporate Contract', 'Self-Pay', 'Insurance']),
            'contract_name' => $this->faker->company(),
            'insurance_name' => $this->faker->company(),
            'staff_id' => Staff::factory(),
            'patient_type' => $this->faker->randomElement(['Patient', 'Customer', 'Dependent']),
        ];
    }
}
