<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            ['name' => 'Cardiology', 'description' => 'Heart and blood vessel related treatments.'],
            ['name' => 'Neurology', 'description' => 'Brain and nervous system related treatments.'],
            ['name' => 'Pediatrics', 'description' => 'Medical care for infants, children, and adolescents.'],
            ['name' => 'Orthopedics', 'description' => 'Musculoskeletal system related treatments.'],
            ['name' => 'Dermatology', 'description' => 'Skin related treatments.'],
            ['name' => 'Gynecology', 'description' => 'Women\'s reproductive health.'],
            ['name' => 'Oncology', 'description' => 'Cancer related treatments.'],
            ['name' => 'Emergency', 'description' => 'Immediate treatment for acute illnesses and injuries.'],
        ];
        foreach ($departments as $department) {
            \App\Models\Department::create($department);
        }
    }
}
