<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SpatiePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        $permissions = [
            // User management
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',

            // Staff management
            'view_staff',
            'create_staff',
            'edit_staff',
            'delete_staff',

            // Doctor management
            'view_doctors',
            'create_doctors',
            'edit_doctors',
            'delete_doctors',

            // Patient management
            'view_patients',
            'create_patients',
            'edit_patients',
            'delete_patients',

            // Appointment management
            'view_appointments',
            'create_appointments',
            'edit_appointments',
            'delete_appointments',

            // Medical records
            'view_medical_records',
            'create_medical_records',
            'edit_medical_records',
            'delete_medical_records',

            // Medical services
            'view_medical_services',
            'create_medical_services',
            'edit_medical_services',
            'delete_medical_services',

            // Medical orders
            'view_medical_orders',
            'create_medical_orders',
            'edit_medical_orders',
            'process_medical_orders',
            'complete_medical_orders',
            'delete_medical_orders',

            // Medications
            'view_medications',
            'create_medications',
            'edit_medications',
            'delete_medications',

            // Visits
            'view_visits',
            'create_visits',
            'edit_visits',
            'delete_visits',

            // Billing
            'view_billing',
            'create_billing',
            'edit_billing',
            'delete_billing',
            'update_billing_status',

            // Inventory
            'view_inventory',
            'create_inventory',
            'edit_inventory',
            'delete_inventory',

            // Lab packages
            'view_lab_packages',
            'create_lab_packages',
            'edit_lab_packages',
            'delete_lab_packages',

            // Department management
            'view_departments',
            'create_departments',
            'edit_departments',
            'delete_departments',

            // Role management
            'view_roles',
            'create_roles',
            'edit_roles',
            'delete_roles',

            // System settings
            'view_settings',
            'edit_settings',

            // File management
            'view_files',
            'create_files',
            'edit_files',
            'delete_files',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions($permissions); // Admin gets all permissions

        $doctorRole = Role::firstOrCreate(['name' => 'doctor']);
        $doctorRole->syncPermissions([
            'view_users',
            'view_staff',
            'view_doctors',
            'view_patients',
            'create_patients',
            'edit_patients',
            'view_appointments',
            'create_appointments',
            'edit_appointments',
            'view_medical_records',
            'create_medical_records',
            'edit_medical_records',
            'view_medical_orders',
            'create_medical_orders',
            'edit_medical_orders',
            'process_medical_orders',
            'complete_medical_orders',
            'view_medical_services',
            'create_medical_services',
            'edit_medical_services',
            'view_medications',
            'view_visits',
            'create_visits',
            'edit_visits',
            'view_billing',
            'view_files',
            'create_files',
            'edit_files',
        ]);

        $staffRole = Role::firstOrCreate(['name' => 'nurse']);
        $staffRole->syncPermissions([
            'view_users',
            'view_staff',
            'view_patients',
            'create_patients',
            'edit_patients',
            'view_appointments',
            'create_appointments',
            'edit_appointments',
            'view_medical_records',
            'view_medical_orders',
            'create_medical_orders',
            'edit_medical_orders',
            'process_medical_orders',
            'complete_medical_orders',
            'view_medical_services',
            'create_medical_services',
            'edit_medical_services',
            'view_medications',
            'view_visits',
            'create_visits',
            'edit_visits',
            'view_billing',
            'create_billing',
            'edit_billing',
            'view_inventory',
            'create_inventory',
            'edit_inventory',
            'view_lab_packages',
            'create_lab_packages',
            'edit_lab_packages',
            'view_files',
            'create_files',
            'edit_files',
            'delete_files',
        ]);

        $patientRole = Role::firstOrCreate(['name' => 'patient']);
        $patientRole->syncPermissions([
            'view_appointments', // Can view their own appointments
            'view_medical_records', // Can view their own records
            'view_medical_orders', // Can view their own medical orders
            'view_visits', // Can view their own visits
            'view_billing', // Can view their own billing
            'view_files', // Can view their own files
        ]);

        $accountantRole = Role::firstOrCreate(['name' => 'accountant']);
        $accountantRole->syncPermissions([
            'view_billing',
            'create_billing',
            'edit_billing',
            'delete_billing',
            'update_billing_status',
            'view_inventory',
            'view_visits',
        ]);

        $receptionistRole = Role::firstOrCreate(['name' => 'receptionist']);
        $receptionistRole->syncPermissions([
            'view_users',
            'view_patients',
            'create_patients',
            'edit_patients',
            'view_appointments',
            'create_appointments',
            'edit_appointments',
            'view_visits',
            'create_visits',
            'edit_visits',
            'view_billing',
            'view_files',
            'create_files',
            'edit_files',
        ]);
    }
}
