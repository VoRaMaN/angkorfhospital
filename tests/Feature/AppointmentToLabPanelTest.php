<?php

use App\Models\Appointment;
use App\Models\MedicalOrderInventory;
use App\Models\Patient;
use App\Models\SpecialItem;
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
        'assign_visits',
        'view_appointments',
        'edit_appointments',
        'view_patients',
    ];

    foreach ($permissions as $perm) {
        Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
    }

    $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    $role->syncPermissions($permissions);
});

function createLabPanelFlowAdmin(): User
{
    $domainRole = StaffRole::firstOrCreate(['name' => 'admin'], ['description' => 'System Administrator']);
    $spatieRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    $user = User::factory()->create();
    $user->assignRole($spatieRole);
    Staff::factory()->create(['user_id' => $user->id, 'role_id' => $domainRole->id]);
    app(PermissionRegistrar::class)->forgetCachedPermissions();

    return $user->fresh();
}

it('converts an appointment to a visit and auto-generates lab items from its IVF monitoring flags', function () {
    SpecialItem::create(['name' => 'TVS 25', 'unit_price' => 25.00, 'is_active' => true]);

    $user = createLabPanelFlowAdmin();
    $staff = $user->staff;
    $patient = Patient::factory()->create();

    $appointment = Appointment::factory()->create([
        'patient_id' => $patient->id,
        'status' => 'confirmed',
        'is_tvs' => true,
        'is_hormone_test' => true,
        'is_beta_hcg' => false,
    ]);

    // Step 1: Convert the appointment into a visit.
    $convertResponse = $this->actingAs($user)->post("/appointments/{$appointment->id}/convert-to-visit");
    $convertResponse->assertRedirect();

    $visit = Visit::where('appointment_id', $appointment->id)->first();
    expect($visit)->not->toBeNull();
    expect($visit->patient_id)->toBe($patient->id);

    // Step 2: Assign staff, which triggers medical order generation.
    $assignResponse = $this->actingAs($user)->patch("/visits/{$visit->id}/assign-process", [
        'staff_id' => $staff->id,
    ]);
    $assignResponse->assertRedirect();

    $visit->refresh();
    $medicalOrder = $visit->medicalOrders()->first();
    expect($medicalOrder)->not->toBeNull();

    $labItems = MedicalOrderInventory::where('medical_order_id', $medicalOrder->id)
        ->where('item_type', 'lab')
        ->get();

    expect($labItems)->toHaveCount(2);

    $tvsItem = $labItems->firstWhere('item_name', 'TVS (Transvaginal Ultrasound Scan)');
    expect($tvsItem)->not->toBeNull();
    expect((float) $tvsItem->selling_price)->toBe(25.0);

    $hormoneItem = $labItems->firstWhere('item_name', 'Hormone Test');
    expect($hormoneItem)->not->toBeNull();
    expect((float) $hormoneItem->selling_price)->toBe(0.0);

    // Step 3: Confirm the order now shows up in the Lab Panel workflow list for that patient.
    $labPanelResponse = $this->actingAs($user)->get('/lab-panels?lab_start_date='.today()->toDateString().'&lab_end_date='.today()->toDateString());
    $labPanelResponse->assertOk();
    $labPanelResponse->assertInertia(fn ($page) => $page
        ->has('activeLabOrders', 1)
        ->where('activeLabOrders.0.patient_id', $patient->id)
        ->where('activeLabOrders.0.lab_items', fn ($items) => count($items) === 2)
    );
});

it('shows a converted visit in the patient\'s Visit History tab immediately, before any staff is assigned', function () {
    $user = createLabPanelFlowAdmin();
    $patient = Patient::factory()->create();

    $appointment = Appointment::factory()->create([
        'patient_id' => $patient->id,
        'status' => 'confirmed',
        'is_tvs' => true,
    ]);

    $this->actingAs($user)->post("/appointments/{$appointment->id}/convert-to-visit")->assertRedirect();

    $visit = Visit::where('appointment_id', $appointment->id)->first();
    expect($visit)->not->toBeNull();

    $patientShowResponse = $this->actingAs($user)->get("/patients/show?patient={$patient->id}");
    $patientShowResponse->assertOk();
    $patientShowResponse->assertInertia(fn ($page) => $page
        ->has('patient.visit_history', 1)
        ->where('patient.visit_history.0.id', $visit->id)
    );
});

it('does not create a duplicate visit when converting the same appointment twice', function () {
    $user = createLabPanelFlowAdmin();
    $patient = Patient::factory()->create();

    $appointment = Appointment::factory()->create([
        'patient_id' => $patient->id,
        'status' => 'confirmed',
        'is_tvs' => true,
    ]);

    $this->actingAs($user)->post("/appointments/{$appointment->id}/convert-to-visit")->assertRedirect();
    $this->actingAs($user)->post("/appointments/{$appointment->id}/convert-to-visit")->assertRedirect();

    expect(Visit::where('appointment_id', $appointment->id)->count())->toBe(1);
});

it('creates no lab items when the appointment has no IVF monitoring flags set', function () {
    $user = createLabPanelFlowAdmin();
    $staff = $user->staff;
    $patient = Patient::factory()->create();

    $appointment = Appointment::factory()->create([
        'patient_id' => $patient->id,
        'status' => 'confirmed',
        'is_tvs' => false,
        'is_hormone_test' => false,
        'is_beta_hcg' => false,
    ]);

    $this->actingAs($user)->post("/appointments/{$appointment->id}/convert-to-visit")->assertRedirect();
    $visit = Visit::where('appointment_id', $appointment->id)->first();

    $this->actingAs($user)->patch("/visits/{$visit->id}/assign-process", [
        'staff_id' => $staff->id,
    ])->assertRedirect();

    $medicalOrder = $visit->fresh()->medicalOrders()->first();
    expect(MedicalOrderInventory::where('medical_order_id', $medicalOrder->id)->where('item_type', 'lab')->count())->toBe(0);
});
