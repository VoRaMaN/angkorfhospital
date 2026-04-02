<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\SpecialItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SpecialItemController extends Controller
{
    public function index(): Response
    {
        $specialItems = SpecialItem::with('items.inventory')
            ->orderBy('name')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'description' => $item->description,
                    'unit_price' => (float) $item->unit_price,
                    'is_active' => $item->is_active,
                    'item_count' => $item->items->count(),
                    'items' => $item->items->map(function ($subItem) {
                        return [
                            'item_name' => $subItem->inventory->item_name ?? 'Unknown',
                            'quantity' => $subItem->quantity,
                        ];
                    }),
                ];
            });

        return Inertia::render('SpecialItems/Index', [
            'specialItems' => $specialItems,
        ]);
    }

    public function create(): Response
    {
        $inventoryItems = Inventory::orderBy('item_name')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->item_name,
                    'type' => $item->type_of_supply->value,
                    'unit' => $item->unit,
                ];
            });

        return Inertia::render('SpecialItems/Create', [
            'inventoryItems' => $inventoryItems,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'unit_price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
            'items' => 'nullable|array',
            'items.*.inventory_id' => 'required|exists:inventories,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $specialItem = SpecialItem::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'unit_price' => $validated['unit_price'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        // Add items if provided
        if (! empty($validated['items'])) {
            foreach ($validated['items'] as $item) {
                $specialItem->items()->create([
                    'inventory_id' => $item['inventory_id'],
                    'quantity' => $item['quantity'],
                ]);
            }
        }

        return redirect()->route('special-items.index')->with('success', 'Special item created successfully.');
    }

    public function edit(SpecialItem $specialItem): Response
    {
        $specialItem->load('items.inventory');

        $inventoryItems = Inventory::orderBy('item_name')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->item_name,
                    'type' => $item->type_of_supply->value,
                    'unit' => $item->unit,
                ];
            });

        $transformedItem = [
            'id' => $specialItem->id,
            'name' => $specialItem->name,
            'description' => $specialItem->description,
            'unit_price' => (float) $specialItem->unit_price,
            'is_active' => $specialItem->is_active,
            'items' => $specialItem->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'inventory_id' => $item->inventory_id,
                    'inventory_name' => $item->inventory->item_name ?? 'Unknown',
                    'quantity' => $item->quantity,
                ];
            }),
        ];

        return Inertia::render('SpecialItems/Edit', [
            'specialItem' => $transformedItem,
            'inventoryItems' => $inventoryItems,
        ]);
    }

    public function update(Request $request, SpecialItem $specialItem): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'unit_price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
            'items' => 'nullable|array',
            'items.*.inventory_id' => 'required|exists:inventories,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $specialItem->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'unit_price' => $validated['unit_price'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        // Delete existing items and recreate
        $specialItem->items()->delete();

        if (! empty($validated['items'])) {
            foreach ($validated['items'] as $item) {
                $specialItem->items()->create([
                    'inventory_id' => $item['inventory_id'],
                    'quantity' => $item['quantity'],
                ]);
            }
        }

        return redirect()->route('special-items.index')->with('success', 'Special item updated successfully.');
    }

    public function destroy(SpecialItem $specialItem): RedirectResponse
    {
        $specialItem->delete();

        return redirect()->route('special-items.index')->with('success', 'Special item deleted successfully.');
    }
}
