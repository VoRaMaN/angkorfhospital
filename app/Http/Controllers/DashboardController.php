<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Inventory;
use App\Models\MedicalOrder;
use App\Models\Patient;
use App\Models\Staff;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index(): Response
    {
        // Key metrics
        $totalPatients = Patient::count();
        $totalStaff = Staff::count();
        $todaysAppointments = Appointment::whereDate('appointment_date_time', today())->count();
        $pendingMedicalOrders = MedicalOrder::where('status', 'pending')->count();
        $lowStockItems = Inventory::whereColumn('quantity', '<=', 'minimum_stock')->count();

        return Inertia::render('Dashboard', [
            'stats' => [
                'total_patients' => $totalPatients,
                'total_staff' => $totalStaff,
                'todays_appointments' => $todaysAppointments,
                'pending_medical_orders' => $pendingMedicalOrders,
                'low_stock_items' => $lowStockItems,
            ],
        ]);
    }
}
