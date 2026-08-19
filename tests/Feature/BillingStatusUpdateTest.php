<?php

use App\Enums\BillingStatusEnum;
use App\Enums\MedicalOrderStatusEnum;
use App\Models\Billing;
use App\Models\Inventory;
use App\Models\MedicalOrder;
use App\Models\MedicalOrderInventory;
use App\Models\Patient;
use App\Models\Staff;
use App\Models\StaffRole;
use App\Models\User;
use App\Models\Visit;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

beforeEach(function () {
    $permissions = [
        'view_billings',
        'edit_billings',
        'update_billing_status',
    ];

    foreach ($permissions as $perm) {
        Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
    }

    $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    $role->syncPermissions($permissions);
});

function createStatusUpdateAdmin(): User
{
    $domainRole = StaffRole::firstOrCreate(['name' => 'admin'], ['description' => 'System Administrator']);
    $spatieRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    $user = User::factory()->create();
    $user->assignRole($spatieRole);
    Staff::factory()->create(['user_id' => $user->id, 'role_id' => $domainRole->id]);
    app(PermissionRegistrar::class)->forgetCachedPermissions();

    return $user->fresh();
}

function createStatusUpdateBilling(array $billingOverrides = []): Billing
{
    $patient = Patient::factory()->create();
    $staff = Staff::factory()->create();
    $visit = Visit::factory()->create([
        'patient_id' => $patient->id,
        'staff_id' => $staff->id,
        'status' => 'awaiting_accountant',
    ]);

    $medicalOrder = MedicalOrder::create([
        'visit_id' => $visit->id,
        'patient_id' => $patient->id,
        'staff_id' => $staff->id,
        'order_details' => 'Test order',
        'status' => MedicalOrderStatusEnum::COMPLETED,
        'priority' => 'routine',
        'notes' => 'Test notes',
        'ordered_at' => now(),
        'completed_at' => now(),
    ]);

    $inventory = Inventory::factory()->rxMedicine()->create([
        'quantity' => 100,
        'selling_price' => 25.00,
    ]);

    MedicalOrderInventory::create([
        'medical_order_id' => $medicalOrder->id,
        'inventory_id' => $inventory->id,
        'item_type' => 'rx_medicine',
        'item_name' => $inventory->item_name,
        'quantity_required' => 2,
        'status' => MedicalOrderStatusEnum::COMPLETED->value,
        'completed_at' => now(),
    ]);

    return Billing::create(array_merge([
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
}

test('reverting a paid billing to pending refreshes billing_date to today', function () {
    $user = createStatusUpdateAdmin();
    $billing = createStatusUpdateBilling([
        'status' => BillingStatusEnum::PAID,
        'billing_date' => now()->subWeek(),
    ]);

    $this->actingAs($user)
        ->patch(route('billings.update-status', $billing), ['status' => 'pending'])
        ->assertRedirect(route('billings.show', $billing));

    $billing->refresh();
    expect($billing->status)->toBe(BillingStatusEnum::PENDING);
    expect($billing->billing_date->isToday())->toBeTrue();
});

test('reverting a paid billing to partial refreshes billing_date to today', function () {
    $user = createStatusUpdateAdmin();
    $billing = createStatusUpdateBilling([
        'status' => BillingStatusEnum::PAID,
        'billing_date' => now()->subDay(),
    ]);

    $this->actingAs($user)
        ->patch(route('billings.update-status', $billing), ['status' => 'partial']);

    $billing->refresh();
    expect($billing->status)->toBe(BillingStatusEnum::PARTIAL);
    expect($billing->billing_date->isToday())->toBeTrue();
});

test('a manually-reverted pending billing survives the auto-overdue sweep and stays visible under the Pending tab', function () {
    $user = createStatusUpdateAdmin();
    $billing = createStatusUpdateBilling([
        'status' => BillingStatusEnum::PAID,
        'billing_date' => now()->subWeek(),
    ]);

    $this->actingAs($user)
        ->patch(route('billings.update-status', $billing), ['status' => 'pending']);

    // Loading the billings list runs the auto-overdue sweep on every request.
    $this->actingAs($user)
        ->get('/billings?status=pending')
        ->assertInertia(fn ($page) => $page
            ->where('billings', fn ($billings) => collect($billings)->contains(fn ($b) => $b['id'] === $billing->id))
        );

    $billing->refresh();
    expect($billing->status)->toBe(BillingStatusEnum::PENDING);
});

test('an untouched stale pending billing still gets auto-swept to overdue', function () {
    $user = createStatusUpdateAdmin();
    $billing = createStatusUpdateBilling([
        'status' => BillingStatusEnum::PENDING,
        'billing_date' => now()->subWeek(),
    ]);

    $this->actingAs($user)->get('/billings');

    $billing->refresh();
    expect($billing->status)->toBe(BillingStatusEnum::OVERDUE);
});

test('billing report excludes a reverted-to-pending billing from revenue and counts it as unpaid', function () {
    $user = createStatusUpdateAdmin();
    createStatusUpdateBilling([
        'status' => BillingStatusEnum::PAID,
        'billing_date' => now(),
        'amount' => 100,
    ]);
    $revertedToPending = createStatusUpdateBilling([
        'status' => BillingStatusEnum::PAID,
        'billing_date' => now(),
        'amount' => 100,
    ]);

    $this->actingAs($user)
        ->patch(route('billings.update-status', $revertedToPending), ['status' => 'pending']);

    $today = now()->format('Y-m-d');

    $this->actingAs($user)
        ->get("/billing-report?start_date={$today}&end_date={$today}")
        ->assertInertia(fn ($page) => $page
            ->where('summary.total_revenue', 100)
            ->where('summary.paid_count', 1)
            ->where('summary.unpaid_count', 1)
        );
});
