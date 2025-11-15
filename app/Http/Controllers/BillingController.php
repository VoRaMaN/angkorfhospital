<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBillingRequest;
use App\Http\Requests\UpdateBillingRequest;
use App\Models\Billing;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class BillingController extends Controller
{
    public function index(): Response
    {
        $this->authorize('viewAny', Billing::class);
        $billings = Billing::with(['appointment.patient.user'])->paginate(15);

        // Transform billings for the frontend
        $transformedBillings = $billings->getCollection()->map(function ($billing) {
            $patientName = 'Unknown Patient';
            if ($billing->appointment && $billing->appointment->patient && $billing->appointment->patient->user) {
                $patient = $billing->appointment->patient;
                $patientName = $patient->first_name.' '.$patient->last_name;
            }

            return [
                'id' => $billing->id,
                'patient_id' => $billing->appointment?->patient?->id,
                'patient_name' => $patientName,
                'appointment_id' => $billing->appointment_id,
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
            ],
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Billing::class);

        return Inertia::render('Billings/Create');
    }

    public function store(StoreBillingRequest $request): RedirectResponse
    {
        $billing = Billing::create($request->validated());

        return redirect()->route('billings.index')->with('success', 'Billing record created successfully.');
    }

    public function show(Billing $billing): Response
    {
        $this->authorize('view', $billing);
        $billing->load(['appointment.patient.user', 'appointment.doctor.staff.user']);

        return Inertia::render('Billings/Show', [
            'billing' => $billing,
        ]);
    }

    public function edit(Billing $billing): Response
    {
        $this->authorize('update', $billing);

        return Inertia::render('Billings/Edit', [
            'billing' => $billing,
        ]);
    }

    public function update(UpdateBillingRequest $request, Billing $billing): RedirectResponse
    {
        $billing->update($request->validated());

        return redirect()->route('billings.index')->with('success', 'Billing record updated successfully.');
    }

    public function destroy(Billing $billing): RedirectResponse
    {
        $this->authorize('delete', $billing);
        $billing->delete();

        return redirect()->route('billings.index')->with('success', 'Billing record deleted successfully.');
    }
}
