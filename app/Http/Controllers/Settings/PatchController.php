<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePatchRequest;
use App\Http\Requests\UpdatePatchRequest;
use App\Models\Inventory;
use App\Models\Patch;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PatchController extends Controller
{
    public function index(): Response
    {
        $patches = Patch::with('inventories')
            ->latest()
            ->get()
            ->map(fn (Patch $patch) => [
                'id' => $patch->id,
                'name' => $patch->name,
                'custom_price' => $patch->custom_price,
                'total_unit_price' => $patch->total_unit_price,
                'items_count' => $patch->inventories->count(),
                'items' => $patch->inventories->map(fn ($item) => [
                    'id' => $item->id,
                    'item_name' => $item->item_name,
                    'unit_price' => $item->unit_price,
                    'category' => $item->category,
                    'type_of_supply' => $item->type_of_supply,
                    'quantity' => $item->pivot->quantity,
                ]),
            ]);

        return Inertia::render('settings/Patches/Index', [
            'patches' => $patches,
            'inventories' => Inventory::orderBy('item_name')->get(['id', 'item_name', 'unit_price', 'category', 'type_of_supply']),
        ]);
    }

    public function store(StorePatchRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $patch = Patch::create([
            'name' => $data['name'],
            'custom_price' => $data['custom_price'] ?? null,
        ]);

        if (isset($data['inventory_items'])) {
            $syncData = [];
            foreach ($data['inventory_items'] as $item) {
                $syncData[$item['id']] = ['quantity' => $item['quantity'] ?? 1];
            }
            $patch->inventories()->sync($syncData);
        }

        return back()->with('success', 'Package created');
    }

    public function edit(Patch $patch): Response
    {
        $patch->load('inventories');

        return Inertia::render('settings/Patches/Index', [
            'patches' => Patch::with('inventories')
                ->latest()
                ->get()
                ->map(fn (Patch $p) => [
                    'id' => $p->id,
                    'name' => $p->name,
                    'custom_price' => $p->custom_price,
                    'total_unit_price' => $p->total_unit_price,
                    'items_count' => $p->inventories->count(),
                ]),
            'inventories' => Inventory::orderBy('item_name')->get(['id', 'item_name', 'unit_price', 'category', 'type_of_supply']),
            'patch' => [
                'id' => $patch->id,
                'name' => $patch->name,
                'custom_price' => $patch->custom_price,
                'inventory_items' => $patch->inventories->map(fn ($item) => [
                    'id' => $item->id,
                    'quantity' => $item->pivot->quantity ?? 1,
                ])->toArray(),
            ],
        ]);
    }

    public function update(UpdatePatchRequest $request, Patch $patch): RedirectResponse
    {
        $data = $request->validated();

        $patch->update([
            'name' => $data['name'],
            'custom_price' => $data['custom_price'] ?? null,
        ]);

        if (isset($data['inventory_items'])) {
            $syncData = [];
            foreach ($data['inventory_items'] as $item) {
                $syncData[$item['id']] = ['quantity' => $item['quantity'] ?? 1];
            }
            $patch->inventories()->sync($syncData);
        }

        return back()->with('success', 'Package updated');
    }

    public function destroy(Patch $patch): RedirectResponse
    {
        $patch->delete();

        return back()->with('success', 'Package deleted');
    }
}
