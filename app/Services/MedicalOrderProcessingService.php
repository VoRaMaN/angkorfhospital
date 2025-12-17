<?php

namespace App\Services;

use App\Enums\MedicalOrderStatusEnum;
use App\Models\Billing;
use App\Models\MedicalOrder;
use App\Models\Staff;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MedicalOrderProcessingService
{
    public function __construct(
        protected MedicalOrderBillingService $billingService
    ) {}

    /**
     * Process medical order: validate, notify staff, create billing
     */
    public function processOrder(MedicalOrder $medicalOrder): array
    {
        return DB::transaction(function () use ($medicalOrder) {
            // 1. Validate inventory availability
            $validation = $this->billingService->canProcessOrder($medicalOrder);
            if (! $validation['can_process']) {
                return [
                    'success' => false,
                    'error' => 'Insufficient inventory: '.implode(', ', $validation['issues']),
                ];
            }

            // 2. Update order status to PROCESSING
            $medicalOrder->update([
                'status' => MedicalOrderStatusEnum::PROCESSING,
            ]);

            // 3. Update visit status if linked
            if ($medicalOrder->visit) {
                $medicalOrder->visit->update(['status' => 'in_progress']);
            }

            // 4. Notify relevant departments based on order items
            $notifications = $this->notifyDepartments($medicalOrder);

            // 5. Reduce inventory stock for medicines and supplies
            $this->billingService->reduceInventoryStock($medicalOrder);

            // 6. Create billing record
            $billing = $this->createBilling($medicalOrder);

            // 7. Update order status to COMPLETED after billing created
            $medicalOrder->update([
                'status' => MedicalOrderStatusEnum::COMPLETED,
                'completed_at' => now(),
            ]);

            // 8. Update all order items to completed
            $medicalOrder->orderItems()->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            return [
                'success' => true,
                'billing_id' => $billing->id,
                'notifications_sent' => $notifications,
                'total_amount' => $billing->amount,
            ];
        });
    }

    /**
     * Notify relevant departments based on order item types
     */
    protected function notifyDepartments(MedicalOrder $medicalOrder): array
    {
        $notifications = [];
        $itemsByDepartment = $this->groupItemsByDepartment($medicalOrder);

        foreach ($itemsByDepartment as $department => $items) {
            $staffList = $this->getStaffByDepartment($department);

            if ($staffList->isEmpty()) {
                Log::warning("No staff found for department: {$department}");

                continue;
            }

            foreach ($staffList as $staff) {
                // Here you would send actual notifications (email, SMS, in-app)
                // For now, we'll log and track them
                $this->sendNotification($staff, $medicalOrder, $items, $department);

                $notifications[] = [
                    'department' => $department,
                    'staff_id' => $staff->id,
                    'staff_name' => $staff->user?->name ?? 'Unknown',
                    'items_count' => count($items),
                ];
            }

            Log::info("Notified {$department} department for Medical Order #{$medicalOrder->id}");
        }

        return $notifications;
    }

    /**
     * Group order items by department
     */
    protected function groupItemsByDepartment(MedicalOrder $medicalOrder): array
    {
        $departments = [];

        foreach ($medicalOrder->orderItems as $item) {
            $department = match ($item->item_type) {
                'lab' => 'Laboratory',
                'rx_medicine' => 'Pharmacy',
                'procedure' => 'Procedure Room',
                'imaging' => 'Radiology',
                'supply' => 'Supply/Inventory',
                default => 'General',
            };

            if (! isset($departments[$department])) {
                $departments[$department] = [];
            }

            $departments[$department][] = [
                'item_name' => $item->item_name,
                'quantity' => $item->quantity_required ?? 1,
                'type' => $item->item_type,
                'details' => $item->details,
                'notes' => $item->notes,
            ];
        }

        return $departments;
    }

    /**
     * Get staff members by department name
     */
    protected function getStaffByDepartment(string $departmentName)
    {
        // Map department names to your actual department records
        $departmentMapping = [
            'Laboratory' => ['Laboratory', 'Lab'],
            'Pharmacy' => ['Pharmacy'],
            'Procedure Room' => ['Surgery', 'Procedures'],
            'Radiology' => ['Radiology', 'Imaging'],
            'Supply/Inventory' => ['Inventory', 'Supply'],
        ];

        $searchNames = $departmentMapping[$departmentName] ?? [$departmentName];

        return Staff::with('user')
            ->whereHas('department', function ($query) use ($searchNames) {
                $query->whereIn('name', $searchNames);
            })
            ->get();
    }

    /**
     * Send notification to staff member
     */
    protected function sendNotification(Staff $staff, MedicalOrder $medicalOrder, array $items, string $department): void
    {
        // This is where you would integrate with your notification system
        // Examples:
        // - Email notification
        // - SMS notification
        // - In-app notification
        // - Push notification

        $patientName = $medicalOrder->patient?->user?->name ?? $medicalOrder->visit?->patient?->user?->name ?? 'Unknown Patient';

        $message = sprintf(
            'New medical order #%d for patient %s requires %s services. %d item(s) need processing.',
            $medicalOrder->id,
            $patientName,
            $department,
            count($items)
        );

        // Log the notification for now
        Log::channel('daily')->info("Notification to {$staff->user?->name}: {$message}");

        // TODO: Implement actual notification sending
        // Mail::to($staff->user->email)->send(new MedicalOrderNotification($medicalOrder, $items));
        // Notification::send($staff->user, new MedicalOrderAlert($medicalOrder, $department, $items));
    }

    /**
     * Create billing record from medical order
     */
    protected function createBilling(MedicalOrder $medicalOrder): Billing
    {
        $totalAmount = $this->billingService->calculateOrderTotal($medicalOrder);
        $patientId = $medicalOrder->patient_id ?? $medicalOrder->visit?->patient_id;
        $doctorId = $medicalOrder->visit?->doctor_id;

        return Billing::create([
            'patient_id' => $patientId,
            'appointment_id' => $medicalOrder->visit?->appointment_id,
            'visit_id' => $medicalOrder->visit_id,
            'medical_order_id' => $medicalOrder->id,
            'doctor_id' => $doctorId,
            'amount' => $totalAmount,
            'status' => 'pending',
            'billing_date' => now()->toDateString(),
            'notes' => $this->generateBillingNotes($medicalOrder),
        ]);
    }

    /**
     * Generate detailed billing notes
     */
    protected function generateBillingNotes(MedicalOrder $medicalOrder): string
    {
        $breakdown = $this->billingService->getOrderCostBreakdown($medicalOrder);

        $notes = "Medical Order #{$medicalOrder->id}\n";
        $notes .= "Order Details: {$medicalOrder->order_details}\n\n";
        $notes .= "Items Breakdown:\n";

        foreach ($breakdown['groups'] as $group) {
            $notes .= "- {$group['name']}: {$group['item_count']} item(s) = $".number_format($group['subtotal'], 2)."\n";
        }

        $notes .= "\nTotal: $".number_format($breakdown['total'], 2);

        return $notes;
    }
}
