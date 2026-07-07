<?php

namespace Database\Seeders;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PatientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $patients = [
            [
                'user' => [
                    'name' => 'John Smith',
                    'email' => 'john.smith@example.com',
                    'password' => Hash::make('password'),
                ],
                'patient' => [
                    'name' => 'John',
                    'surname' => 'Smith',
                    'date_of_birth_day' => 15,
                    'date_of_birth_month' => 3,
                    'date_of_birth_year' => 1985,
                    'gender' => 'Male',
                    'nationality' => 'American',
                    'address' => '123 Main St',
                    'province' => 'IL',
                    'zip_code' => '62701',
                    'mobile_phone' => '+1-555-0101',
                    'email' => 'john.smith@example.com',
                    'payment_method' => 'Insurance',
                    'insurance_name' => 'Blue Cross Blue Shield',
                ],
            ],
            [
                'user' => [
                    'name' => 'Sarah Johnson',
                    'email' => 'sarah.johnson@example.com',
                    'password' => Hash::make('password'),
                ],
                'patient' => [
                    'name' => 'Sarah',
                    'surname' => 'Johnson',
                    'date_of_birth_day' => 22,
                    'date_of_birth_month' => 7,
                    'date_of_birth_year' => 1992,
                    'gender' => 'Female',
                    'nationality' => 'American',
                    'address' => '456 Oak Ave',
                    'province' => 'IL',
                    'zip_code' => '62702',
                    'mobile_phone' => '+1-555-0102',
                    'email' => 'sarah.johnson@example.com',
                    'payment_method' => 'Insurance',
                    'insurance_name' => 'Aetna',
                ],
            ],
            [
                'user' => [
                    'name' => 'Michael Brown',
                    'email' => 'michael.brown@example.com',
                    'password' => Hash::make('password'),
                ],
                'patient' => [
                    'name' => 'Michael',
                    'surname' => 'Brown',
                    'date_of_birth_day' => 8,
                    'date_of_birth_month' => 11,
                    'date_of_birth_year' => 1978,
                    'gender' => 'Male',
                    'nationality' => 'American',
                    'address' => '789 Pine Rd',
                    'province' => 'IL',
                    'zip_code' => '62703',
                    'mobile_phone' => '+1-555-0103',
                    'email' => 'michael.brown@example.com',
                    'payment_method' => 'Insurance',
                    'insurance_name' => 'United Healthcare',
                ],
            ],
            [
                'user' => [
                    'name' => 'Emily Davis',
                    'email' => 'emily.davis@example.com',
                    'password' => Hash::make('password'),
                ],
                'patient' => [
                    'name' => 'Emily',
                    'surname' => 'Davis',
                    'date_of_birth_day' => 30,
                    'date_of_birth_month' => 5,
                    'date_of_birth_year' => 1988,
                    'gender' => 'Female',
                    'nationality' => 'American',
                    'address' => '321 Elm St',
                    'province' => 'IL',
                    'zip_code' => '62704',
                    'mobile_phone' => '+1-555-0104',
                    'email' => 'emily.davis@example.com',
                    'payment_method' => 'Insurance',
                    'insurance_name' => 'Cigna',
                ],
            ],
            [
                'user' => [
                    'name' => 'David Wilson',
                    'email' => 'david.wilson@example.com',
                    'password' => Hash::make('password'),
                ],
                'patient' => [
                    'name' => 'David',
                    'surname' => 'Wilson',
                    'date_of_birth_day' => 12,
                    'date_of_birth_month' => 9,
                    'date_of_birth_year' => 1995,
                    'gender' => 'Male',
                    'nationality' => 'American',
                    'address' => '654 Maple Dr',
                    'province' => 'IL',
                    'zip_code' => '62705',
                    'mobile_phone' => '+1-555-0105',
                    'email' => 'david.wilson@example.com',
                    'payment_method' => 'Insurance',
                    'insurance_name' => 'Humana',
                ],
            ],
        ];

        foreach ($patients as $patientData) {
            // Check if user already exists
            $user = User::where('email', $patientData['user']['email'])->first();

            if (! $user) {
                // Create user if doesn't exist
                $user = User::create($patientData['user']);

                // Assign patient role to user
                $user->assignRole('patient');
            }

            // Check if patient record already exists
            $existingPatient = Patient::where('user_id', $user->id)->first();

            if (! $existingPatient) {
                // Create patient record
                $patientData['patient']['user_id'] = $user->id;
                Patient::create($patientData['patient']);
            }
        }
    }
}
