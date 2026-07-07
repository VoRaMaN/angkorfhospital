<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInventoryRequest;
use App\Http\Requests\UpdateInventoryRequest;
use App\Models\Inventory;
use App\Traits\RendersExportHtml;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class InventoryController extends Controller
{
    use RendersExportHtml;

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

    /**
     * Dropdown options for the create/edit forms. Plastic Ware and Culture
     * Medium are not real enum types — they are stored as lab_supply plus a
     * category (see plasticWare()/cultureMedium() pages); the form requests
     * map these pseudo-values back to that shape on submit.
     */
    public const LAB_CATEGORY_TYPES = [
        'plastic_ware' => 'Plastic Ware',
        'culture_medium' => 'Culture Medium',
    ];

    private function formTypesOfSupply(): array
    {
        return array_merge(\App\Enums\SupplyTypeEnum::options(), self::LAB_CATEGORY_TYPES);
    }

    public function create(): Response
    {
        $this->authorize('create', Inventory::class);

        return Inertia::render('Inventories/Create', [
            'typesOfSupply' => $this->formTypesOfSupply(),
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

        // Show lab_supply items categorized as Plastic Ware / Culture Medium
        // under their pseudo type so the dropdown round-trips.
        $item = $inventory->toArray();
        $pseudo = array_search($inventory->category, self::LAB_CATEGORY_TYPES, true);
        if ($inventory->type_of_supply === \App\Enums\SupplyTypeEnum::LAB_SUPPLY && $pseudo !== false) {
            $item['type_of_supply'] = $pseudo;
        }

        return Inertia::render('Inventories/Edit', [
            'item' => $item,
            'typesOfSupply' => $this->formTypesOfSupply(),
        ]);
    }

    public function update(UpdateInventoryRequest $request, Inventory $inventory): RedirectResponse
    {
        $inventory->update($request->validated());

        return redirect()->back()->with('success', 'Inventory record updated successfully.');
    }

    public function destroy(Inventory $inventory): RedirectResponse
    {
        $this->authorize('delete', $inventory);
        $inventory->delete();

        return redirect()->back()->with('success', 'Inventory record deleted successfully.');
    }

    public function rxMedicine(): Response
    {
        $this->authorize('viewAny', Inventory::class);

        $search = request('search', '');
        $status = request('status', '');
        $dateFrom = request('date_from', '');
        $dateTo = request('date_to', '');

        $base = Inventory::where('type_of_supply', \App\Enums\SupplyTypeEnum::RX_MEDICINE);

        // Status counts for alert buttons
        $expiredCount = (clone $base)->whereNotNull('expiry_date')->whereDate('expiry_date', '<', now()->toDateString())->count();
        $lowStockCount = (clone $base)->whereRaw('quantity > 0')->whereRaw('quantity <= minimum_stock')->count();
        $outOfStockCount = (clone $base)->where('quantity', '<=', 0)->count();

        $query = (clone $base)
            ->when($search, fn ($q, $s) => $q->where('item_name', 'like', "%{$s}%"))
            ->when($status === 'expired', fn ($q) => $q->whereNotNull('expiry_date')->whereDate('expiry_date', '<', now()->toDateString()))
            ->when($status === 'low_stock', fn ($q) => $q->whereRaw('quantity > 0')->whereRaw('quantity <= minimum_stock'))
            ->when($status === 'out_of_stock', fn ($q) => $q->where('quantity', '<=', 0))
            ->when($dateFrom, fn ($q) => $q->whereNotNull('expiry_date')->whereDate('expiry_date', '>=', $dateFrom))
            ->when($dateTo, fn ($q) => $q->whereNotNull('expiry_date')->whereDate('expiry_date', '<=', $dateTo))
            ->orderBy('item_name');

        return Inertia::render('Inventories/RXMedicineIndex', [
            'rxMedicines' => $query->get(),
            'filters' => [
                'search' => $search,
                'status' => $status,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
            ],
            'counts' => [
                'expired' => $expiredCount,
                'low_stock' => $lowStockCount,
                'out_of_stock' => $outOfStockCount,
            ],
        ]);
    }

    public function rxMedicineExport(): \Illuminate\Http\Response
    {
        $this->authorize('viewAny', Inventory::class);

        $search = request('search', '');
        $status = request('status', '');
        $dateFrom = request('date_from', '');
        $dateTo = request('date_to', '');

        $items = Inventory::where('type_of_supply', \App\Enums\SupplyTypeEnum::RX_MEDICINE)
            ->when($search, fn ($q, $s) => $q->where('item_name', 'like', "%{$s}%"))
            ->when($status === 'expired', fn ($q) => $q->whereNotNull('expiry_date')->whereDate('expiry_date', '<', now()->toDateString()))
            ->when($status === 'low_stock', fn ($q) => $q->whereRaw('quantity > 0')->whereRaw('quantity <= minimum_stock'))
            ->when($status === 'out_of_stock', fn ($q) => $q->where('quantity', '<=', 0))
            ->when($dateFrom, fn ($q) => $q->whereNotNull('expiry_date')->whereDate('expiry_date', '>=', $dateFrom))
            ->when($dateTo, fn ($q) => $q->whereNotNull('expiry_date')->whereDate('expiry_date', '<=', $dateTo))
            ->orderBy('item_name')
            ->get();

        $headers = ['Item Name', 'Description', 'Original Qty', 'Remaining', 'Unit', 'Unit Price', 'Selling Price', 'Expiry Date', 'Status'];
        $rows = $items->map(fn ($item) => [
            $item->item_name,
            $item->description ?? '',
            $item->original_quantity ?? $item->quantity,
            $item->quantity,
            $item->unit,
            number_format((float) $item->unit_price, 2),
            number_format((float) $item->selling_price, 2),
            $item->expiry_date ? $item->expiry_date->format('d/M/y') : '',
            $item->status,
        ])->toArray();

        $filenameParts = ['rx-medicine-report'];
        if ($dateFrom) {
            $filenameParts[] = 'from-'.$dateFrom;
        }
        if ($dateTo) {
            $filenameParts[] = 'to-'.$dateTo;
        }
        if (! $dateFrom && ! $dateTo) {
            $filenameParts[] = now()->format('Y-m-d');
        }

        $filename = implode('-', $filenameParts).'.csv';

        return $this->renderExportHtml('RX Medicine Export', $headers, $rows, $this->buildCsvString($headers, $rows), $filename);
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
