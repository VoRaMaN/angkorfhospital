<?php

use App\Enums\MedicalOrderStatusEnum;
use App\Models\Inventory;
use App\Models\MedicalOrder;
use App\Models\MedicalOrderInventory;
use App\Models\Patient;
use App\Models\Staff;
use App\Models\User;
use App\Models\Visit;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $permissions = [
        'view_medical_orders',
        'create_medical_orders',
        'edit_medical_orders',
        'process_medical_orders',
        'complete_medical_orders',
        'process_and_bill_medical_orders',
        'confirm_processed_medical_orders',
        'view_billings',
    ];

    foreach ($permissions as $perm) {
        Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
    }

    $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    $adminRole->syncPermissions($permissions);

    $doctorRole = Role::firstOrCreate(['name' => 'doctor', 'guard_name' => 'web']);
    $doctorRole->syncPermissions($permissions);
});

function createDoctorWithStaff(): User
{
    $role = Role::firstOrCreate(['name' => 'doctor', 'guard_name' => 'web']);
    $user = User::factory()->create();
    $user->assignRole($role);
    Staff::factory()->create(['user_id' => $user->id, 'role_id' => $role->id]);

    return $user;
}

function createPendingOrder(User $doctor): array
{
    $patient = Patient::factory()->create();
    $staff = Staff::where('user_id', $doctor->id)->first();
    $visit = Visit::factory()->create([
        'patient_id' => $patient->id,
        'staff_id' => $staff->id,
        'status' => 'assigned',
    ]);

    $medicalOrder = MedicalOrder::create([
        'visit_id' => $visit->id,
        'patient_id' => $patient->id,
        'staff_id' => $staff->id,
        'order_details' => 'Test order for processing',
        'status' => MedicalOrderStatusEnum::PENDING,
        'priority' => 'routine',
        'notes' => 'Test notes',
        'ordered_at' => now(),
    ]);

    $inventory = Inventory::factory()->rxMedicine()->create([
        'quantity' => 100,
        'selling_price' => 25.00,
    ]);

    $orderItem = MedicalOrderInventory::create([
        'medical_order_id' => $medicalOrder->id,
        'inventory_id' => $inventory->id,
        'item_type' => 'rx_medicine',
        'item_name' => $inventory->item_name,
        'quantity_required' => 2,
        'status' => MedicalOrderStatusEnum::PENDING->value,
    ]);

    return compact('medicalOrder', 'visit', 'patient', 'staff', 'inventory', 'orderItem');
}

it('doctor can process medical order with items via processWithUpdate', function () {
    $doctor = createDoctorWithStaff();
    $data = createPendingOrder($doctor);

    $response = $this->actingAs($doctor)->patch(
        route('medical-orders.process-with-update', $data['medicalOrder']),
        [
            'order_details' => 'Updated order details',
            'notes' => 'Processing notes',
            'order_items' => [
                [
                    'item_type' => 'rx_medicine',
                    'item_name' => $data['inventory']->item_name,
                    'quantity_required' => 2,
                    'status' => 'pending',
                    'inventory_id' => $data['inventory']->id,
                ],
            ],
        ],
    );

    // Should redirect to billing show page, not back
    $response->assertRedirect();
    $response->assertSessionHas('success');

    // Order should be completed
    $data['medicalOrder']->refresh();
    expect($data['medicalOrder']->status)->toBe(MedicalOrderStatusEnum::COMPLETED);

    // Billing should be created
    $billing = \App\Models\Billing::where('medical_order_id', $data['medicalOrder']->id)->first();
    expect($billing)->not->toBeNull();
    expect((float) $billing->amount)->toBe(50.00); // 25.00 * 2
});

it('admin can process medical order via processWithUpdate', function () {
    $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    $admin = User::factory()->create();
    $admin->assignRole($role);
    Staff::factory()->create(['user_id' => $admin->id, 'role_id' => $role->id]);

    $data = createPendingOrder($admin);

    // Override the staff_id to be admin's staff
    $adminStaff = Staff::where('user_id', $admin->id)->first();
    $data['medicalOrder']->update(['staff_id' => $adminStaff->id]);

    $response = $this->actingAs($admin)->patch(
        route('medical-orders.process-with-update', $data['medicalOrder']),
        [
            'order_details' => 'Admin processed',
            'notes' => 'Admin notes',
            'order_items' => [
                [
                    'item_type' => 'rx_medicine',
                    'item_name' => $data['inventory']->item_name,
                    'quantity_required' => 1,
                    'status' => 'pending',
                    'inventory_id' => $data['inventory']->id,
                ],
            ],
        ],
    );

    $response->assertRedirect();
    $response->assertSessionHas('success');
});

it('rejects processWithUpdate for non-pending orders', function () {
    $doctor = createDoctorWithStaff();
    $data = createPendingOrder($doctor);

    // Change status to completed
    $data['medicalOrder']->update(['status' => MedicalOrderStatusEnum::COMPLETED]);

    $response = $this->actingAs($doctor)->patch(
        route('medical-orders.process-with-update', $data['medicalOrder']),
        [
            'order_details' => 'Test',
            'order_items' => [
                [
                    'item_type' => 'rx_medicine',
                    'item_name' => 'Test Medicine',
                    'quantity_required' => 1,
                    'status' => 'pending',
                    'inventory_id' => $data['inventory']->id,
                ],
            ],
        ],
    );

    $response->assertRedirect();
    $response->assertSessionHas('error');
});

it('handles special_item type in processWithUpdate', function () {
    $doctor = createDoctorWithStaff();
    $data = createPendingOrder($doctor);

    // Create a special item
    $specialItem = \App\Models\SpecialItem::create([
        'name' => 'Emergency Kit',
        'description' => 'Emergency supplies kit',
        'unit_price' => 150.00,
        'is_active' => true,
    ]);

    $response = $this->actingAs($doctor)->patch(
        route('medical-orders.process-with-update', $data['medicalOrder']),
        [
            'order_details' => 'Order with special item',
            'notes' => '',
            'order_items' => [
                [
                    'item_type' => 'special_item',
                    'item_name' => 'Emergency Kit',
                    'quantity_required' => 1,
                    'status' => 'pending',
                ],
            ],
        ],
    );

    $response->assertRedirect();
    $response->assertSessionHas('success');

    // Billing should use special item price
    $billing = \App\Models\Billing::where('medical_order_id', $data['medicalOrder']->id)->first();
    expect($billing)->not->toBeNull();
    expect((float) $billing->amount)->toBe(150.00);
});
