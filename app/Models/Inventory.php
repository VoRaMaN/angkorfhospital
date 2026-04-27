<?php

namespace App\Models;

use App\Enums\SupplyTypeEnum;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    /** @use HasFactory<\Database\Factories\InventoryFactory> */
    use HasFactory, LogsActivity;

    protected $fillable = [
        'item_name',
        'description',
        'category',
        'barcode',
        'type_of_supply',
        'quantity',
        'original_quantity',
        'unit',
        'dose_unit',
        'total_per_box',
        'minimum_stock',
        'unit_price',
        'selling_price',
        'supplier',
        'location',
        'expiry_date',
        'alert_days',
        'notes',
    ];

    protected $appends = [
        'status',
    ];

    protected function casts(): array
    {
        return [
            'expiry_date' => 'datetime',
            'unit_price' => 'decimal:2',
            'selling_price' => 'decimal:2',
            'type_of_supply' => \App\Enums\SupplyTypeEnum::class,
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $inventory): void {
            if (is_null($inventory->original_quantity)) {
                $inventory->original_quantity = $inventory->quantity;
            }
        });
    }

    /**
     * Check if the item is low on stock
     */
    public function isLowStock(): bool
    {
        return $this->quantity <= $this->minimum_stock;
    }

    /**
     * Get stock status
     */
    public function getStockStatus(): string
    {
        if ($this->quantity <= 0) {
            return 'Out of Stock';
        }

        if ($this->isLowStock()) {
            return 'Low Stock';
        }

        return 'In Stock';
    }

    /**
     * Get status attribute
     */
    public function getStatusAttribute(): string
    {
        return $this->getStockStatus();
    }

    /**
     * Check if item is expired or expiring soon
     */
    public function isExpiringSoon(int $days = 30): bool
    {
        if (! $this->expiry_date) {
            return false;
        }

        return $this->expiry_date->diffInDays(now()) <= $days && $this->expiry_date->isFuture();
    }

    /**
     * Check if item is expired
     */
    public function isExpired(): bool
    {
        if (! $this->expiry_date) {
            return false;
        }

        return $this->expiry_date->isPast();
    }

    /**
     * Scope to filter by type of supply
     */
    public function scopeByTypeOfSupply($query, SupplyTypeEnum $type)
    {
        return $query->where('type_of_supply', $type);
    }

    /**
     * Scope to get low stock items
     */
    public function scopeLowStock($query)
    {
        return $query->whereRaw('quantity <= minimum_stock');
    }

    /**
     * Scope to get out of stock items
     */
    public function scopeOutOfStock($query)
    {
        return $query->where('quantity', '<=', 0);
    }

    /**
     * Scope to get expiring items
     */
    public function scopeExpiringSoon($query, int $days = 30)
    {
        return $query->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '>', now())
            ->whereDate('expiry_date', '<=', now()->addDays($days));
    }

    /**
     * Scope to get expired items
     */
    public function scopeExpired($query)
    {
        return $query->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<', now());
    }

    /**
     * Get lab panels that include this inventory item
     */
    public function labPanelItems()
    {
        return $this->hasMany(LabPanelItem::class);
    }
}
