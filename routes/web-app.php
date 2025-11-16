<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\LabPanelController;
use App\Http\Controllers\MedicalOrderController;
use App\Http\Controllers\MedicalRecordController;
use App\Http\Controllers\MedicalServiceController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PatientFileController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\StaffFileController;
use App\Http\Controllers\VisitController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('dashboard');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // Appointments
    Route::resource('appointments', AppointmentController::class);
    Route::get('appointments-calendar', [AppointmentController::class, 'calendar'])->name('appointments.calendar');
    Route::patch('appointments/{appointment}/status', [AppointmentController::class, 'updateStatus'])->name('appointments.update-status');

    // Billings
    Route::resource('billings', BillingController::class);

    // Departments
    Route::resource('departments', DepartmentController::class);

    // Doctors
    Route::get('doctors/my-appointments', [DoctorController::class, 'myAppointments'])->name('doctors.my-appointments');
    Route::get('doctors/my-patients', [DoctorController::class, 'myPatients'])->name('doctors.my-patients');
    Route::get('doctors/my-visits', [VisitController::class, 'myVisits'])->name('doctors.my-visits');
    Route::get('doctors/my-to-be-process-visits', [VisitController::class, 'myToBeProcessVisits'])->name('doctors.my-to-be-process-visits');

    // Inventory
    Route::resource('inventory', InventoryController::class);
    Route::get('rx-medicine', [InventoryController::class, 'rxMedicine'])->name('inventory.rx-medicine');
    Route::get('lab-inventory', [InventoryController::class, 'labInventory'])->name('inventory.lab-inventory');

    // Lab Panels
    Route::resource('lab-panels', LabPanelController::class);

    // Medical Orders
    Route::resource('medical-orders', MedicalOrderController::class);
    Route::get('medical-orders/{medical_order}/process', [MedicalOrderController::class, 'processPage'])->name('medical-orders.process-page');
    Route::get('medical-orders/{medical_order}/already-processed', [MedicalOrderController::class, 'alreadyProcessedPage'])->name('medical-orders.already-processed');
    Route::patch('medical-orders/{medical_order}/process-with-update', [MedicalOrderController::class, 'processWithUpdate'])->name('medical-orders.process-with-update');
    Route::get('medical-orders/{medical_order}/complete', [MedicalOrderController::class, 'completePage'])->name('medical-orders.complete-page');
    Route::patch('medical-orders/{medical_order}/complete', [MedicalOrderController::class, 'complete'])->name('medical-orders.complete');
    Route::patch('medical-orders/{medical_order}/items/{item}/complete', [MedicalOrderController::class, 'completeItem'])->name('medical-orders.complete-item');
    Route::patch('medical-orders/{medical_order}/process-and-bill', [MedicalOrderController::class, 'processAndBill'])->name('medical-orders.process-and-bill');
    Route::get('medical-orders/{medical_order}/cost-breakdown', [MedicalOrderController::class, 'getCostBreakdown'])->name('medical-orders.cost-breakdown');
    Route::patch('medical-orders/{medical_order}/cancel-processed', [MedicalOrderController::class, 'cancelProcessed'])->name('medical-orders.cancel-processed');

    // Medical Records
    Route::resource('medical-records', MedicalRecordController::class);

    // Medical Services
    Route::resource('medical-services', MedicalServiceController::class);

    // Patients
    Route::resource('patients', PatientController::class);

    // Staff
    Route::resource('staff', StaffController::class);

    // Files
    Route::resource('files', FileController::class);
    Route::resource('patient-files', PatientFileController::class);
    Route::get('patient-files/{patient_file}/download', [PatientFileController::class, 'download'])->name('patient-files.download');
    Route::resource('staff-files', StaffFileController::class);
    Route::get('staff-files/{staff_file}/download', [StaffFileController::class, 'download'])->name('staff-files.download');

    // Visits
    Route::resource('visits', VisitController::class);
    Route::patch('visits/{visit}/assign-process', [VisitController::class, 'assignAndProcess'])->name('visits.assign-process');
    Route::patch('visits/{visit}/notify-staff', [VisitController::class, 'notifyStaff'])->name('visits.notify-staff');
});

require __DIR__.'/settings.php';
