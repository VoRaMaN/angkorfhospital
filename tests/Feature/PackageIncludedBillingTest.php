<?php

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
use App\Services\MedicalOrderBillingService;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

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
        'edit_billings',
    ];

    foreach ($permissions as $perm) {
        Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
    }

    $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    $adminRole->syncPermissions($permissions);
});

function createPackageBillingAdmin(): User
{
    $domainRole = StaffRole::firstOrCreate(['name' => 'admin'], ['description' => 'System Administrator']);
    $spatieRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    $user = User::factory()->create();
    $user->assignRole($spatieRole);
    Staff::factory()->create(['user_id' => $user->id, 'role_id' => $domainRole->id]);
    app(PermissionRegistrar::class)->forgetCachedPermissions();

    return $user->fresh();
}

function createOrderWithPackageItem(User $user): array
{
    $patient = Patient::factory()->create();
    $staff = Staff::where('user_id', $user->id)->first();
    $visit = Visit::factory()->create([
        'patient_id' => $patient->id,
        'staff_id' => $staff->id,
        'status' => 'assigned',
    ]);

    $medicalOrder = MedicalOrder::create([
        'visit_id' => $visit->id,
        'patient_id' => $patient->id,
        'staff_id' => $staff->id,
        'order_details' => 'Order with package-included item',
        'status' => MedicalOrderStatusEnum::PENDING,
        'priority' => 'routine',
        'ordered_at' => now(),
    ]);

    $labInventory = Inventory::factory()->create([
        'quantity' => 100,
        'unit_price' => 10.00,
        'selling_price' => 15.00,
    ]);

    $rxInventory = Inventory::factory()->rxMedicine()->create([
        'quantity' => 100,
        'selling_price' => 25.00,
    ]);

    return compact('medicalOrder', 'visit', 'patient', 'staff', 'labInventory', 'rxInventory');
}

it('does not bill package-included items even when they have an inventory price', function () {
    $admin = createPackageBillingAdmin();
    $data = createOrderWithPackageItem($admin);

    // Package-included lab item: selling_price 0 but inventory_id points at a $15 item
    $packageItem = MedicalOrderInventory::create([
        'medical_order_id' => $data['medicalOrder']->id,
        'inventory_id' => $data['labInventory']->id,
        'item_type' => 'lab',
        'item_name' => $data['labInventory']->item_name,
        'quantity_required' => 1,
        'unit_price' => 0,
        'selling_price' => 0,
        'is_package_included' => true,
        'status' => MedicalOrderStatusEnum::PENDING->value,
        'notes' => 'Include Package - Not counted in billing',
    ]);

    // Normal rx item that should be billed
    MedicalOrderInventory::create([
        'medical_order_id' => $data['medicalOrder']->id,
        'inventory_id' => $data['rxInventory']->id,
        'item_type' => 'rx_medicine',
        'item_name' => $data['rxInventory']->item_name,
        'quantity_required' => 2,
        'unit_price' => 20.00,
        'selling_price' => 25.00,
        'is_package_included' => false,
        'status' => MedicalOrderStatusEnum::PENDING->value,
    ]);

    $service = app(MedicalOrderBillingService::class);
    $data['medicalOrder']->load('orderItems.inventory');

    expect($service->calculateItemTotal($packageItem))->toBe(0.0);
    expect($service->calculateOrderTotal($data['medicalOrder']))->toBe(50.0); // only 25 * 2
});

it('does not bill a package-included special_item even when a catalog SpecialItem with the same name has a real price', function () {
    // Reproduces the reported bug: the "Special Items" picker (as opposed to
    // the Medicine Groups picker) never sent is_package_included, so these
    // items fell through calculateItemTotal's name-based SpecialItem lookup
    // and got billed at the catalog price despite the checkbox being ticked.
    $admin = createPackageBillingAdmin();
    $data = createOrderWithPackageItem($admin);

    \App\Models\SpecialItem::create([
        'name' => 'Ultrasound Kit',
        'description' => 'Reusable ultrasound supply kit',
        'unit_price' => 15.00,
        'is_active' => true,
    ]);

    $packageSpecialItem = MedicalOrderInventory::create([
        'medical_order_id' => $data['medicalOrder']->id,
        'inventory_id' => null,
        'item_type' => 'special_item',
        'item_name' => 'Ultrasound Kit',
        'quantity_required' => 1,
        'unit_price' => 0,
        'selling_price' => 0,
        'is_package_included' => true,
        'status' => MedicalOrderStatusEnum::PENDING->value,
        'notes' => 'Include Package - Not counted in billing',
    ]);

    $service = app(MedicalOrderBillingService::class);

    expect($service->calculateItemTotal($packageSpecialItem))->toBe(0.0);
});

it('does not re-price a legacy sentinel-note row that was saved without the flag', function () {
    // Rows created by stale (pre-flag) browser bundles have selling_price 0
    // and the "Include Package" note but is_package_included = false; the
    // catalog-name fallback must still treat them as free.
    $admin = createPackageBillingAdmin();
    $data = createOrderWithPackageItem($admin);

    \App\Models\SpecialItem::create([
        'name' => 'TVS 15',
        'description' => 'TVS scan special item',
        'unit_price' => 15.00,
        'is_active' => true,
    ]);

    $legacyRow = MedicalOrderInventory::create([
        'medical_order_id' => $data['medicalOrder']->id,
        'inventory_id' => null,
        'item_type' => 'special_item',
        'item_name' => 'TVS 15',
        'quantity_required' => 1,
        'unit_price' => 0,
        'selling_price' => 0,
        'is_package_included' => false,
        'status' => MedicalOrderStatusEnum::PENDING->value,
        'notes' => 'Special Items: TVS 15 - Include Package - Not counted in billing',
    ]);

    $service = app(MedicalOrderBillingService::class);

    expect($service->calculateItemTotal($legacyRow))->toBe(0.0);
});

it('processWithUpdate persists is_package_included and excludes it from billing', function () {
    $admin = createPackageBillingAdmin();
    $data = createOrderWithPackageItem($admin);

    $response = $this->actingAs($admin)->patch(
        route('medical-orders.process-with-update', $data['medicalOrder']),
        [
            'order_details' => 'Processed with package item',
            'notes' => '',
            'order_items' => [
                [
                    'item_type' => 'lab',
                    'item_name' => $data['labInventory']->item_name,
                    'quantity_required' => 1,
                    'status' => 'pending',
                    'inventory_id' => $data['labInventory']->id,
                    'unit_price' => 0,
                    'selling_price' => 0,
                    'is_package_included' => true,
                    'notes' => 'Include Package - Not counted in billing',
                ],
                [
                    'item_type' => 'rx_medicine',
                    'item_name' => $data['rxInventory']->item_name,
                    'quantity_required' => 2,
                    'status' => 'pending',
                    'inventory_id' => $data['rxInventory']->id,
                    'unit_price' => 20.00,
                    'selling_price' => 25.00,
                    'is_package_included' => false,
                ],
            ],
        ],
    );

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $stored = MedicalOrderInventory::where('medical_order_id', $data['medicalOrder']->id)
        ->where('item_type', 'lab')
        ->first();
    expect($stored->is_package_included)->toBeTrue();

    $billing = Billing::where('medical_order_id', $data['medicalOrder']->id)->first();
    expect($billing)->not->toBeNull();
    expect((float) $billing->amount)->toBe(50.00); // package lab item not counted
});

it('processAndBill reuses an existing sent_to_account billing instead of creating a duplicate', function () {
    $admin = createPackageBillingAdmin();
    $data = createOrderWithPackageItem($admin);

    MedicalOrderInventory::create([
        'medical_order_id' => $data['medicalOrder']->id,
        'inventory_id' => $data['rxInventory']->id,
        'item_type' => 'rx_medicine',
        'item_name' => $data['rxInventory']->item_name,
        'quantity_required' => 1,
        'unit_price' => 20.00,
        'selling_price' => 25.00,
        'status' => MedicalOrderStatusEnum::PENDING->value,
    ]);

    // First send to account
    $this->actingAs($admin)
        ->patch(route('medical-orders.process-and-bill', $data['medicalOrder']))
        ->assertRedirect();

    expect(Billing::where('medical_order_id', $data['medicalOrder']->id)->count())->toBe(1);
    $billing = Billing::where('medical_order_id', $data['medicalOrder']->id)->first();
    expect($billing->status->value)->toBe('sent_to_account');

    // Second send (e.g. after billing was sent back and reprocessed) must not duplicate
    $data['medicalOrder']->update(['status' => MedicalOrderStatusEnum::PENDING]);
    $this->actingAs($admin)
        ->patch(route('medical-orders.process-and-bill', $data['medicalOrder']))
        ->assertRedirect();

    expect(Billing::where('medical_order_id', $data['medicalOrder']->id)->count())->toBe(1);
});

it('edit page preserves is_package_included and does not resurrect the catalog price', function () {
    // Reproduces the reported bug: MedicalOrderController::edit() never sent
    // is_package_included to the page, and its price-fallback logic
    // resurrected the real inventory price for any item stored at $0 —
    // which is exactly how a package-included item is stored.
    $admin = createPackageBillingAdmin();
    $data = createOrderWithPackageItem($admin);

    MedicalOrderInventory::create([
        'medical_order_id' => $data['medicalOrder']->id,
        'inventory_id' => $data['labInventory']->id,
        'item_type' => 'lab',
        'item_name' => $data['labInventory']->item_name,
        'quantity_required' => 1,
        'unit_price' => 0,
        'selling_price' => 0,
        'is_package_included' => true,
        'status' => MedicalOrderStatusEnum::PENDING->value,
        'notes' => 'Include Package - Not counted in billing',
    ]);

    $this->actingAs($admin)
        ->get(route('medical-orders.edit', $data['medicalOrder']))
        ->assertInertia(fn ($page) => $page
            ->component('MedicalOrders/Edit')
            ->where('medicalOrder.order_items.0.is_package_included', true)
            ->where('medicalOrder.order_items.0.unit_price', 0)
            ->where('medicalOrder.order_items.0.selling_price', 0)
        );
});

it('adding a brand-new package-included lab item through update() survives a reload', function () {
    // Reproduces the client's follow-up report: the Lab/RX/Medicine-Group
    // "Add" dialogs zeroed the price and set the notes sentinel when
    // "Include Package" was checked, but never sent is_package_included
    // itself — so a newly-added item looked free in the moment, then lost
    // the flag on save and had its price resurrected on the next reload.
    $admin = createPackageBillingAdmin();
    $data = createOrderWithPackageItem($admin);

    // Simulates Edit.vue's addSelectedLabItems() after the fix: the nurse
    // checks "Include Package" on a brand-new lab item (no existing DB row).
    $this->actingAs($admin)
        ->put(route('medical-orders.update', $data['medicalOrder']), [
            'order_details' => $data['medicalOrder']->order_details,
            'order_items' => [
                [
                    'item_type' => 'lab',
                    'item_name' => $data['labInventory']->item_name,
                    'quantity_required' => 1,
                    'status' => 'pending',
                    'inventory_id' => $data['labInventory']->id,
                    'unit_price' => 0,
                    'selling_price' => 0,
                    'is_package_included' => true,
                    'notes' => 'Include Package - Not counted in billing',
                ],
            ],
        ])
        ->assertRedirect();

    $stored = MedicalOrderInventory::where('medical_order_id', $data['medicalOrder']->id)
        ->where('item_type', 'lab')
        ->first();
    expect($stored)->not->toBeNull();
    expect($stored->is_package_included)->toBeTrue();
    expect((float) $stored->unit_price)->toBe(0.0);
    expect((float) $stored->selling_price)->toBe(0.0);

    // Reloading the edit page (e.g. after a subsequent send-back) must not
    // resurrect the catalog price now that the flag correctly persisted.
    $this->actingAs($admin)
        ->get(route('medical-orders.edit', $data['medicalOrder']))
        ->assertInertia(fn ($page) => $page
            ->component('MedicalOrders/Edit')
            ->where('medicalOrder.order_items.0.is_package_included', true)
            ->where('medicalOrder.order_items.0.unit_price', 0)
            ->where('medicalOrder.order_items.0.selling_price', 0)
        );

    $service = app(MedicalOrderBillingService::class);
    expect($service->calculateItemTotal($stored))->toBe(0.0);
});

it('saving an untouched package-included item through update() keeps it free', function () {
    // Simulates exactly what a correctly-fixed Edit.vue now sends when the
    // nurse saves without touching the package item: the same fields
    // edit() renders it with (is_package_included: true, $0 prices, the
    // notes sentinel). Paired with the previous test — which confirms
    // edit() actually renders those exact values — this proves the full
    // send-back -> nurse-edit -> resend cycle no longer loses the flag.
    $admin = createPackageBillingAdmin();
    $data = createOrderWithPackageItem($admin);

    MedicalOrderInventory::create([
        'medical_order_id' => $data['medicalOrder']->id,
        'inventory_id' => $data['labInventory']->id,
        'item_type' => 'lab',
        'item_name' => $data['labInventory']->item_name,
        'quantity_required' => 1,
        'unit_price' => 0,
        'selling_price' => 0,
        'is_package_included' => true,
        'status' => MedicalOrderStatusEnum::PENDING->value,
        'notes' => 'Include Package - Not counted in billing',
    ]);

    $this->actingAs($admin)
        ->put(route('medical-orders.update', $data['medicalOrder']), [
            'order_details' => $data['medicalOrder']->order_details,
            'order_items' => [
                [
                    'item_type' => 'lab',
                    'item_name' => $data['labInventory']->item_name,
                    'quantity_required' => 1,
                    'status' => 'pending',
                    'inventory_id' => $data['labInventory']->id,
                    'unit_price' => 0,
                    'selling_price' => 0,
                    'is_package_included' => true,
                    'notes' => 'Include Package - Not counted in billing',
                ],
            ],
        ])
        ->assertRedirect();

    $stored = MedicalOrderInventory::where('medical_order_id', $data['medicalOrder']->id)
        ->where('item_type', 'lab')
        ->first();
    expect($stored->is_package_included)->toBeTrue();
    expect((float) $stored->unit_price)->toBe(0.0);
    expect((float) $stored->selling_price)->toBe(0.0);

    $service = app(MedicalOrderBillingService::class);
    expect($service->calculateItemTotal($stored))->toBe(0.0);
});

it('sends billing back to nurse without a reason', function () {
    $admin = createPackageBillingAdmin();
    $data = createOrderWithPackageItem($admin);

    MedicalOrderInventory::create([
        'medical_order_id' => $data['medicalOrder']->id,
        'inventory_id' => $data['rxInventory']->id,
        'item_type' => 'rx_medicine',
        'item_name' => $data['rxInventory']->item_name,
        'quantity_required' => 1,
        'selling_price' => 25.00,
        'status' => MedicalOrderStatusEnum::PENDING->value,
    ]);

    $billing = Billing::create([
        'patient_id' => $data['patient']->id,
        'medical_order_id' => $data['medicalOrder']->id,
        'visit_id' => $data['visit']->id,
        'amount' => 25.00,
        'status' => 'sent_to_account',
        'billing_date' => now(),
    ]);

    $response = $this->actingAs($admin)->patch(
        route('billings.send-back-to-nurse', $billing),
        [] // no reason
    );

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $billing->refresh();
    expect($billing->status->value)->toBe('revision');

    $data['medicalOrder']->refresh();
    expect($data['medicalOrder']->status)->toBe(MedicalOrderStatusEnum::PENDING);
});
