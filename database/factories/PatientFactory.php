<?php

namespace Database\Factories;

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
            'title' => $this->faker->title(),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'native_name' => $this->faker->firstName(),
            'native_surname' => $this->faker->lastName(),
            'date_of_birth' => $this->faker->date(),
            'identification_number' => $this->faker->numerify('#############'),
            'marital_status' => $this->faker->randomElement(['Single', 'Married', 'Divorced', 'Widowed']),
            'nationality' => $this->faker->country(),
            'religion' => $this->faker->randomElement(['Buddhism', 'Christianity', 'Islam', 'Hinduism', 'Other']),
            'race' => $this->faker->word(),
            'gender' => $this->faker->randomElement(['male', 'female', 'other']),
            'address' => $this->faker->address(),
            'address_building_village' => $this->faker->secondaryAddress(),
            'address_moo' => $this->faker->numerify('##'),
            'address_soi' => $this->faker->streetName(),
            'address_road' => $this->faker->streetName(),
            'address_sub_district' => $this->faker->citySuffix(),
            'address_district' => $this->faker->city(),
            'address_province' => $this->faker->state(),
            'address_zip_code' => $this->faker->postcode(),
            'phone_number' => $this->faker->phoneNumber(),
            'home_phone_number' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'occupation' => $this->faker->jobTitle(),
            'company_name' => $this->faker->company(),
            'company_phone_number' => $this->faker->phoneNumber(),
            'emergency_contact_name' => $this->faker->name(),
            'emergency_contact_relationship' => $this->faker->randomElement(['Spouse', 'Parent', 'Sibling', 'Friend']),
            'emergency_contact_description' => $this->faker->sentence(),
            'emergency_contact_same_address' => $this->faker->boolean(),
            'emergency_contact_address' => $this->faker->address(),
            'emergency_contact_road' => $this->faker->streetName(),
            'emergency_contact_sub_district' => $this->faker->citySuffix(),
            'emergency_contact_district' => $this->faker->city(),
            'emergency_contact_province' => $this->faker->state(),
            'emergency_contact_zip_code' => $this->faker->postcode(),
            'emergency_contact_home_phone' => $this->faker->phoneNumber(),
            'emergency_contact_mobile_phone' => $this->faker->phoneNumber(),
            'emergency_contact_email' => $this->faker->safeEmail(),
            'payment_method' => $this->faker->randomElement(['Cash', 'Credit Card', 'Insurance', 'Corporate Contract']),
            'contract_name' => $this->faker->company(),
            'insurance_name' => $this->faker->company(),
            'insurance_info' => $this->faker->paragraph(),
            'agent_name' => $this->faker->name(),
            'patient_type' => $this->faker->randomElement(['Patient', 'VIP', 'Staff']),
        ];
    }
}
