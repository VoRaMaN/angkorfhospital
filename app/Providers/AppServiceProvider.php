<?php

namespace App\Providers;

use App\Models\Billing;
use App\Models\Staff;
use App\Observers\BillingObserver;
use App\Observers\StaffObserver;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Staff::observe(StaffObserver::class);
        Billing::observe(BillingObserver::class);

        Event::listen(Login::class, \App\Listeners\LogSuccessfulLogin::class);
        Event::listen(Logout::class, \App\Listeners\LogSuccessfulLogout::class);

        // Share minimal auth info with the frontend for permission checks
        Inertia::share('auth', function () {
            $user = auth()->user();

            if (! $user) {
                return null;
            }

            // Provide minimal fields + role names + permission checks using Laravel's can()
            return [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'roles' => $user->getRoleNames()->toArray(),
                    'permissions' => [
                        'view_users' => $user->can('view_users'),
                        'create_users' => $user->can('create_users'),
                        'edit_users' => $user->can('edit_users'),
                        'delete_users' => $user->can('delete_users'),
                        'view_staff' => $user->can('view_staff'),
                        'create_staff' => $user->can('create_staff'),
                        'edit_staff' => $user->can('edit_staff'),
                        'delete_staff' => $user->can('delete_staff'),
                        'view_doctors' => $user->can('view_doctors'),
                        'create_doctors' => $user->can('create_doctors'),
                        'edit_doctors' => $user->can('edit_doctors'),
                        'delete_doctors' => $user->can('delete_doctors'),
                        'view_patients' => $user->can('view_patients'),
                        'create_patients' => $user->can('create_patients'),
                        'edit_patients' => $user->can('edit_patients'),
                        'delete_patients' => $user->can('delete_patients'),
                        'view_appointments' => $user->can('view_appointments'),
                        'create_appointments' => $user->can('create_appointments'),
                        'edit_appointments' => $user->can('edit_appointments'),
                        'delete_appointments' => $user->can('delete_appointments'),
                        'view_medical_records' => $user->can('view_medical_records'),
                        'create_medical_records' => $user->can('create_medical_records'),
                        'edit_medical_records' => $user->can('edit_medical_records'),
                        'delete_medical_records' => $user->can('delete_medical_records'),
                        'view_medical_orders' => $user->can('view_medical_orders'),
                        'create_medical_orders' => $user->can('create_medical_orders'),
                        'edit_medical_orders' => $user->can('edit_medical_orders'),
                        'delete_medical_orders' => $user->can('delete_medical_orders'),
                        'view_medications' => $user->can('view_medications'),
                        'create_medications' => $user->can('create_medications'),
                        'edit_medications' => $user->can('edit_medications'),
                        'delete_medications' => $user->can('delete_medications'),
                        'view_visits' => $user->can('view_visits'),
                        'create_visits' => $user->can('create_visits'),
                        'edit_visits' => $user->can('edit_visits'),
                        'delete_visits' => $user->can('delete_visits'),
                        'view_billing' => $user->can('view_billing'),
                        'create_billing' => $user->can('create_billing'),
                        'edit_billing' => $user->can('edit_billing'),
                        'delete_billing' => $user->can('delete_billing'),
                        'view_inventory' => $user->can('view_inventory'),
                        'create_inventory' => $user->can('create_inventory'),
                        'edit_inventory' => $user->can('edit_inventory'),
                        'delete_inventory' => $user->can('delete_inventory'),
                        'view_lab_packages' => $user->can('view_lab_packages'),
                        'create_lab_packages' => $user->can('create_lab_packages'),
                        'edit_lab_packages' => $user->can('edit_lab_packages'),
                        'delete_lab_packages' => $user->can('delete_lab_packages'),
                        'view_departments' => $user->can('view_departments'),
                        'create_departments' => $user->can('create_departments'),
                        'edit_departments' => $user->can('edit_departments'),
                        'delete_departments' => $user->can('delete_departments'),
                        'view_roles' => $user->can('view_roles'),
                        'create_roles' => $user->can('create_roles'),
                        'edit_roles' => $user->can('edit_roles'),
                        'delete_roles' => $user->can('delete_roles'),
                        'view_settings' => $user->can('view_settings'),
                        'edit_settings' => $user->can('edit_settings'),
                    ],
                ],
            ];
        });
    }
}
