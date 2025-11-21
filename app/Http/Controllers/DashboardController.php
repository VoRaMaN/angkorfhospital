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

        $search = request('search');
        $patients = null;

        if ($search) {
            $patients = Patient::where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('surname', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%")
                    ->orWhere('mobile_phone', 'like', "%{$search}%")
                    ->orWhere('id_card_or_passport', 'like', "%{$search}%");
            })->limit(20)->get();
        }

        // Recent patients
        $recentPatients = Patient::orderBy('created_at', 'desc')->limit(10)->get();

        return Inertia::render('Dashboard', [
            'stats' => [
                'total_patients' => $totalPatients,
                'total_staff' => $totalStaff,
                'todays_appointments' => $todaysAppointments,
                'pending_medical_orders' => $pendingMedicalOrders,
                'low_stock_items' => $lowStockItems,
            ],
            'search' => $search,
            'patients' => $patients,
            'recentPatients' => $recentPatients,
        ]);
    }
}
