<?php

use App\Enums\MedicalOrderPriorityEnum;
use App\Enums\MedicalOrderStatusEnum;
use App\Models\MedicalOrder;
use App\Models\MedicalOrderInventory;
use App\Models\Patient;
use App\Models\PatientFile;
use App\Models\Staff;
use App\Models\StaffRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

uses(RefreshDatabase::class);

beforeEach(function () {
    Permission::firstOrCreate(['name' => 'complete_medical_order_items', 'guard_name' => 'web']);

    $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    $adminRole->syncPermissions(['complete_medical_order_items']);
    app(PermissionRegistrar::class)->forgetCachedPermissions();
});

function createAdminUserForLabResults(): User
{
    // Create domain (StaffRole) entry so StaffObserver does not strip roles
    $domainRole = StaffRole::firstOrCreate(
        ['name' => 'admin'],
        ['description' => 'System Administrator'],
    );

    $spatieRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

    $user = User::factory()->create();
    $user->assignRole($spatieRole);
    app(PermissionRegistrar::class)->forgetCachedPermissions();

    Staff::factory()->create([
        'user_id' => $user->id,
        'role_id' => $domainRole->id,
    ]);

    $user = $user->fresh();
    app(PermissionRegistrar::class)->forgetCachedPermissions();

    return $user;
}

function createLabOrderWithItem(User $user, Patient $patient): array
{
    $medicalOrder = MedicalOrder::create([
        'patient_id' => $patient->id,
        'staff_id' => $user->staff->id,
        'order_details' => 'Laboratory request',
        'status' => MedicalOrderStatusEnum::PENDING,
        'priority' => MedicalOrderPriorityEnum::ROUTINE,
        'ordered_at' => now(),
    ]);

    $item = MedicalOrderInventory::create([
        'medical_order_id' => $medicalOrder->id,
        'item_type' => 'lab',
        'item_name' => 'Blood Glucose',
        'quantity_required' => 1,
        'status' => MedicalOrderStatusEnum::PENDING,
    ]);

    return [$medicalOrder, $item];
}

it('creates a lab results patient file when a lab result value is saved', function () {
    Storage::fake('local');

    $user = createAdminUserForLabResults();
    $patient = Patient::factory()->create();
    [$medicalOrder, $item] = createLabOrderWithItem($user, $patient);

    $response = $this->actingAs($user)->patch(
        "/medical-orders/{$medicalOrder->id}/items/{$item->id}/lab-result",
        [
            'result_value' => '5.2',
            'result_unit' => 'mmol/L',
            'result_notes' => 'Fasting sample',
        ]
    );

    $response->assertRedirect();

    $patientFile = PatientFile::where('patient_id', $patient->id)
        ->where('medical_order_id', $medicalOrder->id)
        ->where('type', 'lab_result')
        ->first();

    expect($patientFile)->not->toBeNull();
    expect($patientFile->file->name)->toBe('Lab Results - Order '.$medicalOrder->id.'.pdf');
    expect($patientFile->file->mime_type)->toBe('application/pdf');

    Storage::disk('local')->assertExists($patientFile->file->path);
});

it('keeps a single generated lab file per order when results are saved repeatedly', function () {
    Storage::fake('local');

    $user = createAdminUserForLabResults();
    $patient = Patient::factory()->create();
    [$medicalOrder, $item] = createLabOrderWithItem($user, $patient);

    $secondItem = MedicalOrderInventory::create([
        'medical_order_id' => $medicalOrder->id,
        'item_type' => 'lab',
        'item_name' => 'HIV Antibody',
        'quantity_required' => 1,
        'status' => MedicalOrderStatusEnum::PENDING,
    ]);

    $this->actingAs($user)->patch(
        "/medical-orders/{$medicalOrder->id}/items/{$item->id}/lab-result",
        ['result_value' => '5.2', 'result_unit' => 'mmol/L', 'result_notes' => null]
    )->assertRedirect();

    $this->actingAs($user)->patch(
        "/medical-orders/{$medicalOrder->id}/items/{$secondItem->id}/lab-result",
        ['result_value' => 'Negative', 'result_unit' => null, 'result_notes' => null]
    )->assertRedirect();

    $generatedFiles = PatientFile::where('patient_id', $patient->id)
        ->where('medical_order_id', $medicalOrder->id)
        ->where('type', 'lab_result')
        ->get();

    expect($generatedFiles)->toHaveCount(1);
    Storage::disk('local')->assertExists($generatedFiles->first()->file->path);
});

it('does not create a patient file for non-lab order items', function () {
    Storage::fake('local');

    $user = createAdminUserForLabResults();
    $patient = Patient::factory()->create();

    $medicalOrder = MedicalOrder::create([
        'patient_id' => $patient->id,
        'staff_id' => $user->staff->id,
        'order_details' => 'Medication order',
        'status' => MedicalOrderStatusEnum::PENDING,
        'priority' => MedicalOrderPriorityEnum::ROUTINE,
        'ordered_at' => now(),
    ]);

    $item = MedicalOrderInventory::create([
        'medical_order_id' => $medicalOrder->id,
        'item_type' => 'medication',
        'item_name' => 'Paracetamol',
        'quantity_required' => 1,
        'status' => MedicalOrderStatusEnum::PENDING,
    ]);

    $this->actingAs($user)->patch(
        "/medical-orders/{$medicalOrder->id}/items/{$item->id}/lab-result",
        ['result_value' => 'n/a', 'result_unit' => null, 'result_notes' => null]
    )->assertRedirect();

    expect(PatientFile::where('medical_order_id', $medicalOrder->id)->count())->toBe(0);
});
