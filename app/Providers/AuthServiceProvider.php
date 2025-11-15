<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        \App\Models\User::class => \App\Policies\UserPolicy::class,
        \App\Models\StaffRole::class => \App\Policies\RolePolicy::class,
        \App\Models\Department::class => \App\Policies\DepartmentPolicy::class,
        \App\Models\Staff::class => \App\Policies\StaffPolicy::class,
        \App\Models\Doctor::class => \App\Policies\DoctorPolicy::class,
        \App\Models\Patient::class => \App\Policies\PatientPolicy::class,
        \App\Models\Appointment::class => \App\Policies\AppointmentPolicy::class,
        \App\Models\MedicalRecord::class => \App\Policies\MedicalRecordPolicy::class,
        \App\Models\Medication::class => \App\Policies\MedicationPolicy::class,
        \App\Models\Billing::class => \App\Policies\BillingPolicy::class,
        \App\Models\MedicalOrder::class => \App\Policies\MedicalOrderPolicy::class,
        \App\Models\Inventory::class => \App\Policies\InventoryPolicy::class,
        \App\Models\LabPackage::class => \App\Policies\LabPackagePolicy::class,
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
