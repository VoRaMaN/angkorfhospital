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

        $query = Billing::with(['patient.user', 'appointment', 'visit', 'medicalOrder']);

        // Filter by status if provided, otherwise exclude paid billings by default
        if (request('status')) {
            $query->where('status', request('status'));
        } else {
            // Default: show only active billings (not paid)
            $query->where('status', '!=', 'paid');
        }

        // Filter by search if provided (search in patient id, patient name and related IDs)
        if (request('search')) {
            $search = request('search');
            $query->where(function ($q) use ($search) {
                // search by patient id (string like 25/0000000001)
                $q->where('patient_id', 'like', "%{$search}%")
                    // or by patient full name on the related user
                    ->orWhereHas('patient.user', function ($patientQuery) use ($search) {
                        $patientQuery->where('name', 'like', "%{$search}%");
                    })
                    // or by related numeric IDs
                    ->orWhere('appointment_id', 'like', "%{$search}%")
                    ->orWhere('visit_id', 'like', "%{$search}%")
                    ->orWhere('medical_order_id', 'like', "%{$search}%");
            });
        }

        // Filter by billing date range if provided
        if (request('start_date')) {
            $query->whereDate('billing_date', '>=', request('start_date'));
        }
        if (request('end_date')) {
            $query->whereDate('billing_date', '<=', request('end_date'));
        }

        $billings = $query->paginate(15)->appends(request()->only(['search', 'status', 'start_date', 'end_date']));

        // Transform billings for the frontend
        $transformedBillings = $billings->getCollection()->map(function ($billing) {
            $patient = $billing->patient;
            $patientName = 'Unknown Patient';

            if ($patient && $patient->user) {
                $patientName = $patient->user->name;
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
                'start_date' => request('start_date', ''),
                'end_date' => request('end_date', ''),
            ],
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Billing::class);

        // Get appointments without billings
        $appointments = Appointment::with('patient.user')->whereDoesntHave('billings')->get()->map(function ($appointment) {
            $patientName = $appointment->patient->user ? $appointment->patient->user->name : 'Unknown';

            return [
                'id' => $appointment->id,
                'label' => 'Appointment for '.$patientName.' on '.$appointment->appointment_date_time->format('Y-m-d'),
            ];
        });

        // Get visits without billings
        $visits = Visit::with('patient.user')->whereDoesntHave('billings')->get()->map(function ($visit) {
            $patientName = $visit->patient->user ? $visit->patient->user->name : 'Unknown';

            return [
                'id' => $visit->id,
                'label' => 'Visit for '.$patientName.' on '.$visit->visit_date_time->format('Y-m-d H:i'),
            ];
        });

        // Get medical orders without billings
        $medicalOrders = MedicalOrder::with('visit.patient.user')->whereDoesntHave('billings')->get()->map(function ($order) {
            $patientName = $order->visit?->patient?->user ? $order->visit->patient->user->name : 'Unknown';

            return [
                'id' => $order->id,
                'label' => 'Order for '.$patientName.' - '.$order->order_details,
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
            'patient.user',
            'appointment.staff.user',
            'visit.staff.user',
            'medicalOrder.staff',
            'medicalOrder.orderItems.inventory',
        ]);

        $costBreakdown = null;
        if ($billing->medicalOrder) {
            $billingService = app(MedicalOrderBillingService::class);
            $costBreakdown = $billingService->getOrderCostBreakdown($billing->medicalOrder);
        }

        // Get patient name from direct relationship
        $patientName = 'Unknown Patient';
        if ($billing->patient && $billing->patient->user) {
            $patientName = $billing->patient->user->name;
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
            $patientName = $appointment->patient->user ? $appointment->patient->user->name : 'Unknown';

            return [
                'id' => $appointment->id,
                'label' => 'Appointment for '.$patientName.' on '.$appointment->appointment_date_time->format('Y-m-d'),
            ];
        });

        // Get visits without billings or current
        $visits = Visit::with('patient.user')->where(function ($query) use ($billing) {
            $query->whereDoesntHave('billings')->orWhere('id', $billing->visit_id);
        })->get()->map(function ($visit) {
            $patientName = $visit->patient->user ? $visit->patient->user->name : 'Unknown';

            return [
                'id' => $visit->id,
                'label' => 'Visit for '.$patientName.' on '.$visit->visit_date_time->format('Y-m-d H:i'),
            ];
        });

        // Get medical orders without billings or current
        $medicalOrders = MedicalOrder::with('visit.patient.user')->where(function ($query) use ($billing) {
            $query->whereDoesntHave('billings')->orWhere('id', $billing->medical_order_id);
        })->get()->map(function ($order) {
            $patientName = $order->visit?->patient?->user ? $order->visit->patient->user->name : 'Unknown';

            return [
                'id' => $order->id,
                'label' => 'Order for '.$patientName.' - '.$order->order_details,
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

        $oldStatus = $billing->status;
        $billing->update(['status' => $request->status]);

        // Generate medical record when billing is marked as paid
        if ($request->status === 'paid' && $oldStatus !== 'paid') {
            $billingService = app(MedicalOrderBillingService::class);
            $medicalRecord = $billingService->generateMedicalRecordOnPayment($billing);

            if ($medicalRecord) {
                return redirect()->back()->with('success', 'Billing status updated successfully. Medical record generated.');
            }
        }

        return redirect()->route('billings.show', $billing->id)->with('success', 'Billing status updated successfully.');
    }

    public function completePayment(Billing $billing): RedirectResponse
    {
        $this->authorize('update', $billing);

        // Update billing status to paid
        $billing->update([
            'status' => \App\Enums\BillingStatusEnum::PAID,
        ]);

        // Update related medical order status to PAID
        if ($billing->medicalOrder) {
            $billing->medicalOrder->update([
                'status' => \App\Enums\MedicalOrderStatusEnum::PAID,
            ]);
        }

        // Update related visit status to COMPLETED
        if ($billing->visit) {
            $billing->visit->update([
                'status' => \App\Models\Visit::STATUS_COMPLETED,
            ]);
        }

        return redirect()->route('billings.index')->with('success', 'Payment completed successfully. Billing moved to history.');
    }

    public function destroy(Billing $billing): RedirectResponse
    {
        $this->authorize('delete', $billing);
        $billing->delete();

        return redirect()->route('billings.index')->with('success', 'Billing record deleted successfully.');
    }

    /**
     * Generate a comprehensive billing report as PDF.
     */
    public function generateReport(Billing $billing): \Illuminate\Http\Response
    {
        $this->authorize('view', $billing);

        $billing->load([
            'patient.user',
            'appointment.staff.user',
            'visit.staff.user',
            'medicalOrder.staff.user',
            'medicalOrder.orderItems.inventory',
            'medicalOrder.medicalRecords',
        ]);

        // Get patient info from direct relationship
        $patientName = 'Unknown Patient';
        $patientInfo = null;

        if ($billing->patient) {
            $patient = $billing->patient;
            $patientName = $patient->user ? $patient->user->name : $patient->first_name.' '.$patient->last_name;
            $patientInfo = [
                'id' => $patient->id,
                'name' => $patientName,
                'date_of_birth' => $patient->date_of_birth,
                'gender' => $patient->gender,
                'phone_number' => $patient->phone_number,
                'email' => $patient->email ?? $patient->user?->email,
                'address' => $patient->address,
                'insurance_info' => $patient->insurance_info,
            ];
        }

        // Compile report data
        $report = [
            'billing_info' => [
                'id' => $billing->id,
                'amount' => $billing->amount,
                'status' => $billing->status,
                'billing_date' => $billing->billing_date,
                'due_date' => $billing->due_date,
                'paid_at' => $billing->paid_at,
                'payment_method' => $billing->payment_method,
                'transaction_id' => $billing->transaction_id,
                'notes' => $billing->notes,
                'created_at' => $billing->created_at,
                'updated_at' => $billing->updated_at,
            ],
            'patient_info' => $patientInfo,
            'appointment_info' => $billing->appointment ? [
                'id' => $billing->appointment->id,
                'appointment_date_time' => $billing->appointment->appointment_date_time,
                'duration_minutes' => $billing->appointment->duration_minutes,
                'reason_for_visit' => $billing->appointment->reason_for_visit,
                'status' => $billing->appointment->status,
                'staff_name' => $billing->appointment->staff?->user?->name ?? 'Unknown',
            ] : null,
            'visit_info' => $billing->visit ? [
                'id' => $billing->visit->id,
                'visit_date_time' => $billing->visit->visit_date_time,
                'status' => $billing->visit->status,
                'notes' => $billing->visit->notes,
                'staff_name' => $billing->visit->staff?->user?->name ?? 'Unknown',
            ] : null,
            'medical_order_info' => $billing->medicalOrder ? [
                'id' => $billing->medicalOrder->id,
                'order_type' => $billing->medicalOrder->order_type,
                'order_details' => $billing->medicalOrder->order_details,
                'status' => $billing->medicalOrder->status,
                'priority' => $billing->medicalOrder->priority,
                'ordered_at' => $billing->medicalOrder->ordered_at,
                'completed_at' => $billing->medicalOrder->completed_at,
                'staff_name' => $billing->medicalOrder->staff?->user?->name ?? 'Unknown',
            ] : null,
            'medical_orders' => $billing->medicalOrder ? collect([$billing->medicalOrder])->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_type' => $order->order_type,
                    'order_details' => $order->order_details,
                    'status' => $order->status,
                    'priority' => $order->priority,
                    'ordered_at' => $order->ordered_at,
                    'completed_at' => $order->completed_at,
                    'staff_name' => $order->staff?->user?->name ?? 'Unknown',
                ];
            }) : collect(),
            'order_items' => $billing->medicalOrder?->orderItems->map(function ($item) {
                return [
                    'id' => $item->id,
                    'item_type' => $item->item_type,
                    'item_name' => $item->inventory?->item_name ?? $item->item_name ?? 'Unknown Item',
                    'quantity_required' => $item->quantity_required,
                    'quantity_used' => $item->quantity_used,
                    'unit' => $item->inventory?->unit ?? 'units',
                    'unit_price' => $item->inventory?->unit_price ?? 0,
                    'total_cost' => ($item->quantity_used ?? $item->quantity_required ?? 1) * ($item->inventory?->unit_price ?? 0),
                    'status' => $item->status,
                ];
            }) ?? collect(),
            'medical_records' => $billing->medicalOrder?->medicalRecords->map(function ($record) {
                return [
                    'id' => $record->id,
                    'diagnosis' => $record->diagnosis,
                    'treatment' => $record->treatment,
                    'notes' => $record->notes,
                    'date_of_service' => $record->date_of_service,
                ];
            }) ?? collect(),
        ];

        // Calculate cost breakdown if medical order exists
        if ($billing->medicalOrder) {
            $costBreakdown = app(MedicalOrderBillingService::class)->getOrderCostBreakdown($billing->medicalOrder);
            $report['cost_breakdown'] = $costBreakdown;
        }

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('billing-report', compact('report', 'billing'));

        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'DejaVu Sans',
            'dpi' => 96,
            'isPhpEnabled' => true,
        ]);

        $filename = 'billing-report-'.$billing->id.'-'.now()->format('Y-m-d').'.pdf';

        return $pdf->download($filename);
    }

    /**
     * Generate a simple billing letter as PDF.
     */
    public function generateLetter(Billing $billing): \Illuminate\Http\Response
    {
        $this->authorize('view', $billing);

        $billing->load([
            'patient.user',
            'appointment.staff.user',
            'visit.staff.user',
            'medicalOrder.staff.user',
        ]);

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('billing-letter', compact('billing'));

        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'DejaVu Sans',
            'dpi' => 96,
            'isPhpEnabled' => true,
        ]);

        $filename = 'billing-letter-'.$billing->id.'-'.now()->format('Y-m-d').'.pdf';

        return $pdf->download($filename);
    }
}
