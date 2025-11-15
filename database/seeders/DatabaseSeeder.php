<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SpatiePermissionSeeder::class,
            UserSeeder::class,
            RoleSeeder::class,
            DepartmentSeeder::class,
            StaffSeeder::class,
            PatientSeeder::class,
            AppointmentSeeder::class,
            MedicalRecordSeeder::class,
            BillingSeeder::class,
            InventorySeeder::class,
            LabPackagesSeeder::class,
            VisitSeeder::class,
        ]);
    }
}
