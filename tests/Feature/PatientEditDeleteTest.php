<?php

use App\Models\ActivityLog;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Staff;
use App\Models\StaffRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

uses(RefreshDatabase::class);

function patientAdmin(): User
{
    $domainRole = StaffRole::firstOrCreate(['name' => 'admin'], ['description' => 'System Administrator']);
    $user = User::factory()->create();
    $user->assignRole(Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']));
    Staff::factory()->create(['user_id' => $user->id, 'role_id' => $domainRole->id]);
    app(PermissionRegistrar::class)->forgetCachedPermissions();

    return $user->fresh();
}

it('updates a patient via multipart POST with _method=put', function () {
    $user = patientAdmin();
    $patient = Patient::factory()->create(['nationality' => 'Cambodian']);

    $this->actingAs($user)
        ->post("/patients/update?patient={$patient->id}", [
            '_method' => 'put',
            'name' => 'Changed',
            'surname' => $patient->surname,
            'nationality' => 'Thai',
            'date_of_birth_day' => 5,
            'date_of_birth_month' => 4,
            'date_of_birth_year' => 1996,
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('patients.index'));

    expect($patient->fresh()->name)->toBe('Changed')
        ->and($patient->fresh()->nationality)->toBe('Thai');
});

it('deletes a patient without history and logs it', function () {
    $user = patientAdmin();
    $patient = Patient::factory()->create();

    $this->actingAs($user)
        ->delete("/patients/destroy?patient={$patient->id}")
        ->assertRedirect(route('patients.index'));

    expect(Patient::find($patient->id))->toBeNull()
        ->and(ActivityLog::where('action', 'deleted')->where('subject_id', $patient->id)->exists())->toBeTrue();
});

it('refuses to delete a patient with appointments', function () {
    $user = patientAdmin();
    $patient = Patient::factory()->create();
    Appointment::factory()->create(['patient_id' => $patient->id]);

    $this->actingAs($user)
        ->delete("/patients/destroy?patient={$patient->id}")
        ->assertSessionHas('error');

    expect(Patient::find($patient->id))->not->toBeNull();
});

it('forbids delete without permission', function () {
    $domainRole = StaffRole::firstOrCreate(['name' => 'receptionist'], ['description' => 'r']);
    $user = User::factory()->create();
    Staff::factory()->create(['user_id' => $user->id, 'role_id' => $domainRole->id]);
    $patient = Patient::factory()->create();

    $this->actingAs($user->fresh())
        ->delete("/patients/destroy?patient={$patient->id}")
        ->assertForbidden();
});

it('still renders the patient label PDF', function () {
    $user = patientAdmin();
    $patient = Patient::factory()->create(['date_of_birth_day' => 5, 'date_of_birth_month' => 4, 'date_of_birth_year' => 1996]);

    $response = $this->actingAs($user)->get("/patients/label?patient={$patient->id}");
    $response->assertOk();
    expect($response->getContent())->toStartWith('%PDF');
});
