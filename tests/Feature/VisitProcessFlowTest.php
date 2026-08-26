<?php

use App\Enums\BillingStatusEnum;
use App\Enums\MedicalOrderStatusEnum;
use App\Models\Billing;
use App\Models\MedicalOrder;
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
        'create_visits',
        'view_visits',
        'edit_visits',
        'assign_visits',
        'notify_visits',
        'create_billings',
        'view_billings',
        'edit_billings',
        'complete_billings',
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

function createFlowAdmin(): User
{
    $domainRole = StaffRole::firstOrCreate(['name' => 'admin'], ['description' => 'System Administrator']);
    $spatieRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    $user = User::factory()->create();
    $user->assignRole($spatieRole);
    Staff::factory()->create(['user_id' => $user->id, 'role_id' => $domainRole->id]);
    app(PermissionRegistrar::class)->forgetCachedPermissions();

    return $user->fresh();
}

// ─── Visit Creation ───────────────────────────────────────────────────────────

it('creates a visit with awaiting_assignment status', function () {
    $user = createFlowAdmin();
    $patient = Patient::factory()->create();

    $response = $this->actingAs($user)->post('/visits', [
        'patient_id' => $patient->id,
        'visit_date_time' => now()->toDateTimeString(),
        'status' => 'awaiting_assignment',
        'notes' => 'Patient walk-in',
    ]);

    $response->assertRedirect('/visits');
    $visit = Visit::where('patient_id', $patient->id)->first();
    expect($visit)->not->toBeNull();
    expect($visit->status->value)->toBe('awaiting_assignment');
});

// ─── Staff Assignment ─────────────────────────────────────────────────────────

it('assigns a staff member to the visit and changes status to assigned', function () {
    $user = createFlowAdmin();
    $staff = $user->staff;
    $patient = Patient::factory()->create();

    $visit = Visit::factory()->create([
        'patient_id' => $patient->id,
        'status' => 'awaiting_assignment',
    ]);

    $response = $this->actingAs($user)->patch("/visits/{$visit->id}/assign-process", [
        'staff_id' => $staff->id,
    ]);

    $response->assertRedirect();
    $visit->refresh();
    expect($visit->staff_id)->toBe($staff->id);
    expect($visit->status->value)->toBe(Visit::STATUS_ASSIGNED);
});

it('cannot assign staff without the required permission', function () {
    $restrictedRole = Role::firstOrCreate(['name' => 'receptionist', 'guard_name' => 'web']);
    $restrictedUser = User::factory()->create();
    $restrictedUser->assignRole($restrictedRole);
    Staff::factory()->create(['user_id' => $restrictedUser->id, 'role_id' => $restrictedRole->id]);

    $visit = Visit::factory()->create(['status' => 'awaiting_assignment']);
    $targetStaff = Staff::factory()->create();

    $this->actingAs($restrictedUser)
        ->patch("/visits/{$visit->id}/assign-process", ['staff_id' => $targetStaff->id])
        ->assertForbidden();
});

// ─── Visit Status Transition to awaiting_accountant ──────────────────────────

it('updates visit status to awaiting_accountant after processing', function () {
    $user = createFlowAdmin();
    $patient = Patient::factory()->create();
    $staff = $user->staff;

    $visit = Visit::factory()->create([
        'patient_id' => $patient->id,
        'staff_id' => $staff->id,
        'status' => 'assigned',
    ]);

    $response = $this->actingAs($user)->patch("/visits/{$visit->id}", [
        'patient_id' => $patient->id,
        'visit_date_time' => $visit->visit_date_time->toDateTimeString(),
        'status' => 'awaiting_accountant',
    ]);

    $response->assertRedirect();
    $visit->refresh();
    expect($visit->status->value)->toBe('awaiting_accountant');
});

// ─── Billing Creation ─────────────────────────────────────────────────────────

it('creates a billing record linked to a visit', function () {
    $user = createFlowAdmin();
    $patient = Patient::factory()->create();
    $staff = $user->staff;

    $visit = Visit::factory()->create([
        'patient_id' => $patient->id,
        'staff_id' => $staff->id,
        'status' => 'awaiting_accountant',
    ]);

    $response = $this->actingAs($user)->post('/billings', [
        'visit_id' => $visit->id,
        'amount' => 75.00,
        'status' => 'pending',
        'billing_date' => now()->toDateString(),
        'notes' => 'Consultation fee',
    ]);

    $response->assertRedirect(route('billings.index'));
    $billing = Billing::where('visit_id', $visit->id)->first();
    expect($billing)->not->toBeNull();
    expect((float) $billing->amount)->toBe(75.0);
    expect($billing->status->value)->toBe('pending');
});

// ─── Billing Checkout ─────────────────────────────────────────────────────────

it('completes payment and marks billing as paid and visit as completed', function () {
    $user = createFlowAdmin();
    $patient = Patient::factory()->create();
    $staff = $user->staff;

    $visit = Visit::factory()->create([
        'patient_id' => $patient->id,
        'staff_id' => $staff->id,
        'status' => 'awaiting_accountant',
    ]);

    $medicalOrder = MedicalOrder::create([
        'visit_id' => $visit->id,
        'patient_id' => $patient->id,
        'staff_id' => $staff->id,
        'order_details' => 'Consultation',
        'status' => MedicalOrderStatusEnum::COMPLETED,
        'priority' => 'routine',
        'notes' => 'Routine checkup',
        'ordered_at' => now(),
        'completed_at' => now(),
    ]);

    $billing = Billing::create([
        'bill_no' => Billing::generateBillNo(),
        'patient_id' => $patient->id,
        'visit_id' => $visit->id,
        'medical_order_id' => $medicalOrder->id,
        'doctor_id' => $staff->id,
        'amount' => 75.00,
        'status' => BillingStatusEnum::PENDING,
        'billing_date' => now(),
        'notes' => 'Consultation fee',
    ]);

    $response = $this->actingAs($user)->patch("/billings/{$billing->id}/complete-payment", [
        'payment_method' => 'Cash',
    ]);

    $response->assertRedirect(route('billings.index'));

    $billing->refresh();
    $visit->refresh();
    $medicalOrder->refresh();

    expect($billing->status->value)->toBe('paid');
    expect($visit->status->value)->toBe(Visit::STATUS_COMPLETED);
    expect($medicalOrder->status->value)->toBe(MedicalOrderStatusEnum::PAID->value);
});

// ─── Full End-to-End Flow ─────────────────────────────────────────────────────

it('completes the full visit lifecycle from creation to billing checkout', function () {
    $user = createFlowAdmin();
    $staff = $user->staff;
    $patient = Patient::factory()->create();

    // Step 1: Create visit
    $createResponse = $this->actingAs($user)->post('/visits', [
        'patient_id' => $patient->id,
        'visit_date_time' => now()->toDateTimeString(),
        'status' => 'awaiting_assignment',
        'notes' => 'Patient walk-in',
    ]);
    $createResponse->assertRedirect('/visits');

    $visit = Visit::where('patient_id', $patient->id)->latest()->first();
    expect($visit->status->value)->toBe('awaiting_assignment');

    // Step 2: Assign staff
    $assignResponse = $this->actingAs($user)->patch("/visits/{$visit->id}/assign-process", [
        'staff_id' => $staff->id,
    ]);
    $assignResponse->assertRedirect();
    $visit->refresh();
    expect($visit->status->value)->toBe(Visit::STATUS_ASSIGNED);

    // Step 3: Move visit to awaiting_accountant
    $processResponse = $this->actingAs($user)->patch("/visits/{$visit->id}", [
        'patient_id' => $patient->id,
        'visit_date_time' => $visit->visit_date_time->toDateTimeString(),
        'status' => 'awaiting_accountant',
    ]);
    $processResponse->assertRedirect();
    $visit->refresh();
    expect($visit->status->value)->toBe('awaiting_accountant');

    // Step 4: Create billing
    $billingResponse = $this->actingAs($user)->post('/billings', [
        'visit_id' => $visit->id,
        'amount' => 150.00,
        'status' => 'pending',
        'billing_date' => now()->toDateString(),
        'notes' => 'Full consultation',
    ]);
    $billingResponse->assertRedirect(route('billings.index'));

    $billing = Billing::where('visit_id', $visit->id)->first();
    expect($billing)->not->toBeNull();
    expect($billing->status->value)->toBe('pending');

    // Step 5: Checkout / complete payment
    $checkoutResponse = $this->actingAs($user)->patch("/billings/{$billing->id}/complete-payment", [
        'payment_method' => 'Cash',
    ]);
    $checkoutResponse->assertRedirect(route('billings.index'));

    $billing->refresh();
    $visit->refresh();

    expect($billing->status->value)->toBe('paid');
    expect($visit->status->value)->toBe(Visit::STATUS_COMPLETED);
});

// ─── Guard Rails ──────────────────────────────────────────────────────────────

it('prevents guests from creating a visit', function () {
    $patient = Patient::factory()->create();

    $this->post('/visits', [
        'patient_id' => $patient->id,
        'visit_date_time' => now()->toDateTimeString(),
        'status' => 'awaiting_assignment',
    ])->assertRedirect(route('login'));
});

it('prevents guests from creating a billing', function () {
    $visit = Visit::factory()->create(['status' => 'awaiting_accountant']);

    $this->post('/billings', [
        'visit_id' => $visit->id,
        'amount' => 50.00,
        'status' => 'pending',
        'billing_date' => now()->toDateString(),
    ])->assertRedirect(route('login'));
});

it('prevents guests from completing a payment', function () {
    $billing = Billing::create([
        'bill_no' => Billing::generateBillNo(),
        'patient_id' => Patient::factory()->create()->id,
        'amount' => 50.00,
        'status' => BillingStatusEnum::PENDING,
        'billing_date' => now(),
    ]);

    $this->patch("/billings/{$billing->id}/complete-payment")
        ->assertRedirect(route('login'));
});
