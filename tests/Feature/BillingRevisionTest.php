<?php

use App\Enums\BillingStatusEnum;
use App\Enums\MedicalOrderStatusEnum;
use App\Enums\VisitStatusEnum;
use App\Models\Billing;
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
        'view_billings',
        'edit_billings',
        'send_back_billing',
        'delete_billing',
        'view_medical_orders',
        'edit_medical_orders',
        'process_medical_orders',
        'complete_medical_orders',
        'process_and_bill_medical_orders',
        'confirm_processed_medical_orders',
    ];

    foreach ($permissions as $perm) {
        Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
    }

    $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    $role->syncPermissions($permissions);
});

function createAdminWithStaff(): User
{
    $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    $user = User::factory()->create();
    $user->assignRole($role);
    Staff::factory()->create(['user_id' => $user->id, 'role_id' => $role->id]);

    return $user;
}

function createBillingWithMedicalOrder(array $billingOverrides = [], array $orderOverrides = []): array
{
    $patient = Patient::factory()->create();
    $staff = Staff::factory()->create();
    $visit = Visit::factory()->create([
        'patient_id' => $patient->id,
        'staff_id' => $staff->id,
        'status' => 'awaiting_accountant',
    ]);

    $medicalOrder = MedicalOrder::create(array_merge([
        'visit_id' => $visit->id,
        'patient_id' => $patient->id,
        'staff_id' => $staff->id,
        'order_details' => 'Test order',
        'status' => MedicalOrderStatusEnum::COMPLETED,
        'priority' => 'routine',
        'notes' => 'Test notes',
        'ordered_at' => now(),
        'completed_at' => now(),
    ], $orderOverrides));

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
        'status' => MedicalOrderStatusEnum::COMPLETED->value,
        'completed_at' => now(),
    ]);

    $billing = Billing::create(array_merge([
        'bill_no' => Billing::generateBillNo(),
        'patient_id' => $patient->id,
        'visit_id' => $visit->id,
        'medical_order_id' => $medicalOrder->id,
        'doctor_id' => $staff->id,
        'amount' => 50.00,
        'status' => BillingStatusEnum::PAID,
        'billing_date' => now(),
        'notes' => 'Initial billing',
    ], $billingOverrides));

    return compact('billing', 'medicalOrder', 'visit', 'patient', 'staff', 'inventory', 'orderItem');
}

test('guests cannot send billing back to nurse', function () {
    $data = createBillingWithMedicalOrder();

    $this->patch(route('billings.send-back-to-nurse', $data['billing']))
        ->assertRedirect(route('login'));
});

test('admin can send paid billing back to nurse for revision', function () {
    $user = createAdminWithStaff();
    $data = createBillingWithMedicalOrder();

    $this->actingAs($user)
        ->patch(route('billings.send-back-to-nurse', $data['billing']), [
            'reason' => 'Patient needs additional medicine',
        ])
        ->assertRedirect(route('billings.show', $data['billing']));

    // Billing should be in revision status
    $data['billing']->refresh();
    expect($data['billing']->status)->toBe(BillingStatusEnum::REVISION);
    expect($data['billing']->notes)->toContain('Patient needs additional medicine');

    // Medical order should be back to processing
    $data['medicalOrder']->refresh();
    expect($data['medicalOrder']->status)->toBe(MedicalOrderStatusEnum::PROCESSING);
    expect($data['medicalOrder']->completed_at)->toBeNull();

    // Order items should be reset to pending
    $data['orderItem']->refresh();
    expect($data['orderItem']->status)->toBe(MedicalOrderStatusEnum::PENDING);
    expect($data['orderItem']->completed_at)->toBeNull();

    // Visit should be sent back
    $data['visit']->refresh();
    expect($data['visit']->status)->toBe(VisitStatusEnum::SENT_BACK);
});

test('sending billing back restores inventory stock', function () {
    $user = createAdminWithStaff();
    $data = createBillingWithMedicalOrder();

    $originalQty = $data['inventory']->quantity;

    $this->actingAs($user)
        ->patch(route('billings.send-back-to-nurse', $data['billing']), [
            'reason' => 'Need to adjust items',
        ])
        ->assertRedirect();

    // Inventory should have stock restored (quantity + quantity_required)
    $data['inventory']->refresh();
    expect($data['inventory']->quantity)->toBe($originalQty + 2);
});

test('send back requires a reason', function () {
    $user = createAdminWithStaff();
    $data = createBillingWithMedicalOrder();

    $this->actingAs($user)
        ->patch(route('billings.send-back-to-nurse', $data['billing']), [
            'reason' => '',
        ])
        ->assertSessionHasErrors('reason');
});

test('cannot send back billing without medical order', function () {
    $user = createAdminWithStaff();
    $patient = Patient::factory()->create();

    $billing = Billing::create([
        'bill_no' => Billing::generateBillNo(),
        'patient_id' => $patient->id,
        'amount' => 100.00,
        'status' => BillingStatusEnum::PAID,
        'billing_date' => now(),
    ]);

    $this->actingAs($user)
        ->patch(route('billings.send-back-to-nurse', $billing), [
            'reason' => 'Some reason',
        ])
        ->assertRedirect()
        ->assertSessionHas('error');
});

test('admin can send pending billing back to nurse', function () {
    $user = createAdminWithStaff();
    $data = createBillingWithMedicalOrder(['status' => BillingStatusEnum::PENDING]);

    $this->actingAs($user)
        ->patch(route('billings.send-back-to-nurse', $data['billing']), [
            'reason' => 'Adjustments needed',
        ])
        ->assertRedirect(route('billings.show', $data['billing']));

    $data['billing']->refresh();
    expect($data['billing']->status)->toBe(BillingStatusEnum::REVISION);
});

test('recalculate updates billing amount from medical order', function () {
    $user = createAdminWithStaff();
    $data = createBillingWithMedicalOrder(['status' => BillingStatusEnum::REVISION]);

    // Billing was 50.00, but order items have selling_price 25 * qty 2 = 50
    $this->actingAs($user)
        ->patch(route('billings.recalculate', $data['billing']))
        ->assertRedirect(route('billings.show', $data['billing']));

    $data['billing']->refresh();
    expect((float) $data['billing']->amount)->toBe(50.00);
});

test('process and bill handles revision flow with existing billing', function () {
    $user = createAdminWithStaff();
    $data = createBillingWithMedicalOrder([
        'status' => BillingStatusEnum::REVISION,
    ], [
        'status' => MedicalOrderStatusEnum::PROCESSED,
        'completed_at' => null,
    ]);

    // Reset order items to pending (as they would be after send-back)
    $data['orderItem']->update([
        'status' => MedicalOrderStatusEnum::PENDING->value,
        'completed_at' => null,
    ]);

    $this->actingAs($user)
        ->patch(route('medical-orders.process-and-bill', $data['medicalOrder']))
        ->assertRedirect(route('billings.show', $data['billing']));

    // Billing should be back to pending (ready for accountant)
    $data['billing']->refresh();
    expect($data['billing']->status)->toBe(BillingStatusEnum::PENDING);
    expect($data['billing']->notes)->toContain('Recalculated after revision');

    // Medical order should be completed
    $data['medicalOrder']->refresh();
    expect($data['medicalOrder']->status)->toBe(MedicalOrderStatusEnum::COMPLETED);

    // Visit should be awaiting_accountant
    $data['visit']->refresh();
    expect($data['visit']->status)->toBe(VisitStatusEnum::AWAITING_ACCOUNTANT);
});

test('billing revision enum has correct properties', function () {
    $revision = BillingStatusEnum::REVISION;

    expect($revision->value)->toBe('revision');
    expect($revision->label())->toBe('Revision');
    expect($revision->color())->toBe('bg-amber-100 text-amber-800');
    expect($revision->isActive())->toBeTrue();
});
