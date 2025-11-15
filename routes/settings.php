<?php

use App\Http\Controllers\RoleController;
use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\Settings\TwoFactorAuthenticationController;
use App\Http\Controllers\Settings\UserManagementController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware('auth')->group(function () {
    Route::redirect('settings', '/settings/profile');

    Route::get('settings/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('settings/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('settings/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('settings/password', [PasswordController::class, 'edit'])->name('user-password.edit');

    Route::put('settings/password', [PasswordController::class, 'update'])
        ->middleware('throttle:6,1')
        ->name('user-password.update');

    Route::get('settings/appearance', function () {
        return Inertia::render('settings/Appearance');
    })->name('appearance.edit');

    Route::get('settings/two-factor', [TwoFactorAuthenticationController::class, 'show'])
        ->name('two-factor.show');

    // User Management
    Route::resource('settings/user-management', UserManagementController::class, [
        'names' => [
            'index' => 'settings.user-management.index',
            'create' => 'settings.user-management.create',
            'store' => 'settings.user-management.store',
            'show' => 'settings.user-management.show',
            'edit' => 'settings.user-management.edit',
            'update' => 'settings.user-management.update',
            'destroy' => 'settings.user-management.destroy',
        ],
    ]);

    // Role Management
    Route::resource('settings/roles', RoleController::class, [
        'names' => [
            'index' => 'settings.roles.index',
            'create' => 'settings.roles.create',
            'store' => 'settings.roles.store',
            'show' => 'settings.roles.show',
            'edit' => 'settings.roles.edit',
            'update' => 'settings.roles.update',
            'destroy' => 'settings.roles.destroy',
        ],
    ]);
});
