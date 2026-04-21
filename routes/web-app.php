<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\BillingReportController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\LabPanelController;
use App\Http\Controllers\MedicalOrderController;
use App\Http\Controllers\MedicalRecordController;
use App\Http\Controllers\MedicalServiceController;
use App\Http\Controllers\MedicineGroupController;
use App\Http\Controllers\MedicineReportController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PatientFileController;
use App\Http\Controllers\SpecialItemController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\StaffFileController;
use App\Http\Controllers\VisitController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    // Appointments
    Route::resource('appointments', AppointmentController::class);
    Route::get('appointments-calendar', [AppointmentController::class, 'calendar'])->name('appointments.calendar');
    Route::patch('appointments/{appointment}/status', [AppointmentController::class, 'updateStatus'])->name('appointments.update-status');
    Route::get('appointments/{appointment}/report', [AppointmentController::class, 'generateReport'])->name('appointments.report');
    Route::get('appointments/{appointment}/letter', [AppointmentController::class, 'generateLetter'])->name('appointments.letter');
    Route::get('appointments-export', [AppointmentController::class, 'export'])->name('appointments.export');

    // Billings
    Route::resource('billings', BillingController::class);
    Route::patch('billings/{billing}/status', [BillingController::class, 'updateStatus'])->name('billings.update-status');
    Route::patch('billings/{billing}/complete-payment', [BillingController::class, 'completePayment'])->name('billings.complete-payment');
    Route::patch('billings/{billing}/send-back-to-nurse', [BillingController::class, 'sendBackToNurse'])->name('billings.send-back-to-nurse');
    Route::patch('billings/{billing}/recalculate', [BillingController::class, 'recalculate'])->name('billings.recalculate');
    Route::get('billings/{billing}/report', [BillingController::class, 'generateReport'])->name('billings.report');
    Route::get('billings/{billing}/letter', [BillingController::class, 'generateLetter'])->name('billings.letter');

    // Departments
    Route::resource('departments', DepartmentController::class);

    // Doctors
    Route::get('doctors/my-appointments', [DoctorController::class, 'myAppointments'])->name('doctors.my-appointments');
    Route::get('doctors/my-patients', [DoctorController::class, 'myPatients'])->name('doctors.my-patients');

    // Inventory
    Route::resource('inventory', InventoryController::class);
    Route::get('rx-medicine', [InventoryController::class, 'rxMedicine'])->name('inventory.rx-medicine');
    Route::get('lab-inventory', [InventoryController::class, 'labInventory'])->name('inventory.lab-inventory');
    Route::get('plastic-ware', [InventoryController::class, 'plasticWare'])->name('inventory.plastic-ware');
    Route::get('culture-medium', [InventoryController::class, 'cultureMedium'])->name('inventory.culture-medium');

    // Lab Panels
    Route::resource('lab-panels', LabPanelController::class);

    // Special Items (Medicine Groups)
    Route::resource('medicine-groups', MedicineGroupController::class);

    // Special Items
    Route::resource('special-items', SpecialItemController::class);

    // Medical Orders
    Route::resource('medical-orders', MedicalOrderController::class);
    Route::get('medical-orders/{medical_order}/process', [MedicalOrderController::class, 'processPage'])->name('medical-orders.process-page');
    Route::get('medical-orders/{medical_order}/processing', [MedicalOrderController::class, 'processingPage'])->name('medical-orders.processing-page');
    Route::patch('medical-orders/{medical_order}/process-with-update', [MedicalOrderController::class, 'processWithUpdate'])->name('medical-orders.process-with-update');
    Route::get('medical-orders/{medical_order}/complete', [MedicalOrderController::class, 'completePage'])->name('medical-orders.complete-page');
    Route::patch('medical-orders/{medical_order}/complete', [MedicalOrderController::class, 'complete'])->name('medical-orders.complete');
    Route::patch('medical-orders/{medical_order}/items/{item}/complete', [MedicalOrderController::class, 'completeItem'])->name('medical-orders.complete-item');
    Route::patch('medical-orders/{medical_order}/process-and-bill', [MedicalOrderController::class, 'processAndBill'])->name('medical-orders.process-and-bill');
    Route::patch('medical-orders/{medical_order}/confirm-processed', [MedicalOrderController::class, 'confirmProcessed'])->name('medical-orders.confirm-processed');
    Route::get('medical-orders/{medical_order}/cost-breakdown', [MedicalOrderController::class, 'getCostBreakdown'])->name('medical-orders.cost-breakdown');
    Route::patch('medical-orders/{medical_order}/cancel-processed', [MedicalOrderController::class, 'cancelProcessed'])->name('medical-orders.cancel-processed');
    Route::patch('medical-orders/{medical_order}/send-back', [MedicalOrderController::class, 'sendBack'])->name('medical-orders.send-back');
    Route::get('medical-orders/{medical_order}/report', [MedicalOrderController::class, 'generateReport'])->name('medical-orders.report');
    Route::get('medical-orders/{medical_order}/medical-record-report', [MedicalOrderController::class, 'generateMedicalRecordReport'])->name('medical-orders.medical-record-report');

    // Medical Records
    Route::resource('medical-records', MedicalRecordController::class);
    Route::get('medical-records/{medical_record}/report', [MedicalRecordController::class, 'generateReport'])->name('medical-records.report');

    // Medical Services
    Route::resource('medical-services', MedicalServiceController::class);

    // Medicine Report
    Route::get('medicine-report', [MedicineReportController::class, 'index'])->name('medicine-report.index');
    Route::get('medicine-report/export', [MedicineReportController::class, 'export'])->name('medicine-report.export');
    Route::patch('medicine-report/finish/{medical_order}', [MedicineReportController::class, 'finish'])->name('medicine-report.finish');

    // Billing Report
    Route::get('billing-report', [BillingReportController::class, 'index'])->name('billing-report.index');
    Route::get('billing-report/export', [BillingReportController::class, 'export'])->name('billing-report.export');

    // Patients
    Route::get('patients', [PatientController::class, 'index'])->name('patients.index');
    Route::get('patients/create', [PatientController::class, 'create'])->name('patients.create');
    Route::post('patients', [PatientController::class, 'store'])->name('patients.store');
    Route::get('patients/show', [PatientController::class, 'show'])->name('patients.show');
    Route::get('patients/edit', [PatientController::class, 'edit'])->name('patients.edit');
    Route::put('patients/update', [PatientController::class, 'update'])->name('patients.update');
    Route::delete('patients/destroy', [PatientController::class, 'destroy'])->name('patients.destroy');
    Route::get('patients/report', [PatientController::class, 'generateReport'])->name('patients.report');
    // Download patient report as PDF (query param: patient)
    Route::get('patients/report/download', [PatientController::class, 'downloadReport'])->name('patients.report.download');
    Route::get('patients/sticker', [PatientController::class, 'generateSticker'])->name('patients.sticker');
    // Printable patient label (single small page suitable for label printers)
    Route::get('patients/label', [PatientController::class, 'generateLabel'])->name('patients.label');

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
    Route::get('visits-export', [VisitController::class, 'export'])->name('visits.export');
    Route::patch('visits/{visit}/assign-process', [VisitController::class, 'assignAndProcess'])->name('visits.assign-process');
    Route::patch('visits/{visit}/assign-doctor', [VisitController::class, 'assignDoctor'])->name('visits.assign-doctor');
    Route::patch('visits/{visit}/notify-staff', [VisitController::class, 'notifyStaff'])->name('visits.notify-staff');
    Route::get('/my-visits', [VisitController::class, 'myVisits'])->name('doctors.my-visits');
    Route::get('/my-to-be-process-visits', [VisitController::class, 'myToBeProcessVisits'])->name('doctors.my-to-be-process-visits');

    // Activity Log
    Route::get('activity-log', [ActivityLogController::class, 'index'])->name('activity-log.index')->middleware('can:view_activity_logs');
    Route::get('activity-log/export', [ActivityLogController::class, 'export'])->name('activity-log.export')->middleware('can:view_activity_logs');

});

require __DIR__.'/settings.php';
