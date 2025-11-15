<?php

namespace App\Services;

use App\Enums\MedicalOrderPriorityEnum;
use App\Enums\MedicalOrderStatusEnum;
use App\Enums\MedicalOrderTypeEnum;
use App\Models\Appointment;
use App\Models\MedicalOrder;
use App\Models\MedicalRecord;
use App\Models\Visit;

class FlowService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        /**
         * Workflow for Visit and Medical Order Processing:
         * 1. Create Visit records using existing appointment data or independently.
         * 2. Generate Medical Orders associated with the Visit records.
         * 3. Assign staff members to Visits and Medical Orders.
         * 4. Notify assigned staff to begin processing the Medical Orders.
         * 5. Staff initiate processing by clicking the process button, updating status accordingly.
         * 6. Notify Accounting staff upon processing completion.
         * 7. Accounting staff mark as complete after payment, then generate invoices for the Medical Orders.
         * 8. Finally, mark the Medical Orders as completed.
         */
    }

    public function createVisits(int $appointmentId): void
    {
        $appointment = Appointment::with('patient')->findOrFail($appointmentId);

        // Create a visit record for this appointment
        Visit::create([
            'appointment_id' => $appointment->id,
            'patient_id' => $appointment->patient_id,
            'visit_date_time' => $appointment->appointment_date_time,
            'status' => 'pending',
            'notes' => $appointment->reason_for_visit,
        ]);
    }

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
            'staff_id' => $visit->staff_id,
            'order_type' => MedicalOrderTypeEnum::LAB,
            'order_details' => 'Initial assessment and basic labs',
            'status' => MedicalOrderStatusEnum::PENDING,
            'priority' => MedicalOrderPriorityEnum::ROUTINE,
            'notes' => 'Generated from visit creation',
            'ordered_at' => now(),
        ]);
    }

    public function processMedicalOrder(int $medicalOrderId): void
    {
        $medicalOrder = MedicalOrder::with('orderItems')->findOrFail($medicalOrderId);

        // Check if the medical order has any items
        if ($medicalOrder->orderItems->isEmpty()) {
            throw new \InvalidArgumentException('Cannot process medical order with no items. Please add at least one item before processing.');
        }

        // Update the status to processing
        $medicalOrder->update([
            'status' => MedicalOrderStatusEnum::PROCESSED,
        ]);

        // Here you could add logic to notify staff, update inventory, etc.
    }

    public function assignStaffToOrder(int $medicalOrderId, int $staffId): void
    {
        $medicalOrder = MedicalOrder::findOrFail($medicalOrderId);

        $medicalOrder->update([
            'staff_id' => $staffId,
        ]);

        // Also assign staff to the visit if not already assigned
        if ($medicalOrder->visit && !$medicalOrder->visit->staff_id) {
            $medicalOrder->visit->update([
                'staff_id' => $staffId,
            ]);
        }
    }

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
                $visit->update(['status' => 'completed']);

                // Create medical record for the visit if it doesn't exist
                if (!$visit->medicalRecord) {
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
     * Generate diagnosis based on completed medical orders
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
     * Generate treatment information based on completed medical orders
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
     * Generate notes based on completed medical orders
     */
    private function generateNotesFromOrders(Visit $visit): string
    {
        $completedOrders = $visit->medicalOrders()->where('status', MedicalOrderStatusEnum::COMPLETED)->get();
        $notes = [];

        $notes[] = "Visit completed on " . $visit->visit_date_time->format('M j, Y \a\t g:i A');

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
}
