<?php

namespace App\Services;

use App\Models\Billing;
use App\Models\Inventory;
use App\Models\MedicalOrder;
use App\Models\MedicalOrderInventory;
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
        $quantity = $orderItem->quantity_required ?? 1;

        // For inventory items (lab tests, medicines, supplies)
        if ($orderItem->inventory_id) {
            $inventory = $orderItem->inventory;
            if ($inventory) {
                return $inventory->selling_price * $quantity;
            }
        }

        // For medical services (procedures, imaging)
        if (in_array($orderItem->item_type, ['procedure', 'imaging'])) {
            $service = MedicalService::where('name', $orderItem->item_name)
                ->where('type', $orderItem->item_type)
                ->first();

            if ($service) {
                return $service->price * $quantity;
            }
        }

        // Fallback: if no price found, return 0
        return 0;
    }

    /**
     * Get detailed breakdown of order costs
     */
    public function getOrderCostBreakdown(MedicalOrder $medicalOrder): array
    {
        $breakdown = [
            'items' => [],
            'subtotal' => 0,
            'total' => 0,
        ];

        foreach ($medicalOrder->orderItems as $orderItem) {
            $itemTotal = $this->calculateItemTotal($orderItem);
            $quantity = $orderItem->quantity_required ?? 1;

            $breakdown['items'][] = [
                'id' => $orderItem->id,
                'item_name' => $orderItem->item_name,
                'item_type' => $orderItem->item_type,
                'quantity' => $quantity,
                'unit_price' => $itemTotal / $quantity,
                'total' => $itemTotal,
                'details' => $orderItem->details,
            ];

            $breakdown['subtotal'] += $itemTotal;
        }

        $breakdown['total'] = $breakdown['subtotal'];

        return $breakdown;
    }

    /**
     * Process a medical order and create billing
     */
    public function processOrderAndCreateBilling(MedicalOrder $medicalOrder, ?string $notes = null): Billing
    {
        return DB::transaction(function () use ($medicalOrder, $notes) {
            // Calculate total amount
            $totalAmount = $this->calculateOrderTotal($medicalOrder);

            // Update order status to completed
            $medicalOrder->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            // Update individual order items status
            $medicalOrder->orderItems()->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            // Reduce inventory stock for used items
            $this->reduceInventoryStock($medicalOrder);

            // Create billing record
            $billing = Billing::create([
                'appointment_id' => $medicalOrder->visit?->appointment_id,
                'amount' => $totalAmount,
                'status' => 'pending', // Can be 'pending', 'paid', 'cancelled', etc.
                'billing_date' => now()->toDateString(),
                'notes' => $notes ?? "Medical Order #{$medicalOrder->id} - {$medicalOrder->order_details}",
            ]);

            return $billing;
        });
    }

    /**
     * Reduce inventory stock for items used in the order
     */
    protected function reduceInventoryStock(MedicalOrder $medicalOrder): void
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
            $billing = Billing::where('appointment_id', $medicalOrder->visit?->appointment_id)
                ->where('notes', 'like', "%Medical Order #{$medicalOrder->id}%")
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
}
