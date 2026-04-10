<?php

namespace Database\Seeders;

use App\Models\Staff;
use App\Models\StaffRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role as SpatieRole;

class NewStaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure new roles exist in both tables
        $newRoles = [
            'cashier' => 'Handles cash transactions and payments',
            'gm' => 'General Manager',
            'cleaner' => 'Cleaning and maintenance staff',
            'doorman' => 'Door and security staff',
        ];

        foreach ($newRoles as $name => $description) {
            StaffRole::firstOrCreate(['name' => $name], ['description' => $description]);
            SpatieRole::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        // Basic permissions for new Spatie roles
        $cashierRole = SpatieRole::findByName('cashier');
        $cashierRole->syncPermissions([
            'view_billing',
            'create_billing',
            'edit_billing',
            'update_billing_status',
            'view_billings',
            'create_billings',
            'edit_billings',
            'view_patients',
            'view_visits',
        ]);

        $gmRole = SpatieRole::findByName('gm');
        $gmRole->syncPermissions([
            'view_users',
            'view_staff',
            'view_patients',
            'view_appointments',
            'view_medical_records',
            'view_medical_orders',
            'view_medical_services',
            'view_medications',
            'view_visits',
            'view_billing',
            'view_billings',
            'view_inventory',
            'view_inventories',
            'view_lab_packages',
            'view_departments',
            'view_roles',
            'view_settings',
            'view_activity_logs',
            'view_files',
        ]);

        $cleanerRole = SpatieRole::findByName('cleaner');
        $cleanerRole->syncPermissions([
            'view_appointments',
            'view_visits',
        ]);

        $doormanRole = SpatieRole::findByName('doorman');
        $doormanRole->syncPermissions([
            'view_appointments',
            'view_visits',
        ]);

        // Staff members to create
        $staffMembers = [
            ['first_name' => 'Chheng', 'last_name' => 'Damady', 'phone' => '0964205467', 'role' => 'nurse', 'email' => 'damady@nurse.com'],
            ['first_name' => 'Sean', 'last_name' => 'Mouykea', 'phone' => '010973327', 'role' => 'nurse', 'email' => 'mouykea@nurse.com'],
            ['first_name' => 'Ounn', 'last_name' => 'Voleak', 'phone' => '016443376', 'role' => 'nurse', 'email' => 'voleak@nurse.com'],
            ['first_name' => 'Lim', 'last_name' => 'Hongchin', 'phone' => '095585923', 'role' => 'lab', 'email' => 'hongchin@lab.com'],
            ['first_name' => 'Tek', 'last_name' => 'Theara', 'phone' => '0968716645', 'role' => 'lab', 'email' => 'theara@lab.com'],
            ['first_name' => 'Danh', 'last_name' => 'VanDaroat', 'phone' => '098595958', 'role' => 'lab', 'email' => 'vandaroat@lab.com'],
            ['first_name' => 'Chhean', 'last_name' => 'Momnida', 'phone' => '095955261', 'role' => 'lab', 'email' => 'momnida@lab.com'],
            ['first_name' => 'Chay', 'last_name' => 'Sokry', 'phone' => '087559966', 'role' => 'gm', 'email' => 'sokry@gm.com'],
            ['first_name' => 'Norn', 'last_name' => 'Chheavbouy', 'phone' => '086972727', 'role' => 'cashier', 'email' => 'chheavbouy@cashier.com'],
            ['first_name' => 'Serey', 'last_name' => 'Chanda', 'phone' => '077954656', 'role' => 'receptionist', 'email' => 'chanda@receptionist.com'],
            ['first_name' => 'Mao', 'last_name' => 'Sreysor', 'phone' => '069451759', 'role' => 'cleaner', 'email' => 'sreysor@cleaner.com'],
            ['first_name' => 'Chhouy', 'last_name' => 'Veasna', 'phone' => '0967381181', 'role' => 'cleaner', 'email' => 'veasna@cleaner.com'],
            ['first_name' => 'Chosen', 'last_name' => 'Chan', 'phone' => '078940179', 'role' => 'doorman', 'email' => 'chan@doorman.com'],
            ['first_name' => 'Lim', 'last_name' => 'Sokong', 'phone' => '012296891', 'role' => 'doctor', 'email' => 'sokong@doctor.com'],
        ];

        foreach ($staffMembers as $data) {
            $staffRole = StaffRole::where('name', $data['role'])->first();

            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['first_name'].' '.$data['last_name'],
                    'password' => bcrypt('password'),
                    'email_verified_at' => now(),
                ]
            );

            $user->assignRole($data['role']);

            Staff::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'role_id' => $staffRole->id,
                    'contact_number' => $data['phone'],
                    'hire_date' => now(),
                ]
            );
        }
    }
}
