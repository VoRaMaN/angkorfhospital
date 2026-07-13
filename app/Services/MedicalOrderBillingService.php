<?php

namespace App\Services;

use App\Enums\MedicalOrderStatusEnum;
use App\Models\Billing;
use App\Models\Inventory;
use App\Models\MedicalOrder;
use App\Models\MedicalOrderInventory;
use App\Models\MedicalRecord;
use App\Models\MedicalService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MedicalOrderBillingService
{
    /**
     * Calculate the total cost of items in a medical order
     */
    public function calculateOrderTotal(MedicalOrder $medicalOrder): float
    {
        $total = 0;

        foreach ($medicalOrder->orderItems as $orderItem) {
            $itemTotal = $this->calculateItemTotal($orderItem);
            $total += $itemTotal;
        }

        return $total;
    }

    /**
     * Calculate the total cost for a single order item
     */
    public function calculateItemTotal(MedicalOrderInventory $orderItem): float
    {
        // Package-included items are deliberately free: their price is covered
        // by the package, so never fall back to catalog/inventory prices.
        if ($orderItem->isPackageIncluded()) {
            return 0;
        }

        $quantity = $orderItem->quantity_required ?? 1;

        // Use stored selling_price if available
        if ($orderItem->selling_price > 0) {
            return $orderItem->selling_price * $quantity;
        }

        // For inventory items (lab tests, medicines, supplies)
        if ($orderItem->inventory_id) {
            $inventory = $orderItem->inventory;
            if ($inventory) {
                return $inventory->selling_price * $quantity;
            }
        }

        // For medical services (procedures, imaging, consultation, therapy)
        if (in_array($orderItem->item_type, ['procedure', 'imaging', 'consultation', 'therapy'])) {
            $service = MedicalService::where('name', $orderItem->item_name)
                ->where('type', $orderItem->item_type)
                ->first();

            if ($service) {
                return $service->price * $quantity;
            }
        }

        // For special items
        if ($orderItem->item_type === 'special_item') {
            // Try inventory_id first (most reliable)
            if ($orderItem->inventory_id && $orderItem->inventory) {
                $price = $orderItem->inventory->selling_price > 0
                    ? $orderItem->inventory->selling_price
                    : $orderItem->inventory->unit_price;
                if ($price > 0) {
                    return $price * $quantity;
                }
            }
            // Fallback: look up by name in SpecialItem
            $specialItem = \App\Models\SpecialItem::where('name', $orderItem->item_name)->first();
            if ($specialItem && $specialItem->unit_price > 0) {
                return $specialItem->unit_price * $quantity;
            }
            // Fallback: look up by name in MedicineGroup
            $medicineGroup = \App\Models\MedicineGroup::where('name', $orderItem->item_name)->first();
            if ($medicineGroup) {
                $price = (float) $medicineGroup->total_price;
                if ($price > 0) {
                    return $price * $quantity;
                }
            }
        }

        // Fallback: if no price found, return 0
        return 0;
    }

    /**
     * Get detailed breakdown of order costs grouped by panels/services
     */
    public function getOrderCostBreakdown(MedicalOrder $medicalOrder): array
    {
        $groups = [];
        $total = 0;

        // Load lab panels for grouping
        $labPanels = \App\Models\LabPanel::where('is_active', true)
            ->with(['labPanelItems.inventory'])
            ->get()
            ->keyBy('id');

        foreach ($medicalOrder->orderItems as $orderItem) {
            $itemTotal = $this->calculateItemTotal($orderItem);
            $quantity = $orderItem->quantity_required ?? 1;
            $unitPrice = $itemTotal / $quantity;

            $groupKey = $orderItem->item_type;
            $groupName = $this->getGroupDisplayName($orderItem->item_type);
            $panelName = null;

            // For lab items, check if they belong to a panel
            if ($orderItem->item_type === 'lab' && $orderItem->inventory_id) {
                foreach ($labPanels as $panel) {
                    foreach ($panel->labPanelItems as $panelItem) {
                        if ($panelItem->inventory_id === $orderItem->inventory_id) {
                            $groupKey = "lab-{$panel->id}";
                            $groupName = $panel->name;
                            $panelName = $panel->name;
                            break 2; // Break out of both loops
                        }
                    }
                }
            }

            // Initialize group if it doesn't exist
            if (! isset($groups[$groupKey])) {
                $groups[$groupKey] = [
                    'name' => $groupName,
                    'type' => $orderItem->item_type,
                    'panel_name' => $panelName,
                    'items' => [],
                    'subtotal' => 0,
                    'item_count' => 0,
                ];
            }

            // Add item to group
            $groups[$groupKey]['items'][] = [
                'id' => $orderItem->id,
                'item_name' => $orderItem->item_name,
                'item_type' => $orderItem->item_type,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total' => $itemTotal,
                'details' => $orderItem->details,
                'is_package_included' => $orderItem->isPackageIncluded(),
            ];

            $groups[$groupKey]['subtotal'] += $itemTotal;
            $groups[$groupKey]['item_count']++;
            $total += $itemTotal;
        }

        return [
            'groups' => array_values($groups),
            'total' => $total,
        ];
    }

    /**
     * Get display name for item type groups
     */
    private function getGroupDisplayName(string $itemType): string
    {
        return match ($itemType) {
            'lab' => 'Lab Tests',
            'rx_medicine' => 'Medicines',
            'procedure' => 'Procedures',
            'imaging' => 'Imaging',
            'supply' => 'Supplies',
            'therapy' => 'Therapy',
            'consultation' => 'Consultations',
            'special_item' => 'Special Items',
            default => ucfirst($itemType),
        };
    }

    /**
     * Process a medical order and create billing
     */
    public function processOrderAndCreateBilling(MedicalOrder $medicalOrder, ?string $notes = null): Billing
    {
        return DB::transaction(function () use ($medicalOrder, $notes) {
            // Load necessary relationships
            $medicalOrder->load(['visit.appointment', 'orderItems.inventory']);

            // Check if order can be processed
            $canProcess = $this->canProcessOrder($medicalOrder);
            if (! $canProcess['can_process']) {
                throw new \Exception('Cannot process order: '.implode(', ', $canProcess['issues']));
            }

            // Calculate total amount
            $totalAmount = $this->calculateOrderTotal($medicalOrder);

            // Update order status to completed
            $medicalOrder->update([
                'status' => MedicalOrderStatusEnum::COMPLETED,
                'completed_at' => now(),
            ]);

            // Update individual order items status
            $medicalOrder->orderItems()->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            // Reduce inventory stock for used items
            $this->reduceInventoryStock($medicalOrder);

            // Get patient_id from medical order or fallback to visit's patient_id
            $patientId = $medicalOrder->patient_id ?? $medicalOrder->visit?->patient_id;

            // Create billing record
            $billing = Billing::create([
                'patient_id' => $patientId,
                'appointment_id' => $medicalOrder->visit?->appointment_id,
                'visit_id' => $medicalOrder->visit_id,
                'medical_order_id' => $medicalOrder->id,
                'amount' => $totalAmount,
                'status' => \App\Enums\BillingStatusEnum::SENT_TO_ACCOUNT->value,
                'billing_date' => now()->toDateString(),
                'notes' => $notes ?? "Medical Order #{$medicalOrder->id} - {$medicalOrder->order_details}",
            ]);

            return $billing;
        });
    }

    /**
     * Reduce inventory stock for items used in the order
     */
    public function reduceInventoryStock(MedicalOrder $medicalOrder): void
    {
        $inventoryService = app(InventoryService::class);

        foreach ($medicalOrder->orderItems as $orderItem) {
            // Only reduce stock for inventory-based items (not services)
            if ($orderItem->inventory_id && in_array($orderItem->item_type, ['lab', 'rx_medicine', 'supply'])) {
                $inventory = $orderItem->inventory;
                $quantity = $orderItem->quantity_required ?? 1;

                if ($inventory && $inventory->quantity >= $quantity) {
                    $inventoryService->removeStock(
                        $inventory,
                        $quantity,
                        "Used in Medical Order #{$medicalOrder->id}"
                    );
                }
            }
        }
    }

    /**
     * Restore inventory stock for all items in a medical order (used when sending back for revision).
     */
    public function restoreInventoryStock(MedicalOrder $medicalOrder): void
    {
        $inventoryService = app(InventoryService::class);

        foreach ($medicalOrder->orderItems as $orderItem) {
            if ($orderItem->inventory_id && in_array($orderItem->item_type, ['lab', 'rx_medicine', 'supply'])) {
                $inventory = $orderItem->inventory;
                $quantity = $orderItem->quantity_required ?? 1;

                if ($inventory) {
                    $inventoryService->addStock(
                        $inventory,
                        $quantity,
                        "Restored from Medical Order #{$medicalOrder->id} (billing revision)"
                    );
                }
            }
        }
    }

    /**
     * Check if order can be processed (sufficient inventory)
     */
    public function canProcessOrder(MedicalOrder $medicalOrder): array
    {
        $issues = [];

        foreach ($medicalOrder->orderItems as $orderItem) {
            if ($orderItem->inventory_id && in_array($orderItem->item_type, ['lab', 'rx_medicine', 'supply'])) {
                $inventory = $orderItem->inventory;
                $quantity = $orderItem->quantity_required ?? 1;

                if (! $inventory) {
                    $issues[] = "Inventory item not found for: {$orderItem->item_name}";
                } elseif ($inventory->quantity < $quantity) {
                    $issues[] = "Insufficient stock for {$orderItem->item_name}. Available: {$inventory->quantity}, Required: {$quantity}";
                }
            }
        }

        return [
            'can_process' => empty($issues),
            'issues' => $issues,
        ];
    }

    /**
     * Get inventory usage summary for a medical order
     */
    public function getInventoryUsageSummary(MedicalOrder $medicalOrder): Collection
    {
        return $medicalOrder->orderItems
            ->filter(function ($item) {
                return $item->inventory_id && in_array($item->item_type, ['lab', 'rx_medicine', 'supply']);
            })
            ->map(function ($item) {
                $inventory = $item->inventory;
                $quantity = $item->quantity_required ?? 1;

                return [
                    'inventory_id' => $item->inventory_id,
                    'item_name' => $item->item_name,
                    'item_type' => $item->item_type,
                    'quantity_used' => $quantity,
                    'current_stock' => $inventory ? $inventory->quantity : 0,
                    'remaining_stock' => $inventory ? $inventory->quantity - $quantity : 0,
                    'unit' => $inventory ? $inventory->unit : null,
                ];
            });
    }

    /**
     * Cancel a processed order and restore inventory
     */
    public function cancelProcessedOrder(MedicalOrder $medicalOrder, ?string $reason = null): bool
    {
        return DB::transaction(function () use ($medicalOrder, $reason) {
            // Only allow cancellation if order is completed
            if ($medicalOrder->status !== 'completed') {
                return false;
            }

            $inventoryService = app(InventoryService::class);

            // Restore inventory stock
            foreach ($medicalOrder->orderItems as $orderItem) {
                if ($orderItem->inventory_id && in_array($orderItem->item_type, ['lab', 'rx_medicine', 'supply'])) {
                    $inventory = $orderItem->inventory;
                    $quantity = $orderItem->quantity_required ?? 1;

                    if ($inventory) {
                        $inventoryService->addStock(
                            $inventory,
                            $quantity,
                            "Restored from cancelled Medical Order #{$medicalOrder->id}"
                        );
                    }
                }
            }

            // Update order status
            $medicalOrder->update([
                'status' => 'cancelled',
                'notes' => ($medicalOrder->notes ? $medicalOrder->notes."\n" : '').
                    'Cancelled: '.($reason ?? 'No reason provided').' - '.now()->format('Y-m-d H:i'),
            ]);

            // Update order items status
            $medicalOrder->orderItems()->update([
                'status' => 'cancelled',
            ]);

            // Cancel related billing if exists
            $billing = Billing::where('medical_order_id', $medicalOrder->id)
                ->where('status', 'pending')
                ->first();

            if ($billing) {
                $billing->update([
                    'status' => 'cancelled',
                    'notes' => ($billing->notes ? $billing->notes."\n" : '').
                        'Cancelled due to order cancellation: '.($reason ?? 'No reason provided'),
                ]);
            }

            return true;
        });
    }

    /**
     * Generate medical record data when billing is paid
     */
    public function generateMedicalRecordOnPayment(Billing $billing): ?MedicalRecord
    {
        // Only generate record if billing has a medical order and is being paid
        if (! $billing->medicalOrder || $billing->status !== 'paid') {
            return null;
        }

        $medicalOrder = $billing->medicalOrder;

        // Check if medical record already exists for this billing/medical order
        $existingRecord = MedicalRecord::where('medical_order_id', $medicalOrder->id)->first();
        if ($existingRecord) {
            return $existingRecord;
        }

        // Generate diagnosis from visit/appointment notes or order details
        $diagnosis = $this->generateDiagnosis($medicalOrder);

        // Generate treatment summary from order items
        $treatment = $this->generateTreatmentSummary($medicalOrder);

        // Generate notes combining order details and billing info
        $notes = $this->generateMedicalNotes($medicalOrder, $billing);

        // Create medical record
        return MedicalRecord::create([
            'appointment_id' => $billing->appointment_id,
            'visit_id' => $billing->visit_id,
            'medical_order_id' => $medicalOrder->id,
            'diagnosis' => $diagnosis,
            'treatment' => $treatment,
            'notes' => $notes,
            'date_of_service' => $medicalOrder->completed_at?->toDateString() ?? now()->toDateString(),
        ]);
    }

    /**
     * Generate diagnosis information from medical order context
     */
    private function generateDiagnosis(MedicalOrder $medicalOrder): string
    {
        $diagnosisParts = [];

        // Try to get diagnosis from visit notes
        if ($medicalOrder->visit && $medicalOrder->visit->notes) {
            $diagnosisParts[] = "Visit Notes: {$medicalOrder->visit->notes}";
        }

        // Try to get diagnosis from appointment notes
        if ($medicalOrder->visit?->appointment && $medicalOrder->visit->appointment->notes) {
            $diagnosisParts[] = "Appointment Notes: {$medicalOrder->visit->appointment->notes}";
        }

        // Extract diagnosis from order details if it contains diagnostic information
        if ($medicalOrder->order_details) {
            $diagnosisParts[] = "Order Details: {$medicalOrder->order_details}";
        }

        // If no specific diagnosis found, generate based on ordered items
        if (empty($diagnosisParts)) {
            $itemTypes = $medicalOrder->orderItems->pluck('item_type')->unique();
            $diagnosisParts[] = 'Diagnostic workup including: '.implode(', ', $itemTypes->toArray());
        }

        return implode('; ', $diagnosisParts);
    }

    /**
     * Generate treatment summary from medical order items
     */
    private function generateTreatmentSummary(MedicalOrder $medicalOrder): string
    {
        $treatmentParts = [];

        // Group items by type for better organization
        $groupedItems = $medicalOrder->orderItems->groupBy('item_type');

        foreach ($groupedItems as $itemType => $items) {
            $typeLabel = $this->getTreatmentTypeLabel($itemType);
            $itemDescriptions = [];

            foreach ($items as $item) {
                $description = $item->item_name;

                // Add dosage/frequency/route for medicines
                if ($itemType === 'rx_medicine' && ($item->dosage || $item->frequency || $item->route)) {
                    $medicationDetails = [];
                    if ($item->dosage) {
                        $medicationDetails[] = $item->dosage;
                    }
                    if ($item->frequency) {
                        $medicationDetails[] = $item->frequency;
                    }
                    if ($item->route) {
                        $medicationDetails[] = "via {$item->route}";
                    }
                    if (! empty($medicationDetails)) {
                        $description .= ' ('.implode(', ', $medicationDetails).')';
                    }
                }

                // Add quantity for relevant items
                if ($item->quantity_required && $item->quantity_required > 1) {
                    $description .= " x{$item->quantity_required}";
                }

                $itemDescriptions[] = $description;
            }

            $treatmentParts[] = "{$typeLabel}: ".implode(', ', $itemDescriptions);
        }

        return implode('; ', $treatmentParts);
    }

    /**
     * Get human-readable label for treatment type
     */
    private function getTreatmentTypeLabel(string $itemType): string
    {
        return match ($itemType) {
            'lab' => 'Laboratory Tests',
            'rx_medicine' => 'Prescribed Medications',
            'procedure' => 'Medical Procedures',
            'imaging' => 'Imaging Studies',
            'supply' => 'Medical Supplies',
            'therapy' => 'Therapy Services',
            'consultation' => 'Consultations',
            default => ucfirst($itemType),
        };
    }

    /**
     * Generate comprehensive medical notes
     */
    private function generateMedicalNotes(MedicalOrder $medicalOrder, Billing $billing): string
    {
        $notes = [];

        // Add order priority if high
        if ($medicalOrder->priority && $medicalOrder->priority->value === 'high') {
            $notes[] = 'High Priority Order';
        }

        // Add completion information
        if ($medicalOrder->completed_at) {
            $notes[] = "Order completed on {$medicalOrder->completed_at->format('M j, Y \a\t g:i A')}";
        }

        // Add billing information
        $notes[] = "Billing Amount: \${$billing->amount}";
        $notes[] = "Payment Status: {$billing->status}";

        // Add any additional order notes
        if ($medicalOrder->notes) {
            $notes[] = "Additional Notes: {$medicalOrder->notes}";
        }

        // Add staff information if available
        if ($medicalOrder->staff) {
            $notes[] = "Ordered by: {$medicalOrder->staff->first_name} {$medicalOrder->staff->last_name}";
        }

        return implode('; ', $notes);
    }
}
