<?php

use App\Models\Patient;
use App\Models\Staff;
use App\Models\User;
use App\Models\Visit;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    foreach (['create_visits', 'view_visits'] as $perm) {
        Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
    }

    $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    $role->syncPermissions(['create_visits', 'view_visits']);
});

function createVisitTestAdmin(): User
{
    $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    $user = User::factory()->create();
    $user->assignRole($role);
    Staff::factory()->create(['user_id' => $user->id, 'role_id' => $role->id]);

    return $user;
}

it('allows creating a visit when no visit exists today for the patient', function () {
    $user = createVisitTestAdmin();
    $patient = Patient::factory()->create();

    $response = $this->actingAs($user)->post('/visits', [
        'patient_id' => $patient->id,
        'visit_date_time' => now()->toDateTimeString(),
        'status' => 'awaiting_assignment',
        'notes' => 'First visit today',
    ]);

    $response->assertRedirect('/visits');
    expect(Visit::where('patient_id', $patient->id)->count())->toBe(1);
});

it('blocks creating a duplicate visit on the same day', function () {
    $user = createVisitTestAdmin();
    $patient = Patient::factory()->create();

    Visit::factory()->create([
        'patient_id' => $patient->id,
        'visit_date_time' => now(),
        'status' => 'awaiting_assignment',
    ]);

    $response = $this->actingAs($user)->post('/visits', [
        'patient_id' => $patient->id,
        'visit_date_time' => now()->toDateTimeString(),
        'status' => 'awaiting_assignment',
        'notes' => 'Duplicate visit',
    ]);

    $response->assertRedirect();
    expect(Visit::where('patient_id', $patient->id)->count())->toBe(1);
});

it('allows a second visit on a different day', function () {
    $user = createVisitTestAdmin();
    $patient = Patient::factory()->create();

    Visit::factory()->create([
        'patient_id' => $patient->id,
        'visit_date_time' => now()->subDay(),
        'status' => 'completed',
    ]);

    $response = $this->actingAs($user)->post('/visits', [
        'patient_id' => $patient->id,
        'visit_date_time' => now()->toDateTimeString(),
        'status' => 'awaiting_assignment',
        'notes' => 'New day visit',
    ]);

    $response->assertRedirect('/visits');
    expect(Visit::where('patient_id', $patient->id)->count())->toBe(2);
});

it('allows a new visit on same day if the existing visit is cancelled', function () {
    $user = createVisitTestAdmin();
    $patient = Patient::factory()->create();

    Visit::factory()->create([
        'patient_id' => $patient->id,
        'visit_date_time' => now(),
        'status' => 'cancelled',
    ]);

    $response = $this->actingAs($user)->post('/visits', [
        'patient_id' => $patient->id,
        'visit_date_time' => now()->toDateTimeString(),
        'status' => 'awaiting_assignment',
        'notes' => 'New visit after cancellation',
    ]);

    $response->assertRedirect('/visits');
    expect(Visit::where('patient_id', $patient->id)->count())->toBe(2);
});
