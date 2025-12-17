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
                'total_unit_price' => $patch->total_unit_price,
                'items_count' => $patch->inventories->count(),
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
        ]);

        if (isset($data['inventory_ids'])) {
            $patch->inventories()->sync($data['inventory_ids']);
        }

        return back()->with('success', 'Patch created');
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
                    'total_unit_price' => $p->total_unit_price,
                    'items_count' => $p->inventories->count(),
                ]),
            'inventories' => Inventory::orderBy('item_name')->get(['id', 'item_name', 'unit_price', 'category', 'type_of_supply']),
            'patch' => [
                'id' => $patch->id,
                'name' => $patch->name,
                'inventory_ids' => $patch->inventories->pluck('id')->toArray(),
            ],
        ]);
    }

    public function update(UpdatePatchRequest $request, Patch $patch): RedirectResponse
    {
        $data = $request->validated();

        $patch->update([
            'name' => $data['name'],
        ]);

        if (isset($data['inventory_ids'])) {
            $patch->inventories()->sync($data['inventory_ids']);
        }

        return back()->with('success', 'Patch updated');
    }

    public function destroy(Patch $patch): RedirectResponse
    {
        $patch->delete();

        return back()->with('success', 'Patch deleted');
    }
}
