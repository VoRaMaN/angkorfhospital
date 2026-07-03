<?php

namespace App\Observers;

use App\Models\Staff;
use App\Models\StaffRole;
use Spatie\Permission\Models\Role as SpatieRole;
use Spatie\Permission\PermissionRegistrar;

class StaffObserver
{
    public function created(Staff $staff): void
    {
        $this->syncRole($staff);
    }

    public function updated(Staff $staff): void
    {
        // If the role_id changed, sync the Spatie role
        if ($staff->wasChanged('role_id')) {
            $this->syncRole($staff);
        }
    }

    /**
     * Sync the user's Spatie role with the domain staff role name.
     */
    protected function syncRole(Staff $staff): void
    {
        $user = $staff->user;

        if (! $user) {
            return; // No user associated yet
        }

        // Use StaffRole (custom roles table) to get the correct role name
        $role = StaffRole::find($staff->role_id);

        if (! $role || ! $role->name) {
            // If there's no domain role, remove all roles from the user
            $user->syncRoles([]);

            return;
        }

        // Ensure the Spatie role exists (create if missing), then sync
        $spatieRole = SpatieRole::firstOrCreate([
            'name' => $role->name,
            'guard_name' => config('auth.defaults.guard', 'web'),
        ]);

        // If the Spatie role was just created (no permissions yet), re-seed its permissions
        // from the database seeder defaults so the role is never left empty.
        if ($spatieRole->wasRecentlyCreated) {
            $this->seedRolePermissions($spatieRole);
        }

        // Sync roles so the user has exactly the spatie role aligned to the domain role.
        $user->syncRoles([$spatieRole->name]);

        // Always flush the cached permissions so frontend gets fresh data immediately.
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * Re-apply the default permission set for a given Spatie role.
     * This ensures roles are never left empty when recreated by the observer.
     */
    protected function seedRolePermissions(SpatieRole $spatieRole): void
    {
        $defaultPermissions = [
            'admin' => [], // admin is handled by the seeder (all permissions)
            'doctor' => ['view_users', 'view_staff', 'view_doctors', 'view_patients', 'create_patients', 'edit_patients', 'view_appointments', 'create_appointments', 'edit_appointments', 'view_medical_records', 'create_medical_records', 'edit_medical_records', 'view_medical_orders', 'create_medical_orders', 'edit_medical_orders', 'process_medical_orders', 'process_and_bill_medical_orders', 'confirm_processed_medical_orders', 'complete_medical_orders', 'complete_medical_order_items', 'send_back_medical_orders', 'view_medical_services', 'create_medical_services', 'edit_medical_services', 'view_medications', 'view_visits', 'create_visits', 'edit_visits', 'notify_visits', 'cancel_visits', 'assign_visits', 'view_billing', 'view_billings', 'view_files', 'create_files', 'edit_files', 'view_lab_panels', 'create_lab_panels', 'edit_lab_panels'],
            'nurse' => ['view_users', 'view_staff', 'view_patients', 'create_patients', 'edit_patients', 'view_appointments', 'create_appointments', 'edit_appointments', 'view_medical_records', 'view_medical_orders', 'create_medical_orders', 'edit_medical_orders', 'process_medical_orders', 'process_and_bill_medical_orders', 'confirm_processed_medical_orders', 'complete_medical_orders', 'complete_medical_order_items', 'send_back_medical_orders', 'view_medical_services', 'create_medical_services', 'edit_medical_services', 'view_medications', 'view_visits', 'create_visits', 'edit_visits', 'notify_visits', 'cancel_visits', 'assign_visits', 'view_billing', 'create_billing', 'edit_billing', 'view_billings', 'create_billings', 'edit_billings', 'view_inventory', 'create_inventory', 'edit_inventory', 'view_inventories', 'create_inventories', 'edit_inventories', 'view_lab_packages', 'create_lab_packages', 'edit_lab_packages', 'view_lab_panels', 'create_lab_panels', 'edit_lab_panels', 'view_files', 'create_files', 'edit_files', 'delete_files'],
            'receptionist' => ['view_users', 'view_patients', 'create_patients', 'edit_patients', 'view_appointments', 'create_appointments', 'edit_appointments', 'view_visits', 'create_visits', 'edit_visits', 'delete_visits', 'notify_visits', 'view_billing', 'view_billings', 'view_files', 'create_files', 'edit_files', 'delete_patient_files'],
            'accountant' => ['view_billing', 'create_billing', 'edit_billing', 'delete_billing', 'update_billing_status', 'view_billings', 'create_billings', 'edit_billings', 'view_inventory', 'view_inventories', 'view_visits', 'view_medical_orders', 'process_and_bill_medical_orders', 'confirm_processed_medical_orders', 'complete_medical_orders'],
            'billing' => ['view_billing', 'create_billing', 'edit_billing', 'delete_billing', 'update_billing_status', 'view_billings', 'create_billings', 'edit_billings', 'view_patients', 'create_patients', 'edit_patients', 'view_visits', 'view_medical_orders', 'process_and_bill_medical_orders', 'confirm_processed_medical_orders', 'view_inventory', 'view_inventories'],
            'pharmacist' => ['view_medications', 'create_medications', 'edit_medications', 'delete_medications', 'view_inventory', 'create_inventory', 'edit_inventory', 'view_inventories', 'create_inventories', 'edit_inventories', 'view_medical_orders', 'process_medical_orders', 'complete_medical_order_items', 'view_patients', 'view_billing', 'view_billings', 'view_medicine_reports', 'create_medicine_reports', 'edit_medicine_reports', 'delete_medicine_reports'],
            'lab' => ['view_lab_packages', 'create_lab_packages', 'edit_lab_packages', 'delete_lab_packages', 'view_lab_panels', 'create_lab_panels', 'edit_lab_panels', 'view_medical_orders', 'process_medical_orders', 'complete_medical_order_items', 'view_patients', 'view_medical_records', 'view_files', 'create_files'],
            'inventory' => ['view_inventory', 'create_inventory', 'edit_inventory', 'delete_inventory', 'view_inventories', 'create_inventories', 'edit_inventories', 'view_medications', 'view_medicine_reports', 'create_medicine_reports', 'edit_medicine_reports'],
            'staff' => ['view_patients', 'view_appointments', 'view_visits', 'view_billing', 'view_billings', 'view_inventory', 'view_inventories', 'view_files'],
            'patient' => ['view_appointments', 'view_medical_records', 'view_medical_orders', 'view_visits', 'view_billing', 'view_files'],
        ];

        $roleName = strtolower($spatieRole->name);

        if (! isset($defaultPermissions[$roleName]) || empty($defaultPermissions[$roleName])) {
            return;
        }

        // Only sync permissions that actually exist in the database.
        // This keeps the observer safe in test environments where not all permissions are seeded.
        $existingPermissions = \Spatie\Permission\Models\Permission::whereIn('name', $defaultPermissions[$roleName])
            ->where('guard_name', config('auth.defaults.guard', 'web'))
            ->pluck('name')
            ->all();

        if (! empty($existingPermissions)) {
            $spatieRole->syncPermissions($existingPermissions);
        }
    }

    public function deleted(Staff $staff): void
    {
        $user = $staff->user;

        if (! $user) {
            return; // Nothing to do
        }

        $role = $staff->role;
        if (! $role || ! $role->name) {
            return;
        }

        // Remove the role from the user if they have it.
        if ($user->hasRole($role->name)) {
            $user->removeRole($role->name);
        }

        // If no models are assigned to this spatie role, delete it to keep things clean.
        $spatieRole = SpatieRole::where('name', $role->name)
            ->where('guard_name', config('auth.defaults.guard', 'web'))
            ->first();

        if ($spatieRole) {
            $table = config('permission.table_names.model_has_roles');
            $assignedCount = \DB::table($table)->where('role_id', $spatieRole->id)->count();

            if ($assignedCount === 0) {
                $spatieRole->delete();
            }
        }
    }
}
