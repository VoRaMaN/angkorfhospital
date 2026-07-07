<?php

namespace App\Services;

use App\Enums\MedicalOrderPriorityEnum;
use App\Enums\MedicalOrderStatusEnum;
use App\Models\Appointment;
use App\Models\MedicalOrder;
use App\Models\MedicalRecord;
use App\Models\SpecialItem;
use App\Models\Visit;

class VisitFlowService
{
    /**
     * Get the next status in the visit flow.
     */
    public function getNextStatus(string $currentStatus): ?string
    {
        $statusFlow = [
            Visit::STATUS_PENDING => Visit::STATUS_AWAITING_ASSIGNMENT,
            Visit::STATUS_AWAITING_ASSIGNMENT => Visit::STATUS_ASSIGNED,
            Visit::STATUS_ASSIGNED => Visit::STATUS_IN_PROGRESS,
            Visit::STATUS_IN_PROGRESS => Visit::STATUS_COMPLETED,
            Visit::STATUS_COMPLETED => null,
            Visit::STATUS_CANCELLED => null,
        ];

        return $statusFlow[$currentStatus] ?? null;
    }

    /**
     * Create a visit for the given appointment. If this appointment was
     * already converted, returns its existing visit instead of creating a
     * duplicate. Deliberately does NOT dedupe against unrelated same-day
     * visits for the same patient (e.g. an already-completed walk-in visit
     * that has nothing to do with this appointment) — a patient can
     * legitimately have more than one visit in a day driven by different
     * appointments, and silently reusing an unrelated visit here would leave
     * this appointment's flags with nowhere to go.
     */
    public function createVisits(int $appointmentId): Visit
    {
        $appointment = Appointment::with('patient')->findOrFail($appointmentId);

        $existingVisit = Visit::where('appointment_id', $appointment->id)->first();

        if ($existingVisit) {
            return $existingVisit;
        }

        return Visit::create([
            'appointment_id' => $appointment->id,
            'patient_id' => $appointment->patient_id,
            'visit_date_time' => $appointment->appointment_date_time,
            'status' => Visit::STATUS_PENDING,
            'notes' => $appointment->reason_for_visit,
        ]);
    }

    /**
     * Maps appointment IVF-monitoring flags to the lab item they should
     * auto-generate on the visit's medical order, plus a catalog price to
     * look up when one exists. Only "TVS 25" has a clean matching SpecialItem
     * price; Hormone Test and Beta hCG only appear bundled inside package
     * names, so they default to 0 for staff to adjust during processing.
     */
    private const APPOINTMENT_LAB_FLAGS = [
        'is_tvs' => ['item_name' => 'TVS (Transvaginal Ultrasound Scan)', 'special_item_name' => 'TVS 25'],
        'is_hormone_test' => ['item_name' => 'Hormone Test', 'special_item_name' => null],
        'is_beta_hcg' => ['item_name' => 'Beta hCG', 'special_item_name' => null],
    ];

    /**
     * Generate medical orders for the given visit.
     */
    public function generateMedicalOrders(int $visitId): void
    {
        $visit = Visit::with('patient', 'appointment')->findOrFail($visitId);

        // Generate medical orders based on the visit
        // This is a placeholder - in a real implementation, you might
        // have logic to determine what orders to create based on
        // patient history, appointment reason, etc.

        // For now, we'll create a basic medical order
        $medicalOrder = MedicalOrder::create([
            'visit_id' => $visit->id,
            'patient_id' => $visit->patient_id,
            'staff_id' => $visit->staff_id,
            'order_details' => 'Initial assessment and basic labs',
            'status' => MedicalOrderStatusEnum::PENDING,
            'priority' => MedicalOrderPriorityEnum::ROUTINE,
            'notes' => 'Generated from visit creation',
            'ordered_at' => now(),
        ]);

        if ($visit->appointment) {
            $this->addAppointmentLabItems($medicalOrder, $visit->appointment);
        }
    }

    /**
     * Auto-add lab order items for whichever IVF-monitoring flags are set on
     * the appointment, so they flow into the Lab Panel workflow without staff
     * having to manually re-select them during order processing.
     */
    private function addAppointmentLabItems(MedicalOrder $medicalOrder, Appointment $appointment): void
    {
        foreach (self::APPOINTMENT_LAB_FLAGS as $flag => $config) {
            if (! $appointment->{$flag}) {
                continue;
            }

            $price = $config['special_item_name']
                ? (float) (SpecialItem::where('name', $config['special_item_name'])->value('unit_price') ?? 0)
                : 0;

            $medicalOrder->orderItems()->create([
                'item_type' => 'lab',
                'item_name' => $config['item_name'],
                'quantity_required' => 1,
                'unit_price' => $price,
                'selling_price' => $price,
                'status' => MedicalOrderStatusEnum::PENDING,
                'notes' => 'Auto-added from appointment IVF monitoring flags',
            ]);
        }
    }

    /**
     * Complete the medical order and potentially the visit.
     */
    public function completeMedicalOrder(int $medicalOrderId): void
    {
        $medicalOrder = MedicalOrder::findOrFail($medicalOrderId);

        $medicalOrder->update([
            'status' => MedicalOrderStatusEnum::COMPLETED,
            'completed_at' => now(),
        ]);

        // Check if all medical orders for this visit are completed
        $visit = $medicalOrder->visit;
        if ($visit) {
            $allOrdersCompleted = $visit->medicalOrders()
                ->where('status', '!=', MedicalOrderStatusEnum::COMPLETED)
                ->doesntExist();

            if ($allOrdersCompleted) {
                // Mark the visit as completed
                $visit->update(['status' => Visit::STATUS_COMPLETED]);

                // Create medical record for the visit if it doesn't exist
                if (! $visit->medicalRecord) {
                    $appointment = $visit->appointment;
                    // Generate diagnosis and treatment based on completed orders
                    $diagnosis = $this->generateDiagnosisFromOrders($visit);
                    $treatment = $this->generateTreatmentFromOrders($visit);
                    $notes = $this->generateNotesFromOrders($visit);

                    MedicalRecord::create([
                        'appointment_id' => $appointment?->id,
                        'visit_id' => $visit->id,
                        'diagnosis' => $diagnosis,
                        'treatment' => $treatment,
                        'notes' => $notes,
                        'date_of_service' => $visit->visit_date_time->toDateString(),
                    ]);
                }
            }
        }
    }

    /**
     * Generate diagnosis based on completed medical orders.
     */
    private function generateDiagnosisFromOrders(Visit $visit): string
    {
        $completedOrders = $visit->medicalOrders()->where('status', MedicalOrderStatusEnum::COMPLETED)->get();
        $diagnoses = [];

        foreach ($completedOrders as $order) {
            // Extract diagnosis information from order details or items
            if ($order->order_details) {
                $diagnoses[] = $order->order_details;
            }

            foreach ($order->orderItems as $item) {
                if ($item->details && str_contains(strtolower($item->details), 'diagnos')) {
                    $diagnoses[] = $item->details;
                }
            }
        }

        return $diagnoses ? implode('; ', array_unique($diagnoses)) : 'Assessment completed - see order details for specific findings';
    }

    /**
     * Generate treatment information based on completed medical orders.
     */
    private function generateTreatmentFromOrders(Visit $visit): string
    {
        $completedOrders = $visit->medicalOrders()->where('status', MedicalOrderStatusEnum::COMPLETED)->get();
        $treatments = [];

        foreach ($completedOrders as $order) {
            foreach ($order->orderItems as $item) {
                $treatmentInfo = [];

                if ($item->item_name) {
                    $treatmentInfo[] = $item->item_name;
                }

                if ($item->dosage) {
                    $treatmentInfo[] = "Dosage: {$item->dosage}";
                }

                if ($item->frequency) {
                    $treatmentInfo[] = "Frequency: {$item->frequency}";
                }

                if ($item->route) {
                    $treatmentInfo[] = "Route: {$item->route}";
                }

                if ($treatmentInfo) {
                    $treatments[] = implode(' - ', $treatmentInfo);
                }
            }
        }

        return $treatments ? implode('; ', $treatments) : 'Treatments administered as ordered';
    }

    /**
     * Generate notes based on completed medical orders.
     */
    private function generateNotesFromOrders(Visit $visit): string
    {
        $completedOrders = $visit->medicalOrders()->where('status', MedicalOrderStatusEnum::COMPLETED)->get();
        $notes = [];

        $notes[] = 'Visit completed on '.$visit->visit_date_time->format('M j, Y \a\t g:i A');

        foreach ($completedOrders as $order) {
            $orderNotes = [];

            if ($order->orderItems->count() > 0) {
                $orderNotes[] = "Completed {$order->orderItems->count()} item(s) from order: {$order->order_details}";
            }

            if ($order->notes) {
                $orderNotes[] = "Order notes: {$order->notes}";
            }

            foreach ($order->orderItems as $item) {
                if ($item->notes) {
                    $orderNotes[] = "Item '{$item->item_name}': {$item->notes}";
                }
            }

            if ($orderNotes) {
                $notes[] = implode(' | ', $orderNotes);
            }
        }

        return implode("\n\n", $notes);
    }

    /**
     * Update the status of a visit.
     */
    public function updateVisitStatus(int $visitId, string $status): void
    {
        $visit = Visit::findOrFail($visitId);
        $visit->update([
            'status' => $status,
        ]);

        $visit->refresh();
    }

    /**
     * Assign staff to process the visit via medical order.
     */
    public function assignStaffToProcessVisit(int $medicalOrderId, int $staffId): void
    {
        $medicalOrder = MedicalOrder::findOrFail($medicalOrderId);

        $medicalOrder->update([
            'staff_id' => $staffId,
        ]);

        // Also assign staff to the visit if not already assigned
        if ($medicalOrder->visit && ! $medicalOrder->visit->staff_id) {
            $medicalOrder->visit->update([
                'staff_id' => $staffId,
                'status' => Visit::STATUS_ASSIGNED,
            ]);
        }
    }

    /**
     * Get visits awaiting to be assigned for the current user.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, Visit>
     */
    public function getAwaitingToBeAssignVisits(): \Illuminate\Database\Eloquent\Collection
    {
        // Exclude completed visits
        return Visit::with(['patient.user', 'appointment', 'medicalOrders'])
            ->where('status', '!=', Visit::STATUS_COMPLETED)
            ->get();
    }

    /**
     * Notify staff that the visit is ready for assignment.
     */
    public function notifyStaffForAssignment(int $visitId): void
    {
        $this->updateVisitStatus($visitId, Visit::STATUS_AWAITING_ASSIGNMENT);
    }
}
