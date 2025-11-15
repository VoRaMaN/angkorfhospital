<?php

namespace App\Services;

use App\Models\Inventory;
use Illuminate\Support\Collection;

class InventoryService
{
    /**
     * Get all low stock items
     */
    public function getLowStockItems(): Collection
    {
        return Inventory::lowStock()
            ->orderBy('quantity', 'asc')
            ->get();
    }

    /**
     * Get items expiring soon
     */
    public function getExpiringSoonItems(int $days = 30): Collection
    {
        return Inventory::expiringSoon($days)
            ->orderBy('expiry_date', 'asc')
            ->get();
    }

    /**
     * Get expired items
     */
    public function getExpiredItems(): Collection
    {
        return Inventory::expired()
            ->orderBy('expiry_date', 'asc')
            ->get();
    }

    /**
     * Get stock levels by type of supply
     */
    public function getStockLevelsByTypeOfSupply(\App\Enums\SupplyTypeEnum $type): Collection
    {
        return Inventory::byTypeOfSupply($type)
            ->orderBy('item_name')
            ->get()
            ->map(function ($inventory) {
                return [
                    'id' => $inventory->id,
                    'name' => $inventory->item_name,
                    'quantity' => $inventory->quantity,
                    'unit' => $inventory->unit,
                    'minimum_stock' => $inventory->minimum_stock,
                    'status' => $inventory->getStockStatus(),
                    'location' => $inventory->location,
                ];
            });
    }

    /**
     * Add stock to an inventory item
     */
    public function addStock(Inventory $inventory, int $quantity, ?string $notes = null): Inventory
    {
        $inventory->quantity += $quantity;

        if ($notes) {
            $inventory->notes = ($inventory->notes ? $inventory->notes."\n" : '').
                               now()->format('Y-m-d H:i').": Added {$quantity} {$inventory->unit}. {$notes}";
        }

        $inventory->save();

        return $inventory->fresh();
    }

    /**
     * Remove stock from an inventory item
     */
    public function removeStock(Inventory $inventory, int $quantity, ?string $notes = null): Inventory
    {
        if ($inventory->quantity < $quantity) {
            throw new \Exception("Insufficient stock. Available: {$inventory->quantity}, Requested: {$quantity}");
        }

        $inventory->quantity -= $quantity;

        if ($notes) {
            $inventory->notes = ($inventory->notes ? $inventory->notes."\n" : '').
                               now()->format('Y-m-d H:i').": Removed {$quantity} {$inventory->unit}. {$notes}";
        }

        $inventory->save();

        return $inventory->fresh();
    }

    /**
     * Create reorder notification/request
     */
    public function createReorderRequest(Inventory $inventory, int $quantity): array
    {
        return [
            'inventory_id' => $inventory->id,
            'item_name' => $inventory->item_name,
            'category' => $inventory->category,
            'current_quantity' => $inventory->quantity,
            'minimum_stock' => $inventory->minimum_stock,
            'reorder_quantity' => $quantity,
            'supplier' => $inventory->supplier,
            'unit_price' => $inventory->unit_price,
            'estimated_cost' => $inventory->unit_price ? $inventory->unit_price * $quantity : null,
            'created_at' => now()->toISOString(),
        ];
    }

    /**
     * Get inventory statistics
     */
    public function getInventoryStatistics(): array
    {
        $totalItems = Inventory::count();
        $lowStockItems = Inventory::lowStock()->count();
        $outOfStockItems = Inventory::outOfStock()->count();
        $expiringSoonItems = Inventory::expiringSoon(30)->count();
        $expiredItems = Inventory::expired()->count();

        $totalValue = Inventory::whereNotNull('unit_price')
            ->get()
            ->sum(fn ($item) => $item->quantity * $item->unit_price);

        // Get type of supply counts
        $typeOfSupplyCounts = Inventory::selectRaw('type_of_supply, count(*) as count')
            ->groupBy('type_of_supply')
            ->pluck('count', 'type_of_supply')
            ->toArray();

        return [
            'total_items' => $totalItems,
            'low_stock_items' => $lowStockItems,
            'out_of_stock_items' => $outOfStockItems,
            'expiring_soon_items' => $expiringSoonItems,
            'expired_items' => $expiredItems,
            'total_inventory_value' => $totalValue,
            'type_of_supply_counts' => $typeOfSupplyCounts,
        ];
    }
}
