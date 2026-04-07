<?php

namespace App\Http\Controllers;

use App\Enums\MedicalOrderStatusEnum;
use App\Models\Inventory;
use App\Models\LabPanel;
use App\Models\LabPanelItem;
use App\Models\MedicalOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class LabPanelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = LabPanel::withCount('labPanelItems')
            ->with('labPanelItems.inventory');

        // Search
        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->search.'%')
                ->orWhere('description', 'like', '%'.$request->search.'%');
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $labPanels = $query->paginate(15)->through(function ($labPanel) {
            return [
                'id' => $labPanel->id,
                'name' => $labPanel->name,
                'description' => $labPanel->description,
                'price' => $labPanel->price,
                'is_active' => $labPanel->is_active,
                'inventory_items_count' => $labPanel->lab_panel_items_count,
            ];
        });

        $categories = []; // Removed since category field is removed

        // Active medical orders with lab items for lab staff workflow
        $activeLabOrders = MedicalOrder::with(['patient', 'staff.user', 'orderItems'])
            ->whereIn('status', [MedicalOrderStatusEnum::PENDING, MedicalOrderStatusEnum::PROCESSING])
            ->whereHas('orderItems', function ($q) {
                $q->where('item_type', 'lab');
            })
            ->latest('ordered_at')
            ->get()
            ->map(function ($order) {
                $patient = $order->patient ?? $order->visit?->patient;

                return [
                    'id' => $order->id,
                    'patient_name' => $patient?->full_name ?? 'Unknown Patient',
                    'staff_name' => $order->staff?->user?->name ?? 'Unknown Staff',
                    'status' => $order->status->value,
                    'status_label' => $order->status->label(),
                    'priority' => $order->priority->value,
                    'priority_label' => $order->priority->label(),
                    'ordered_at' => $order->ordered_at?->format('M d, Y H:i'),
                    'lab_items' => $order->orderItems->where('item_type', 'lab')->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'item_name' => $item->item_name,
                            'details' => $item->details,
                            'status' => $item->status->value,
                            'status_label' => $item->status->label(),
                        ];
                    })->values(),
                ];
            });

        return Inertia::render('LabPanel/Index', [
            'labPanels' => $labPanels,
            'activeLabOrders' => $activeLabOrders,
            'categories' => $categories,
            'filters' => $request->only(['search', 'category', 'status']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $labSupplies = Inventory::select('id', 'item_name', 'unit', 'quantity')->get();

        return Inertia::render('LabPanel/Create', [
            'labSupplies' => $labSupplies,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
            'inventory_items' => 'array',
            'inventory_items.*.inventory_id' => 'required|exists:inventories,id',
            'inventory_items.*.quantity_required' => 'required|integer|min:1',
            'inventory_items.*.notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated) {
            $labPanel = LabPanel::create([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'price' => $validated['price'],
                'is_active' => $validated['is_active'] ?? true,
            ]);

            foreach ($validated['inventory_items'] as $item) {
                LabPanelItem::create([
                    'lab_panel_id' => $labPanel->id,
                    'inventory_id' => $item['inventory_id'],
                    'quantity_required' => $item['quantity_required'],
                    'notes' => $item['notes'] ?? null,
                ]);
            }
        });

        return redirect()->route('lab-panels.index')->with('success', 'Lab panel created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(LabPanel $labPanel)
    {
        $labPanel->load(['labPanelItems.inventory']);

        return Inertia::render('LabPanel/Show', [
            'labPanel' => [
                'id' => $labPanel->id,
                'name' => $labPanel->name,
                'description' => $labPanel->description,
                'price' => $labPanel->price,
                'is_active' => $labPanel->is_active,
                'created_at' => $labPanel->created_at,
                'updated_at' => $labPanel->updated_at,
                'inventory_items' => $labPanel->labPanelItems->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'item_name' => $item->inventory->item_name,
                        'unit' => $item->inventory->unit,
                        'quantity' => $item->inventory->quantity,
                        'price' => $item->inventory->selling_price,
                        'pivot' => [
                            'quantity_required' => $item->quantity_required,
                            'notes' => $item->notes,
                        ],
                    ];
                }),
            ],
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LabPanel $labPanel)
    {
        $labPanel->load(['labPanelItems.inventory']);

        $labSupplies = Inventory::select('id', 'item_name', 'unit', 'quantity')->get();

        return Inertia::render('LabPanel/Edit', [
            'labPanel' => [
                'id' => $labPanel->id,
                'name' => $labPanel->name,
                'description' => $labPanel->description,
                'price' => $labPanel->price,
                'is_active' => $labPanel->is_active,
                'inventory_items' => $labPanel->labPanelItems->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'item_name' => $item->inventory->item_name,
                        'unit' => $item->inventory->unit,
                        'quantity' => $item->inventory->quantity,
                        'price' => $item->inventory->selling_price,
                        'pivot' => [
                            'quantity_required' => $item->quantity_required,
                            'notes' => $item->notes,
                        ],
                    ];
                }),
            ],
            'labSupplies' => $labSupplies,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LabPanel $labPanel)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
            'inventory_items' => 'array',
            'inventory_items.*.inventory_id' => 'required|exists:inventories,id',
            'inventory_items.*.quantity_required' => 'required|integer|min:1',
            'inventory_items.*.notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated, $labPanel) {
            $labPanel->update([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'price' => $validated['price'],
                'is_active' => $validated['is_active'] ?? true,
            ]);

            // Delete existing items
            $labPanel->labPanelItems()->delete();

            // Create new items
            foreach ($validated['inventory_items'] as $item) {
                LabPanelItem::create([
                    'lab_panel_id' => $labPanel->id,
                    'inventory_id' => $item['inventory_id'],
                    'quantity_required' => $item['quantity_required'],
                    'notes' => $item['notes'] ?? null,
                ]);
            }
        });

        return redirect()->route('lab-panels.index')->with('success', 'Lab panel updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LabPanel $labPanel)
    {
        $labPanel->delete();

        return redirect()->route('lab-panels.index')->with('success', 'Lab panel deleted successfully.');
    }
}
