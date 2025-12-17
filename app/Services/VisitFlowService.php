<?php

namespace App\Services;

use App\Enums\MedicalOrderPriorityEnum;
use App\Enums\MedicalOrderStatusEnum;
use App\Enums\MedicalOrderTypeEnum;
use App\Models\Appointment;
use App\Models\MedicalOrder;
use App\Models\MedicalRecord;
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
     * Create visits for the given appointment.
     */
    public function createVisits(int $appointmentId): void
    {
        $appointment = Appointment::with('patient')->findOrFail($appointmentId);

        // Create a visit record for this appointment
        Visit::create([
            'appointment_id' => $appointment->id,
            'patient_id' => $appointment->patient_id,
            'visit_date_time' => $appointment->appointment_date_time,
            'status' => Visit::STATUS_PENDING,
            'notes' => $appointment->reason_for_visit,
        ]);
    }

    /**
     * Generate medical orders for the given visit.
     */
    public function generateMedicalOrders(int $visitId): void
    {
        $visit = Visit::with('patient')->findOrFail($visitId);

        // Generate medical orders based on the visit
        // This is a placeholder - in a real implementation, you might
        // have logic to determine what orders to create based on
        // patient history, appointment reason, etc.

        // For now, we'll create a basic medical order
        MedicalOrder::create([
            'visit_id' => $visit->id,
            'patient_id' => $visit->patient_id,
            'staff_id' => $visit->staff_id, // Use the assigned staff
            'order_type' => MedicalOrderTypeEnum::LAB,
            'order_details' => 'Initial assessment and basic labs',
            'status' => MedicalOrderStatusEnum::PENDING,
            'priority' => MedicalOrderPriorityEnum::ROUTINE,
            'notes' => 'Generated from visit creation',
            'ordered_at' => now(),
        ]);
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
