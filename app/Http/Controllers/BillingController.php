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

        // Auto-mark yesterday's unpaid bills as overdue
        Billing::whereIn('status', [
            \App\Enums\BillingStatusEnum::PENDING,
            \App\Enums\BillingStatusEnum::PARTIAL,
        ])
            ->whereDate('billing_date', '<', today())
            ->update(['status' => \App\Enums\BillingStatusEnum::OVERDUE]);

        $query = Billing::with(['patient.user', 'appointment', 'visit', 'medicalOrder']);

        // Filter by status if provided
        if (request('status')) {
            $query->where('status', request('status'));
        } else {
            // Default: hide paid bills unless explicitly filtered
            $query->where('status', '!=', \App\Enums\BillingStatusEnum::PAID);
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

        // Filter by billing date range if provided, default to today
        if (request('start_date')) {
            $query->whereDate('billing_date', '>=', request('start_date'));
        }
        if (request('end_date')) {
            $query->whereDate('billing_date', '<=', request('end_date'));
        }
        if (! request('start_date') && ! request('end_date')) {
            // Only apply today filter on the default All tab (no specific status)
            if (! request('status')) {
                $query->whereDate('billing_date', today());
            }
        }

        $billings = $query->orderBy('billing_date', 'desc')->orderBy('created_at', 'desc')
            ->paginate(15)->appends(request()->only(['search', 'status', 'start_date', 'end_date']));

        // Transform billings for the frontend
        $transformedBillings = $billings->getCollection()->map(function ($billing) {
            $patient = $billing->patient;
            $patientName = 'Unknown Patient';

            if ($patient) {
                $patientName = $patient->full_name ?: 'Patient #'.$patient->id;
            }

            // The system does not track partial payments separately, so a
            // billing is either fully paid (status PAID) or not paid at all.
            $netAmount = $billing->amount - ($billing->discount_amount ?? 0);
            $paidAmount = $billing->status === \App\Enums\BillingStatusEnum::PAID ? $netAmount : 0;

            return [
                'id' => $billing->id,
                'bill_no' => $billing->bill_no,
                'patient_id' => $patient?->id,
                'patient_name' => $patientName,
                'appointment_id' => $billing->appointment_id,
                'visit_id' => $billing->visit_id,
                'medical_order_id' => $billing->medical_order_id,
                'total_amount' => $billing->amount,
                'paid_amount' => $paidAmount,
                'outstanding_amount' => $netAmount - $paidAmount,
                'status' => $billing->status,
                'billing_date' => $billing->billing_date,
                'due_date' => $billing->billing_date?->copy()->addDays(30), // TODO: Add due_date field to model
                'created_at' => $billing->created_at,
            ];
        });

        return Inertia::render('Billings/Index', [
            'billings' => $transformedBillings,
            'overdueCount' => Billing::where('status', \App\Enums\BillingStatusEnum::OVERDUE)->count(),
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
            return [
                'id' => $appointment->id,
                'label' => 'Appointment for '.($appointment->patient?->full_name ?: 'Unknown').' on '.$appointment->appointment_date_time->setTimezone('Asia/Phnom_Penh')->format('d/m/y'),
            ];
        });

        // Get visits without billings
        $visits = Visit::with('patient.user')->whereDoesntHave('billings')->get()->map(function ($visit) {
            return [
                'id' => $visit->id,
                'label' => 'Visit for '.($visit->patient?->full_name ?: 'Unknown').' on '.$visit->visit_date_time->format('d/m/y H:i'),
            ];
        });

        // Get medical orders without billings
        $medicalOrders = MedicalOrder::with('visit.patient.user')->whereDoesntHave('billings')->get()->map(function ($order) {
            return [
                'id' => $order->id,
                'label' => 'Order for '.($order->visit?->patient?->full_name ?: 'Unknown').' - '.$order->order_details,
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

                // Auto-set patient_id from medical order
                if (empty($data['patient_id']) && $medicalOrder->patient_id) {
                    $data['patient_id'] = $medicalOrder->patient_id;
                }
            }
        }

        // Auto-derive patient_id from visit if not yet set
        if (empty($data['patient_id']) && ! empty($data['visit_id'])) {
            $visit = Visit::find($data['visit_id']);
            if ($visit) {
                $data['patient_id'] = $visit->patient_id;
            }
        }

        // Auto-derive patient_id from appointment if still not set
        if (empty($data['patient_id']) && ! empty($data['appointment_id'])) {
            $appointment = \App\Models\Appointment::find($data['appointment_id']);
            if ($appointment) {
                $data['patient_id'] = $appointment->patient_id;
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
        $patientName = $billing->patient?->full_name ?: 'Unknown Patient';

        $transformedBilling = [
            'id' => $billing->id,
            'bill_no' => $billing->bill_no,
            'patient_name' => $patientName,
            'appointment_id' => $billing->appointment_id,
            'visit_id' => $billing->visit_id,
            'medical_order_id' => $billing->medical_order_id,
            'amount' => $billing->amount,
            'discount_amount' => $billing->discount_amount ?? 0,
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
                'label' => 'Appointment for '.($appointment->patient?->full_name ?: 'Unknown').' on '.$appointment->appointment_date_time->setTimezone('Asia/Phnom_Penh')->format('d/m/y'),
            ];
        });

        // Get visits without billings or current
        $visits = Visit::with('patient.user')->where(function ($query) use ($billing) {
            $query->whereDoesntHave('billings')->orWhere('id', $billing->visit_id);
        })->get()->map(function ($visit) {
            return [
                'id' => $visit->id,
                'label' => 'Visit for '.($visit->patient?->full_name ?: 'Unknown').' on '.$visit->visit_date_time->format('d/m/y H:i'),
            ];
        });

        // Get medical orders without billings or current
        $medicalOrders = MedicalOrder::with('visit.patient.user')->where(function ($query) use ($billing) {
            $query->whereDoesntHave('billings')->orWhere('id', $billing->medical_order_id);
        })->get()->map(function ($order) {
            return [
                'id' => $order->id,
                'label' => 'Order for '.($order->visit?->patient?->full_name ?: 'Unknown').' - '.$order->order_details,
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
            'status' => 'required|in:pending,paid,overdue,partial,cancelled,revision,revised,sent_to_account',
        ]);

        $this->authorize('updateStatus', $billing);

        $oldStatus = $billing->status;
        $data = ['status' => $request->status];

        // Refreshing billing_date whenever a bill is manually put back into
        // pending/partial keeps it immune to the auto-overdue sweep below
        // (and to the console `billings:mark-overdue` equivalent) so it
        // actually stays visible as "Pending" instead of being silently
        // flipped back to "Overdue" the next time anyone loads the billings
        // list — mirrors the existing revised-billing billing_date refresh.
        if (in_array($request->status, ['pending', 'partial'], true)) {
            $data['billing_date'] = now()->toDateString();
        }

        $billing->update($data);

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
                'due_date' => null,
                'paid_at' => null,
                'payment_method' => null,
                'transaction_id' => null,
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
                    'order_details' => $order->order_details,
                    'status' => $order->status,
                    'priority' => $order->priority,
                    'ordered_at' => $order->ordered_at,
                    'completed_at' => $order->completed_at,
                    'staff_name' => $order->staff?->user?->name ?? 'Unknown',
                ];
            }) : collect(),
            'order_items' => $billing->medicalOrder?->orderItems->map(function ($item) {
                $unitPrice = $item->selling_price > 0 ? $item->selling_price : ($item->unit_price > 0 ? $item->unit_price : ($item->inventory?->selling_price ?? $item->inventory?->unit_price ?? 0));
                $qty = $item->quantity_required ?? 1;

                return [
                    'id' => $item->id,
                    'item_type' => $item->item_type,
                    'item_name' => $item->item_name ?? $item->inventory?->item_name ?? 'Unknown Item',
                    'quantity_required' => $qty,
                    'quantity_used' => $item->quantity_used,
                    'unit' => $item->inventory?->unit ?? 'units',
                    'unit_price' => $unitPrice,
                    'total_cost' => $qty * $unitPrice,
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

        ini_set('memory_limit', '512M');
        $pdf = app('dompdf.wrapper');
        $pdf->loadView('billing-report', compact('report', 'billing'));

        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'DejaVu Sans',
            'dpi' => 96,
            'isPhpEnabled' => true,
        ], true);

        $filename = 'billing-report-'.$billing->id.'-'.now()->format('Y-m-d').'.pdf';

        return $pdf->stream($filename);
    }

    /**
     * Generate a simple billing letter as PDF.
     */
    public function applyDiscount(Request $request, Billing $billing): \Illuminate\Http\RedirectResponse
    {
        $this->authorize('update', $billing);

        $request->validate([
            'discount_amount' => 'required|numeric|min:0|max:'.(float) $billing->amount,
        ]);

        $billing->update(['discount_amount' => $request->discount_amount]);

        return redirect()->back()->with('success', 'Discount applied successfully.');
    }

    public function generateLetter(Billing $billing): \Illuminate\Http\Response
    {
        $this->authorize('view', $billing);

        $billing->load([
            'patient.user',
            'appointment.staff.user',
            'visit.staff.user',
            'medicalOrder.staff.user',
            'medicalOrder.orderItems.inventory',
        ]);

        // Get cost breakdown if medical order exists
        $costBreakdown = null;
        if ($billing->medicalOrder) {
            $costBreakdown = app(MedicalOrderBillingService::class)->getOrderCostBreakdown($billing->medicalOrder);
        }

        ini_set('memory_limit', '512M');
        $pdf = app('dompdf.wrapper');
        $pdf->loadView('billing-letter', compact('billing', 'costBreakdown'));

        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'DejaVu Sans',
            'dpi' => 96,
            'isPhpEnabled' => true,
        ], true);

        $filename = 'receipt-'.$billing->bill_no.'-'.now()->format('Y-m-d').'.pdf';

        return $pdf->stream($filename);
    }

    /**
     * Send billing back to nurse for order revision (add/remove items).
     */
    public function sendBackToNurse(Request $request, Billing $billing): RedirectResponse
    {
        $this->authorize('sendBack', $billing);

        $request->validate([
            'reason' => 'nullable|string|max:1000',
        ]);

        // Must have a linked medical order to send back
        if (! $billing->medical_order_id || ! $billing->medicalOrder) {
            return redirect()->back()->with('error', 'This billing has no linked medical order and cannot be sent back for revision.');
        }

        // Already in revision (would double-restore inventory before the nurse
        // resubmits), or a dead/terminal status (cancelled, written off) that
        // should never be revived via send-back.
        $blockedStatuses = [
            \App\Enums\BillingStatusEnum::REVISION,
            \App\Enums\BillingStatusEnum::CANCELLED,
            \App\Enums\BillingStatusEnum::WRITTEN_OFF,
        ];

        if (in_array($billing->status, $blockedStatuses, true)) {
            return redirect()->back()->with('error', 'This billing cannot be sent back for revision at its current status.');
        }

        $medicalOrder = $billing->medicalOrder;
        $medicalOrder->load('orderItems.inventory');

        // Restore inventory stock before sending back (will be re-reduced when reprocessed)
        $billingService = app(MedicalOrderBillingService::class);
        $billingService->restoreInventoryStock($medicalOrder);

        // Track revision in billing notes
        $reason = trim((string) $request->reason);
        $currentNotes = $billing->notes ? $billing->notes."\n\n" : '';
        $revisionNote = 'Sent back for revision on '.now()->format('Y-m-d H:i').($reason !== '' ? ":\n".$reason : '.');

        // Update billing status to revision
        $billing->update([
            'status' => \App\Enums\BillingStatusEnum::REVISION,
            'notes' => $currentNotes.$revisionNote,
        ]);

        // Reopen the medical order for editing (set back to PENDING so nurse can access processPage)
        $orderNotes = $medicalOrder->notes ? $medicalOrder->notes."\n\n" : '';
        $medicalOrder->update([
            'status' => \App\Enums\MedicalOrderStatusEnum::PENDING,
            'completed_at' => null,
            'notes' => $orderNotes.'Billing sent back for revision on '.now()->format('Y-m-d H:i').($reason !== '' ? ":\n".$reason : '.'),
        ]);

        // Reset order items back to pending so nurse can edit
        $medicalOrder->orderItems()->update([
            'status' => \App\Enums\MedicalOrderStatusEnum::PENDING->value,
            'completed_at' => null,
        ]);

        // Update visit status to sent_back
        if ($billing->visit) {
            $billing->visit->update([
                'status' => Visit::STATUS_SENT_BACK,
            ]);
        }

        return redirect()->route('billings.show', $billing)
            ->with('success', 'Billing has been sent back to the nurse for revision. The medical order is now editable.');
    }

    /**
     * Accountant receives billing sent by nurse — recalculates amount and sets status to pending.
     */
    public function receive(Billing $billing): RedirectResponse
    {
        $this->authorize('receive', $billing);

        $receivableStatuses = [
            \App\Enums\BillingStatusEnum::REVISED,
            \App\Enums\BillingStatusEnum::SENT_TO_ACCOUNT,
        ];

        if (! in_array($billing->status, $receivableStatuses)) {
            return redirect()->back()->with('error', 'This billing cannot be received at this stage.');
        }

        if ($billing->medical_order_id && $billing->medicalOrder) {
            $billingService = app(MedicalOrderBillingService::class);
            $billing->update([
                'amount' => $billingService->calculateOrderTotal($billing->medicalOrder),
                'status' => \App\Enums\BillingStatusEnum::PENDING->value,
            ]);
        } else {
            $billing->update([
                'status' => \App\Enums\BillingStatusEnum::PENDING->value,
            ]);
        }

        return redirect()->route('billings.show', $billing)
            ->with('success', 'Billing received. You can now review and complete payment.');
    }

    /**
     * Admin-only safety net: force-finalize a medical order and its billing that got
     * stuck in "revision" status because the nurse saved without clicking "Send to
     * Account". Finalizes the order exactly as currently saved.
     */
    public function recoverStuckRevision(Billing $billing): RedirectResponse
    {
        $this->authorize('recoverStuckRevision', $billing);

        if ($billing->status !== \App\Enums\BillingStatusEnum::REVISION) {
            return redirect()->back()->with('error', 'This billing is not in a stuck revision state.');
        }

        if (! $billing->medical_order_id || ! $billing->medicalOrder) {
            return redirect()->back()->with('error', 'This billing has no linked medical order to recover.');
        }

        $billingService = app(MedicalOrderBillingService::class);
        $billingService->finalizeRevisedOrder(
            $billing->medicalOrder,
            $billing,
            'Force-recovered from stuck revision by '.auth()->user()->name.' on '.now()->format('Y-m-d H:i').'.'
        );

        return redirect()->route('billings.show', $billing)
            ->with('success', 'Order recovered and sent to account for payment.');
    }

    /**
     * Recalculate billing amount from the linked medical order after revision.
     */
    public function recalculate(Billing $billing): RedirectResponse
    {
        $this->authorize('update', $billing);

        if (! $billing->medical_order_id || ! $billing->medicalOrder) {
            return redirect()->back()->with('error', 'This billing has no linked medical order to recalculate from.');
        }

        $billingService = app(MedicalOrderBillingService::class);
        $newAmount = $billingService->calculateOrderTotal($billing->medicalOrder);

        $billing->update([
            'amount' => $newAmount,
        ]);

        return redirect()->route('billings.show', $billing)
            ->with('success', 'Billing amount recalculated successfully. New total: $'.number_format($newAmount, 2));
    }

    public function export(): \Illuminate\Http\Response
    {
        $this->authorize('viewAny', Billing::class);

        $filters = [
            'search' => request('search', ''),
            'status' => request('status', ''),
            'start_date' => request('start_date', ''),
            'end_date' => request('end_date', ''),
        ];

        $exporter = new \App\Exports\BillingsExport($filters);

        return $exporter->download();
    }
}
