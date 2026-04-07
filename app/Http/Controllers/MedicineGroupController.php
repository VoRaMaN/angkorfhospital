<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\MedicineGroup;
use App\Models\MedicineGroupItem;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MedicineGroupController extends Controller
{
    public function index(): Response
    {
        $medicineGroups = MedicineGroup::with('items.inventory')
            ->orderBy('name')
            ->get()
            ->map(function ($group) {
                // Calculate total price
                $totalPrice = $group->custom_price;
                if (! $totalPrice) {
                    $totalPrice = $group->items->sum(function ($item) {
                        return ($item->inventory->selling_price ?? 0) * $item->quantity;
                    });
                }

                // Ensure total_price is always a number
                $totalPrice = (float) ($totalPrice ?? 0);

                return [
                    'id' => $group->id,
                    'name' => $group->name,
                    'description' => $group->description,
                    'custom_price' => $group->custom_price ? (float) $group->custom_price : null,
                    'total_price' => $totalPrice,
                    'is_active' => $group->is_active,
                    'item_count' => $group->items->count(),
                    'items' => $group->items->map(function ($item) {
                        return [
                            'medicine_name' => $item->inventory->item_name ?? 'Unknown',
                            'quantity' => $item->quantity,
                            'dosage' => $item->dosage,
                            'frequency' => $item->frequency,
                        ];
                    }),
                ];
            });

        return Inertia::render('MedicineGroups/Index', [
            'medicineGroups' => $medicineGroups,
        ]);
    }

    public function create(): Response
    {
        $rxMedicines = Inventory::where('type_of_supply', \App\Enums\SupplyTypeEnum::RX_MEDICINE)
            ->orderBy('item_name')
            ->get()
            ->map(function ($medicine) {
                return [
                    'id' => $medicine->id,
                    'name' => $medicine->item_name,
                    'unit_price' => $medicine->unit_price,
                    'selling_price' => $medicine->selling_price,
                    'stock_quantity' => $medicine->quantity,
                ];
            });

        return Inertia::render('MedicineGroups/Create', [
            'rxMedicines' => $rxMedicines,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'custom_price' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'items' => 'nullable|array',
            'items.*.inventory_id' => 'required|exists:inventories,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.dosage' => 'nullable|string',
            'items.*.frequency' => 'nullable|string',
        ]);

        $group = MedicineGroup::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'custom_price' => $validated['custom_price'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        foreach ($validated['items'] ?? [] as $item) {
            MedicineGroupItem::create([
                'medicine_group_id' => $group->id,
                'inventory_id' => $item['inventory_id'],
                'quantity' => $item['quantity'],
                'dosage' => $item['dosage'] ?? null,
                'frequency' => $item['frequency'] ?? null,
            ]);
        }

        return redirect()->route('medicine-groups.index')
            ->with('success', 'Special item created successfully');
    }

    public function edit(MedicineGroup $medicineGroup): Response
    {
        $medicineGroup->load('items.inventory');

        $rxMedicines = Inventory::where('type_of_supply', 'rx_medicine')
            ->orderBy('item_name')
            ->get()
            ->map(function ($medicine) {
                return [
                    'id' => $medicine->id,
                    'name' => $medicine->item_name,
                    'unit_price' => $medicine->unit_price,
                    'selling_price' => $medicine->selling_price,
                    'stock_quantity' => $medicine->quantity,
                ];
            });

        return Inertia::render('MedicineGroups/Edit', [
            'medicineGroup' => [
                'id' => $medicineGroup->id,
                'name' => $medicineGroup->name,
                'description' => $medicineGroup->description,
                'custom_price' => $medicineGroup->custom_price,
                'is_active' => $medicineGroup->is_active,
                'items' => $medicineGroup->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'inventory_id' => $item->inventory_id,
                        'medicine_name' => $item->inventory->item_name ?? 'Unknown',
                        'quantity' => $item->quantity,
                        'dosage' => $item->dosage,
                        'frequency' => $item->frequency,
                    ];
                }),
            ],
            'rxMedicines' => $rxMedicines,
        ]);
    }

    public function update(Request $request, MedicineGroup $medicineGroup)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'custom_price' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'items' => 'nullable|array',
            'items.*.inventory_id' => 'required|exists:inventories,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.dosage' => 'nullable|string',
            'items.*.frequency' => 'nullable|string',
        ]);

        $medicineGroup->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'custom_price' => $validated['custom_price'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        // Delete old items
        $medicineGroup->items()->delete();

        // Create new items
        foreach ($validated['items'] ?? [] as $item) {
            MedicineGroupItem::create([
                'medicine_group_id' => $medicineGroup->id,
                'inventory_id' => $item['inventory_id'],
                'quantity' => $item['quantity'],
                'dosage' => $item['dosage'] ?? null,
                'frequency' => $item['frequency'] ?? null,
            ]);
        }

        return redirect()->route('medicine-groups.index')
            ->with('success', 'Special item updated successfully');
    }

    public function destroy(MedicineGroup $medicineGroup)
    {
        $medicineGroup->delete();

        return redirect()->route('medicine-groups.index')
            ->with('success', 'Special item deleted successfully');
    }
}
