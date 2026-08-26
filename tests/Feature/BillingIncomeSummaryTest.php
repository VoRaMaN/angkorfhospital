<?php

use App\Enums\BillingStatusEnum;
use App\Models\Billing;
use App\Models\Patient;
use App\Models\Staff;
use App\Models\StaffRole;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

beforeEach(function () {
    $permissions = ['view_billings', 'edit_billings'];
    foreach ($permissions as $perm) {
        Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
    }
    $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    $role->syncPermissions($permissions);
});

function createIncomeSummaryAdmin(): User
{
    $domainRole = StaffRole::firstOrCreate(['name' => 'admin'], ['description' => 'System Administrator']);
    $spatieRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    $user = User::factory()->create();
    $user->assignRole($spatieRole);
    Staff::factory()->create(['user_id' => $user->id, 'role_id' => $domainRole->id]);
    app(PermissionRegistrar::class)->forgetCachedPermissions();

    return $user->fresh();
}

test('marking a billing paid via complete-payment requires and stores a payment method', function () {
    $user = createIncomeSummaryAdmin();
    $patient = Patient::factory()->create();
    $billing = Billing::create([
        'bill_no' => Billing::generateBillNo(),
        'patient_id' => $patient->id,
        'amount' => 50,
        'status' => BillingStatusEnum::PENDING,
        'billing_date' => now(),
    ]);

    $this->actingAs($user)
        ->patch("/billings/{$billing->id}/complete-payment", [])
        ->assertSessionHasErrors('payment_method');

    $this->actingAs($user)
        ->patch("/billings/{$billing->id}/complete-payment", ['payment_method' => 'Canadia Bank']);

    $billing->refresh();
    expect($billing->status)->toBe(BillingStatusEnum::PAID);
    expect($billing->payment_method)->toBe('Canadia Bank');
});

test('marking a billing paid via update-status requires and stores a payment method', function () {
    $user = createIncomeSummaryAdmin();
    $patient = Patient::factory()->create();
    $billing = Billing::create([
        'bill_no' => Billing::generateBillNo(),
        'patient_id' => $patient->id,
        'amount' => 50,
        'status' => BillingStatusEnum::PENDING,
        'billing_date' => now(),
    ]);

    $this->actingAs($user)
        ->patch(route('billings.update-status', $billing), ['status' => 'paid'])
        ->assertSessionHasErrors('payment_method');

    $this->actingAs($user)
        ->patch(route('billings.update-status', $billing), ['status' => 'paid', 'payment_method' => 'Cash']);

    $billing->refresh();
    expect($billing->status)->toBe(BillingStatusEnum::PAID);
    expect($billing->payment_method)->toBe('Cash');
});

test('income summary report renders todays paid bills with grand total and cashier name', function () {
    $user = createIncomeSummaryAdmin();

    $patientA = Patient::factory()->create([
        'date_of_birth_year' => 1990, 'date_of_birth_month' => 1, 'date_of_birth_day' => 1,
    ]);
    $patientB = Patient::factory()->create([
        'date_of_birth_year' => 1985, 'date_of_birth_month' => 6, 'date_of_birth_day' => 15,
    ]);

    Billing::create([
        'bill_no' => Billing::generateBillNo(),
        'patient_id' => $patientA->id,
        'amount' => 100,
        'status' => BillingStatusEnum::PAID,
        'payment_method' => 'Cash',
        'billing_date' => now(),
    ]);
    Billing::create([
        'bill_no' => Billing::generateBillNo(),
        'patient_id' => $patientB->id,
        'amount' => 50,
        'discount_amount' => 5,
        'status' => BillingStatusEnum::PAID,
        'payment_method' => 'Canadia Bank',
        'billing_date' => now(),
    ]);
    // A pending billing today must NOT appear in the income summary.
    Billing::create([
        'bill_no' => Billing::generateBillNo(),
        'patient_id' => $patientA->id,
        'amount' => 30,
        'status' => BillingStatusEnum::PENDING,
        'billing_date' => now(),
    ]);

    $response = $this->actingAs($user)->get('/billings-print-today?closed_by=Mrs. Norn Chheavbouy');

    $response->assertOk();
    $response->assertHeader('content-type', 'application/pdf');
});
