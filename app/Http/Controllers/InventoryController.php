<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInventoryRequest;
use App\Http\Requests\UpdateInventoryRequest;
use App\Models\Inventory;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class InventoryController extends Controller
{
    public function index(): Response
    {
        $this->authorize('viewAny', Inventory::class);

        $query = Inventory::query();

        // Search functionality
        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('item_name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('type_of_supply', 'like', "%{$search}%");
            });
        }

        // Filter by type of supply
        if ($typeOfSupply = request('type_of_supply')) {
            $query->where('type_of_supply', $typeOfSupply);
        }

        // Filter by stock status
        if ($status = request('status')) {
            if ($status === 'low_stock') {
                $query->lowStock();
            } elseif ($status === 'out_of_stock') {
                $query->outOfStock();
            } elseif ($status === 'in_stock') {
                $query->whereRaw('quantity > minimum_stock');
            }
        }

        $inventories = $query->latest()->paginate(15);

        // Get unique types of supply for filter
        $typesOfSupply = Inventory::distinct()->pluck('type_of_supply')->map(fn ($type) => [
            'value' => $type->value,
            'label' => $type->label(),
        ])->values();

        return Inertia::render('Inventories/Index', [
            'inventories' => $inventories,
            'typesOfSupply' => $typesOfSupply,
            'filters' => [
                'search' => request('search', ''),
                'type_of_supply' => request('type_of_supply', ''),
                'status' => request('status', ''),
            ],
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Inventory::class);

        return Inertia::render('Inventories/Create', [
            'typesOfSupply' => \App\Enums\SupplyTypeEnum::options(),
        ]);
    }

    public function store(StoreInventoryRequest $request): RedirectResponse
    {
        $inventory = Inventory::create($request->validated());

        return redirect()->route('inventories.index')->with('success', 'Inventory record created successfully.');
    }

    public function show(Inventory $inventory): Response
    {
        $this->authorize('view', $inventory);

        // dd($inventory);
        return Inertia::render('Inventories/Show', [
            'item' => $inventory,
        ]);
    }

    public function edit(Inventory $inventory): Response
    {
        $this->authorize('update', $inventory);

        return Inertia::render('Inventories/Edit', [
            'item' => $inventory,
            'typesOfSupply' => \App\Enums\SupplyTypeEnum::options(),
        ]);
    }

    public function update(UpdateInventoryRequest $request, Inventory $inventory): RedirectResponse
    {
        $inventory->update($request->validated());

        return redirect()->route('inventory.rx-medicine')->with('success', 'Inventory record updated successfully.');
    }

    public function destroy(Inventory $inventory): RedirectResponse
    {
        $this->authorize('delete', $inventory);
        $inventory->delete();

        return redirect()->route('inventory.rx-medicine')->with('success', 'Inventory record deleted successfully.');
    }

    public function rxMedicine(): Response
    {
        $this->authorize('viewAny', Inventory::class);

        $search = request('search', '');

        $rxMedicines = Inventory::where('type_of_supply', \App\Enums\SupplyTypeEnum::RX_MEDICINE)
            ->when($search, function ($query, $search) {
                $query->where('item_name', 'like', "%{$search}%");
            })
            ->orderBy('item_name')
            ->get();

        return Inertia::render('Inventories/RXMedicineIndex', [
            'rxMedicines' => $rxMedicines,
            'filters' => [
                'search' => $search,
                'status' => request('status', ''),
            ],
        ]);
    }

    public function labInventory(): Response
    {
        $this->authorize('viewAny', Inventory::class);

        $labSupplies = Inventory::where('type_of_supply', \App\Enums\SupplyTypeEnum::LAB_SUPPLY)->get();

        return Inertia::render('Inventories/LabInventoryIndex', [
            'labSupplies' => $labSupplies,
            'filters' => [
                'search' => request('search', ''),
                'status' => request('status', ''),
            ],
        ]);
    }

    public function plasticWare(): Response
    {
        $this->authorize('viewAny', Inventory::class);

        $plasticWare = Inventory::where('type_of_supply', \App\Enums\SupplyTypeEnum::LAB_SUPPLY)
            ->where('category', 'Plastic Ware')
            ->get();

        return Inertia::render('Inventories/PlasticWareIndex', [
            'plasticWare' => $plasticWare,
            'filters' => [
                'search' => request('search', ''),
                'status' => request('status', ''),
            ],
        ]);
    }

    public function cultureMedium(): Response
    {
        $this->authorize('viewAny', Inventory::class);

        $cultureMedium = Inventory::where('type_of_supply', \App\Enums\SupplyTypeEnum::LAB_SUPPLY)
            ->where('category', 'Culture Medium')
            ->get();

        return Inertia::render('Inventories/CultureMediumIndex', [
            'cultureMedium' => $cultureMedium,
            'filters' => [
                'search' => request('search', ''),
                'status' => request('status', ''),
            ],
        ]);
    }
}
