<?php

use App\Enums\VisitStatusEnum;
use App\Models\Patient;
use App\Models\Staff;
use App\Models\StaffRole;
use App\Models\User;
use App\Models\Visit;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

beforeEach(function () {
    $permissions = ['view_visits'];
    foreach ($permissions as $perm) {
        Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
    }
    $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    $role->syncPermissions($permissions);
});

function createVisibilityAdmin(): User
{
    $domainRole = StaffRole::firstOrCreate(['name' => 'admin'], ['description' => 'System Administrator']);
    $spatieRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    $user = User::factory()->create();
    $user->assignRole($spatieRole);
    Staff::factory()->create(['user_id' => $user->id, 'role_id' => $domainRole->id]);
    app(PermissionRegistrar::class)->forgetCachedPermissions();

    return $user->fresh();
}

function createVisibilityVisit(string $status, int $daysOld = 0): Visit
{
    $patient = Patient::factory()->create();
    $staff = Staff::factory()->create();

    return Visit::factory()->create([
        'patient_id' => $patient->id,
        'staff_id' => $staff->id,
        'status' => $status,
        'visit_date_time' => now()->subDays($daysOld),
    ]);
}

test('an old awaiting_accountant visit survives the auto-archive sweep and shows on Visits index by default', function () {
    $user = createVisibilityAdmin();
    $visit = createVisibilityVisit(VisitStatusEnum::AWAITING_ACCOUNTANT->value, daysOld: 5);

    $this->actingAs($user)
        ->get(route('visits.index'))
        ->assertInertia(fn ($page) => $page
            ->where('visits', fn ($visits) => collect($visits)->contains(fn ($v) => $v['id'] === $visit->id))
        );

    $visit->refresh();
    expect($visit->status)->toBe(VisitStatusEnum::AWAITING_ACCOUNTANT);
});

test('an old sent_back visit survives the auto-archive sweep and shows on My Visits to Process by default', function () {
    $user = createVisibilityAdmin();
    $visit = createVisibilityVisit(VisitStatusEnum::SENT_BACK->value, daysOld: 3);

    $this->actingAs($user)
        ->get(route('doctors.my-to-be-process-visits'))
        ->assertInertia(fn ($page) => $page
            ->where('visits', fn ($visits) => collect($visits)->contains(fn ($v) => $v['id'] === $visit->id))
        );

    $visit->refresh();
    expect($visit->status)->toBe(VisitStatusEnum::SENT_BACK);
});

test('a genuinely stale pending visit still gets auto-completed by the sweep', function () {
    $user = createVisibilityAdmin();
    $visit = createVisibilityVisit(VisitStatusEnum::PENDING->value, daysOld: 5);

    $this->actingAs($user)->get(route('visits.index'));

    $visit->refresh();
    expect($visit->status)->toBe(VisitStatusEnum::COMPLETED);
});

test('a genuinely stale assigned visit is excluded from My Visits to Process after the sweep', function () {
    $user = createVisibilityAdmin();
    $visit = createVisibilityVisit(VisitStatusEnum::ASSIGNED->value, daysOld: 5);

    $this->actingAs($user)
        ->get(route('doctors.my-to-be-process-visits'))
        ->assertInertia(fn ($page) => $page
            ->where('visits', fn ($visits) => ! collect($visits)->contains(fn ($v) => $v['id'] === $visit->id))
        );

    $visit->refresh();
    expect($visit->status)->toBe(VisitStatusEnum::COMPLETED);
});
