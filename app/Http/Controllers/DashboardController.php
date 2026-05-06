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
        $phone = request('phone');
        $dob = request('dob');
        $patients = null;

        if ($search || $phone || $dob) {
            $patients = Patient::query()
                ->when($search, function ($query) use ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('surname', 'like', "%{$search}%")
                            ->orWhere('id', 'like', "%{$search}%")
                            ->orWhere('mobile_phone', 'like', "%{$search}%")
                            ->orWhere('id_card_or_passport', 'like', "%{$search}%");
                    });
                })
                ->when($phone, function ($query) use ($phone) {
                    $query->where(function ($q) use ($phone) {
                        $q->where('mobile_phone', 'like', "%{$phone}%")
                            ->orWhere('home_phone', 'like', "%{$phone}%");
                    });
                })
                ->when($dob, function ($query) use ($dob) {
                    $query->where(function ($q) use ($dob) {
                        $q->where('date_of_birth_day', 'like', "%{$dob}%")
                            ->orWhere('date_of_birth_month', 'like', "%{$dob}%")
                            ->orWhere('date_of_birth_year', 'like', "%{$dob}%");
                    });
                })
                ->limit(20)
                ->get();
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
            'phoneSearch' => $phone,
            'dobSearch' => $dob,
            'patients' => $patients,
            'recentPatients' => $recentPatients,
        ]);
    }
}
