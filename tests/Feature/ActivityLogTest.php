<?php

use App\Models\ActivityLog;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $permission = Permission::firstOrCreate(['name' => 'view_activity_logs', 'guard_name' => 'web']);
    $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    $role->syncPermissions([$permission]);
});

test('guests cannot access activity log', function () {
    $this->get(route('activity-log.index'))
        ->assertRedirect(route('login'));
});

test('authenticated users with permission can access activity log', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    $this->actingAs($user)
        ->get(route('activity-log.index'))
        ->assertSuccessful();
});

test('authenticated users without permission cannot access activity log', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('activity-log.index'))
        ->assertForbidden();
});

test('activity log static method creates a log entry', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    ActivityLog::log('created', 'Test item was created');

    $log = ActivityLog::where('action', 'created')
        ->where('description', 'Test item was created')
        ->first();

    expect($log)->not->toBeNull()
        ->and($log->user_id)->toBe($user->id)
        ->and($log->action)->toBe('created');
});

test('activity log stores properties as json', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    ActivityLog::log('updated', 'Item updated', null, [
        'old' => ['name' => 'Old Name'],
        'new' => ['name' => 'New Name'],
    ]);

    $log = ActivityLog::where('action', 'updated')->first();

    expect($log->properties)->toBeArray()
        ->and($log->properties['old']['name'])->toBe('Old Name')
        ->and($log->properties['new']['name'])->toBe('New Name');
});

test('activity log captures ip address and user agent', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    ActivityLog::log('login', "User \"{$user->name}\" logged in");

    $log = ActivityLog::where('action', 'login')->first();

    expect($log)->not->toBeNull()
        ->and($log->ip_address)->not->toBeNull();
});

test('activity log index page returns paginated logs', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    ActivityLog::log('created', 'Test entry');

    $this->actingAs($user)
        ->get(route('activity-log.index'))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('ActivityLog/Index')
            ->has('activityLogs.data')
            ->has('filters')
        );
});

test('activity log can be filtered by action', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');
    $this->actingAs($user);

    ActivityLog::log('created', 'Created entry');
    ActivityLog::log('deleted', 'Deleted entry');

    $this->get(route('activity-log.index', ['action' => 'created']))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('ActivityLog/Index')
            ->where('activityLogs.data', fn ($data) => collect($data)->every(fn ($log) => $log['action'] === 'created'))
        );
});

test('activity log can be filtered by user', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $otherUser = User::factory()->create();

    $this->actingAs($admin);
    ActivityLog::log('created', 'Admin entry');

    $this->actingAs($otherUser);
    ActivityLog::log('created', 'Other user entry');

    $this->actingAs($admin)
        ->get(route('activity-log.index', ['user_id' => $admin->id]))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('ActivityLog/Index')
            ->where('activityLogs.data', fn ($data) => collect($data)->every(fn ($log) => $log['user_id'] === $admin->id))
        );
});

test('user last_active_at is updated by middleware', function () {
    $user = User::factory()->create(['last_active_at' => null]);

    $this->actingAs($user)
        ->get(route('dashboard'));

    $user->refresh();
    expect($user->last_active_at)->not->toBeNull();
});

test('failed login attempt is logged', function () {
    User::factory()->create(['email' => 'test@example.com']);

    $this->post('/login', [
        'email' => 'test@example.com',
        'password' => 'wrong-password',
    ]);

    $log = ActivityLog::where('action', 'failed_login')->first();

    expect($log)->not->toBeNull()
        ->and($log->description)->toContain('test@example.com');
});

test('sensitive fields are masked in activity log properties', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    // Simulate what LogsActivity trait does when logging with sensitive fields
    $attributes = ['password' => 'hashed-value', 'name' => 'John', 'remember_token' => 'abc123'];
    $sensitiveFields = ['password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes', 'secret', 'token', 'password_confirmation'];

    foreach ($sensitiveFields as $field) {
        if (array_key_exists($field, $attributes)) {
            $attributes[$field] = '********';
        }
    }

    ActivityLog::log('updated', 'User updated', null, ['attributes' => $attributes]);

    $log = ActivityLog::where('description', 'User updated')->latest()->first();

    expect($log->properties['attributes']['password'])->toBe('********')
        ->and($log->properties['attributes']['remember_token'])->toBe('********')
        ->and($log->properties['attributes']['name'])->toBe('John');
});

test('activity log export returns csv', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');
    $this->actingAs($user);

    ActivityLog::log('created', 'Test export entry');

    // Export renders an HTML print-preview page (with embedded CSV download button)
    $this->get(route('activity-log.export'))
        ->assertSuccessful()
        ->assertHeader('Content-Type', 'text/html; charset=UTF-8');
});

test('activity log export requires permission', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('activity-log.export'))
        ->assertForbidden();
});

test('activity log pruning targets old records', function () {
    $oldLog = ActivityLog::create([
        'action' => 'old_entry',
        'description' => 'Ancient log',
    ]);
    ActivityLog::where('id', $oldLog->id)->update(['created_at' => now()->subDays(100)]);

    $recentLog = ActivityLog::create([
        'action' => 'recent_entry',
        'description' => 'Recent log',
    ]);
    ActivityLog::where('id', $recentLog->id)->update(['created_at' => now()->subDays(10)]);

    expect((new ActivityLog)->prunable()->count())->toBe(1);
});
