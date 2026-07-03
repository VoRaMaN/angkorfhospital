<?php

use App\Enums\MedicalOrderPriorityEnum;
use App\Enums\MedicalOrderStatusEnum;
use App\Models\MedicalOrder;
use App\Models\Patient;
use App\Models\PatientFile;
use App\Models\Staff;
use App\Models\StaffRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

uses(RefreshDatabase::class);

beforeEach(function () {
    Permission::firstOrCreate(['name' => 'create_files', 'guard_name' => 'web']);
    Permission::firstOrCreate(['name' => 'view_files', 'guard_name' => 'web']);

    $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    $adminRole->syncPermissions(['create_files', 'view_files']);
    app(PermissionRegistrar::class)->forgetCachedPermissions();
});

function createAdminUserForUpload(): User
{
    // Create domain (StaffRole) entry so StaffObserver does not strip roles
    $domainRole = StaffRole::firstOrCreate(
        ['name' => 'admin'],
        ['description' => 'System Administrator'],
    );

    // Create Spatie role with permissions
    $spatieRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

    $user = User::factory()->create();
    $user->assignRole($spatieRole);
    app(PermissionRegistrar::class)->forgetCachedPermissions();

    // Use domain role id — prevents StaffObserver from calling syncRoles([])
    Staff::factory()->create([
        'user_id' => $user->id,
        'role_id' => $domainRole->id,
    ]);

    // Refresh after observer may have run syncRoles
    $user = $user->fresh();
    app(PermissionRegistrar::class)->forgetCachedPermissions();

    return $user;
}

it('uploads a lab file linked to a medical order', function () {
    Storage::fake('local');

    $user = createAdminUserForUpload();
    $patient = Patient::factory()->create();

    $medicalOrder = MedicalOrder::create([
        'patient_id' => $patient->id,
        'staff_id' => $user->staff->id,
        'order_details' => 'Laboratory request',
        'status' => MedicalOrderStatusEnum::PENDING,
        'priority' => MedicalOrderPriorityEnum::ROUTINE,
        'ordered_at' => now(),
    ]);

    $file = UploadedFile::fake()->create('lab-report.pdf', 100, 'application/pdf');

    expect($user->can('create_files'))->toBeTrue();

    $response = $this->actingAs($user)->post('/patient-files', [
        'file' => $file,
        'patient_id' => $patient->id,
        'type' => 'lab_result',
        'medical_order_id' => $medicalOrder->id,
    ]);

    $response->assertRedirect();

    $patientFile = PatientFile::where('patient_id', $patient->id)
        ->where('medical_order_id', $medicalOrder->id)
        ->first();

    expect($patientFile)->not->toBeNull();
    $patientFile->load('file');

    Storage::disk('local')->assertExists($patientFile->file->path);

    $this->assertDatabaseHas('patient_files', [
        'patient_id' => $patient->id,
        'medical_order_id' => $medicalOrder->id,
        'type' => 'lab_result',
    ]);
});
