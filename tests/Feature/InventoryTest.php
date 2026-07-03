<?php

use App\Models\Inventory;
use App\Models\Staff;
use App\Models\StaffRole;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

beforeEach(function () {
    $permissions = [
        'view_inventory',
        'create_inventory',
        'edit_inventory',
        'delete_inventory',
    ];

    foreach ($permissions as $perm) {
        Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
    }

    $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    $role->syncPermissions($permissions);
});

function createAdminUser(): User
{
    $domainRole = StaffRole::firstOrCreate(['name' => 'admin'], ['description' => 'System Administrator']);
    $spatieRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    $user = User::factory()->create();
    $user->assignRole($spatieRole);
    Staff::factory()->create(['user_id' => $user->id, 'role_id' => $domainRole->id]);
    app(PermissionRegistrar::class)->forgetCachedPermissions();

    return $user->fresh();
}

test('guests cannot access inventory index', function () {
    $this->get(route('inventory.index'))
        ->assertRedirect(route('login'));
});

test('admin can view inventory index', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    Inventory::factory()->count(3)->create();

    $this->actingAs($user)
        ->get(route('inventory.index'))
        ->assertSuccessful();
});

test('admin can view rx medicine listing', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    Inventory::factory()->rxMedicine()->count(2)->create();

    $this->actingAs($user)
        ->get(route('inventory.rx-medicine'))
        ->assertSuccessful();
});

test('admin can view lab inventory listing', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    Inventory::factory()->labSupply()->count(2)->create();

    $this->actingAs($user)
        ->get(route('inventory.lab-inventory'))
        ->assertSuccessful();
});

test('admin can view inventory show page with all fields', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    $inventory = Inventory::factory()->create([
        'selling_price' => 25.50,
        'unit_price' => 15.00,
        'category' => 'Antibiotic',
        'barcode' => '1234567890123',
    ]);

    $this->actingAs($user)
        ->get(route('inventory.show', $inventory))
        ->assertSuccessful();
});

test('admin can update inventory with all fields including prices', function () {
    $user = createAdminUser();

    $inventory = Inventory::factory()->rxMedicine()->create();

    $this->actingAs($user)
        ->put(route('inventory.update', $inventory), [
            'item_name' => 'Updated Medicine',
            'unit_price' => 10.50,
            'selling_price' => 18.99,
            'quantity' => 100,
            'unit' => 'tablets',
            'minimum_stock' => 20,
            'expiry_date' => now()->addYear()->format('Y-m-d'),
            'alert_days' => 30,
            'category' => 'Analgesic',
            'barcode' => '9876543210987',
            'dose_unit' => 'mg',
            'total_per_box' => 30,
            'location' => 'Pharmacy Shelf A',
            'notes' => 'Keep refrigerated',
        ])
        ->assertRedirect();

    $inventory->refresh();
    expect($inventory->item_name)->toBe('Updated Medicine');
    expect((float) $inventory->unit_price)->toBe(10.50);
    expect((float) $inventory->selling_price)->toBe(18.99);
    expect($inventory->quantity)->toBe(100);
    expect($inventory->alert_days)->toBe(30);
    expect($inventory->category)->toBe('Analgesic');
    expect($inventory->barcode)->toBe('9876543210987');
    expect($inventory->dose_unit)->toBe('mg');
    expect($inventory->total_per_box)->toBe(30);
    expect($inventory->location)->toBe('Pharmacy Shelf A');
    expect($inventory->notes)->toBe('Keep refrigerated');
});

test('update validates selling_price as numeric', function () {
    $user = createAdminUser();

    $inventory = Inventory::factory()->create();

    $this->actingAs($user)
        ->put(route('inventory.update', $inventory), [
            'selling_price' => 'not-a-number',
        ])
        ->assertSessionHasErrors('selling_price');
});

test('update validates alert_days as integer', function () {
    $user = createAdminUser();

    $inventory = Inventory::factory()->create();

    $this->actingAs($user)
        ->put(route('inventory.update', $inventory), [
            'alert_days' => -5,
        ])
        ->assertSessionHasErrors('alert_days');
});

test('rx medicine listing returns inventory data with prices', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    Inventory::factory()->rxMedicine()->create([
        'item_name' => 'Amoxicillin 500mg',
        'selling_price' => 12.50,
        'unit_price' => 8.00,
    ]);

    $response = $this->actingAs($user)
        ->get(route('inventory.rx-medicine'));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('Inventories/RXMedicineIndex')
        ->has('rxMedicines', 1)
        ->where('rxMedicines.0.item_name', 'Amoxicillin 500mg')
        ->where('rxMedicines.0.selling_price', '12.50')
    );
});

test('inventory model correctly reports stock status', function () {
    $inStock = Inventory::factory()->create(['quantity' => 100, 'minimum_stock' => 10]);
    $lowStock = Inventory::factory()->lowStock()->create();
    $outOfStock = Inventory::factory()->outOfStock()->create();

    expect($inStock->status)->toBe('In Stock');
    expect($lowStock->status)->toBe('Low Stock');
    expect($outOfStock->status)->toBe('Out of Stock');
});
