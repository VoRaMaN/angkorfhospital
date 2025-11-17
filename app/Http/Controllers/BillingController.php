<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBillingRequest;
use App\Http\Requests\UpdateBillingRequest;
use App\Models\Appointment;
use App\Models\Billing;
use App\Models\MedicalOrder;
use App\Models\Visit;
use App\Services\MedicalOrderBillingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BillingController extends Controller
{
    public function index(): Response
    {
        $this->authorize('viewAny', Billing::class);

        $query = Billing::with(['appointment.patient.user', 'visit.patient.user', 'medicalOrder.patient.user']);

        // Filter by status if provided
        if (request('status')) {
            $query->where('status', request('status'));
        }

        // Filter by search if provided
        if (request('search')) {
            $search = request('search');
            $query->where(function ($q) use ($search) {
                // Search in related patient names
                $q->whereHas('appointment.patient.user', function ($patientQuery) use ($search) {
                    $patientQuery->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                })
                    ->orWhereHas('visit.patient.user', function ($patientQuery) use ($search) {
                        $patientQuery->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('medicalOrder.patient.user', function ($patientQuery) use ($search) {
                        $patientQuery->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
            });
        }

        $billings = $query->paginate(15);

        // Transform billings for the frontend
        $transformedBillings = $billings->getCollection()->map(function ($billing) {
            $patient = null;
            $patientName = 'Unknown Patient';

            if ($billing->appointment && $billing->appointment->patient && $billing->appointment->patient->user) {
                $patient = $billing->appointment->patient;
            } elseif ($billing->visit && $billing->visit->patient && $billing->visit->patient->user) {
                $patient = $billing->visit->patient;
            } elseif ($billing->medicalOrder && $billing->medicalOrder->patient && $billing->medicalOrder->patient->user) {
                $patient = $billing->medicalOrder->patient;
            }

            if ($patient) {
                $patientName = $patient->first_name.' '.$patient->last_name;
            }

            return [
                'id' => $billing->id,
                'patient_id' => $patient?->id,
                'patient_name' => $patientName,
                'appointment_id' => $billing->appointment_id,
                'visit_id' => $billing->visit_id,
                'medical_order_id' => $billing->medical_order_id,
                'total_amount' => $billing->amount,
                'paid_amount' => 0, // TODO: Add payment tracking
                'outstanding_amount' => $billing->amount, // TODO: Calculate based on payments
                'status' => $billing->status,
                'billing_date' => $billing->billing_date,
                'due_date' => $billing->billing_date->addDays(30), // TODO: Add due_date field to model
                'created_at' => $billing->created_at,
            ];
        });

        return Inertia::render('Billings/Index', [
            'billings' => $transformedBillings,
            'filters' => [
                'search' => request('search', ''),
                'status' => request('status', ''),
            ],
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Billing::class);

        // Get appointments without billings
        $appointments = Appointment::with('patient.user')->whereDoesntHave('billings')->get()->map(function ($appointment) {
            return [
                'id' => $appointment->id,
                'label' => 'Appointment for '.$appointment->patient->first_name.' '.$appointment->patient->last_name.' on '.$appointment->appointment_date_time->format('Y-m-d'),
            ];
        });

        // Get visits without billings
        $visits = Visit::with('patient.user')->whereDoesntHave('billings')->get()->map(function ($visit) {
            return [
                'id' => $visit->id,
                'label' => 'Visit for '.$visit->patient->first_name.' '.$visit->patient->last_name.' on '.$visit->visit_date_time->format('Y-m-d H:i'),
            ];
        });

        // Get medical orders without billings
        $medicalOrders = MedicalOrder::with('patient.user')->whereDoesntHave('billings')->get()->map(function ($order) {
            return [
                'id' => $order->id,
                'label' => 'Order for '.$order->patient->first_name.' '.$order->patient->last_name.' - '.$order->order_details,
            ];
        });

        return Inertia::render('Billings/Create', [
            'appointments' => $appointments,
            'visits' => $visits,
            'medicalOrders' => $medicalOrders,
        ]);
    }

    public function store(StoreBillingRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // If medical order is selected, calculate amount from the order
        if (! empty($data['medical_order_id'])) {
            $medicalOrder = MedicalOrder::find($data['medical_order_id']);
            if ($medicalOrder) {
                $billingService = app(MedicalOrderBillingService::class);
                $calculatedAmount = $billingService->calculateOrderTotal($medicalOrder);
                $data['amount'] = $calculatedAmount;

                // Auto-set visit_id if not provided
                if (empty($data['visit_id']) && $medicalOrder->visit_id) {
                    $data['visit_id'] = $medicalOrder->visit_id;
                }

                // Auto-set appointment_id if not provided
                if (empty($data['appointment_id']) && $medicalOrder->visit?->appointment_id) {
                    $data['appointment_id'] = $medicalOrder->visit->appointment_id;
                }
            }
        }

        $billing = Billing::create($data);

        return redirect()->route('billings.index')->with('success', 'Billing record created successfully.');
    }

    public function show(Billing $billing): Response
    {
        $this->authorize('view', $billing);
        $billing->load([
            'appointment.patient.user',
            'appointment.staff.user',
            'visit.patient.user',
            'visit.staff.user',
            'medicalOrder.patient.user',
            'medicalOrder.staff',
            'medicalOrder.orderItems.inventory',
        ]);

        $costBreakdown = null;
        if ($billing->medicalOrder) {
            $billingService = app(MedicalOrderBillingService::class);
            $costBreakdown = $billingService->getOrderCostBreakdown($billing->medicalOrder);
        }

        // Get patient name from related records
        $patientName = 'Unknown Patient';
        if ($billing->appointment && $billing->appointment->patient && $billing->appointment->patient->user) {
            $patientName = $billing->appointment->patient->first_name.' '.$billing->appointment->patient->last_name;
        } elseif ($billing->visit && $billing->visit->patient && $billing->visit->patient->user) {
            $patientName = $billing->visit->patient->first_name.' '.$billing->visit->patient->last_name;
        } elseif ($billing->medicalOrder && $billing->medicalOrder->patient && $billing->medicalOrder->patient->user) {
            $patientName = $billing->medicalOrder->patient->first_name.' '.$billing->medicalOrder->patient->last_name;
        }

        $transformedBilling = [
            'id' => $billing->id,
            'patient_name' => $patientName,
            'appointment_id' => $billing->appointment_id,
            'visit_id' => $billing->visit_id,
            'medical_order_id' => $billing->medical_order_id,
            'amount' => $billing->amount,
            'status' => $billing->status,
            'billing_date' => $billing->billing_date ? \Carbon\Carbon::parse($billing->billing_date)->toDateString() : null,
            'notes' => $billing->notes,
            'created_at' => $billing->created_at->toISOString(),
            'updated_at' => $billing->updated_at->toISOString(),
        ];

        return Inertia::render('Billings/Show', [
            'billing' => $transformedBilling,
            'costBreakdown' => $costBreakdown,
        ]);
    }

    public function edit(Billing $billing): Response
    {
        $this->authorize('update', $billing);

        // Get appointments without billings or current
        $appointments = Appointment::with('patient.user')->where(function ($query) use ($billing) {
            $query->whereDoesntHave('billings')->orWhere('id', $billing->appointment_id);
        })->get()->map(function ($appointment) {
            return [
                'id' => $appointment->id,
                'label' => 'Appointment for '.$appointment->patient->first_name.' '.$appointment->patient->last_name.' on '.$appointment->appointment_date_time->format('Y-m-d'),
            ];
        });

        // Get visits without billings or current
        $visits = Visit::with('patient.user')->where(function ($query) use ($billing) {
            $query->whereDoesntHave('billings')->orWhere('id', $billing->visit_id);
        })->get()->map(function ($visit) {
            return [
                'id' => $visit->id,
                'label' => 'Visit for '.$visit->patient->first_name.' '.$visit->patient->last_name.' on '.$visit->visit_date_time->format('Y-m-d H:i'),
            ];
        });

        // Get medical orders without billings or current
        $medicalOrders = MedicalOrder::with('patient.user')->where(function ($query) use ($billing) {
            $query->whereDoesntHave('billings')->orWhere('id', $billing->medical_order_id);
        })->get()->map(function ($order) {
            return [
                'id' => $order->id,
                'label' => 'Order for '.$order->patient->first_name.' '.$order->patient->last_name.' - '.$order->order_details,
            ];
        });

        return Inertia::render('Billings/Edit', [
            'billing' => $billing,
            'appointments' => $appointments,
            'visits' => $visits,
            'medicalOrders' => $medicalOrders,
        ]);
    }

    public function update(UpdateBillingRequest $request, Billing $billing): RedirectResponse
    {
        $data = $request->validated();

        // If medical order is selected, calculate amount from the order
        if (! empty($data['medical_order_id'])) {
            $medicalOrder = MedicalOrder::find($data['medical_order_id']);
            if ($medicalOrder) {
                $billingService = app(MedicalOrderBillingService::class);
                $calculatedAmount = $billingService->calculateOrderTotal($medicalOrder);
                $data['amount'] = $calculatedAmount;

                // Auto-set visit_id if not provided
                if (empty($data['visit_id']) && $medicalOrder->visit_id) {
                    $data['visit_id'] = $medicalOrder->visit_id;
                }

                // Auto-set appointment_id if not provided
                if (empty($data['appointment_id']) && $medicalOrder->visit?->appointment_id) {
                    $data['appointment_id'] = $medicalOrder->visit->appointment_id;
                }
            }
        }

        $billing->update($data);

        return redirect()->route('billings.index')->with('success', 'Billing record updated successfully.');
    }

    public function updateStatus(Billing $billing, Request $request): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:pending,paid,overdue,partial,written_off,cancelled',
        ]);

        $this->authorize('updateStatus', $billing);

        $billing->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Billing status updated successfully.');
    }

    public function destroy(Billing $billing): RedirectResponse
    {
        $this->authorize('delete', $billing);
        $billing->delete();

        return redirect()->route('billings.index')->with('success', 'Billing record deleted successfully.');
    }
}
