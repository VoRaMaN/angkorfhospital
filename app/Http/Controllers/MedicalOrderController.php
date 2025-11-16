<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMedicalOrderRequest;
use App\Http\Requests\UpdateMedicalOrderRequest;
use App\Models\MedicalOrder;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class MedicalOrderController extends Controller
{
    public function index(): Response
    {
        $this->authorize('viewAny', MedicalOrder::class);
        $medicalOrders = MedicalOrder::with(['patient.user', 'staff.user', 'orderItems'])->paginate(15);

        // Transform medical orders for the frontend
        $transformedOrders = $medicalOrders->getCollection()->map(function ($order) {
            return [
                'id' => $order->id,
                'patient_id' => $order->patient_id,
                'patient_name' => $order->patient?->user?->name ?? 'Unknown Patient',
                'staff_id' => $order->staff_id,
                'staff_name' => $order->staff?->user?->name ?? 'Unknown Staff',
                'order_details' => $order->order_details,
                'status' => $order->status->value,
                'status_label' => $order->status->label(),
                'priority' => $order->priority->value,
                'priority_label' => $order->priority->label(),
                'notes' => $order->notes,
                'ordered_at' => $order->ordered_at->toDateString(),
                'completed_at' => $order->completed_at?->toDateString(),
                'created_at' => $order->created_at,
                'items_count' => $order->orderItems->count(),
                'order_items' => $order->orderItems->map(function ($item) {
                    return [
                        'item_type' => $item->item_type,
                        'item_name' => $item->item_name,
                        'status' => $item->status->value,
                    ];
                }),
            ];
        });

        return Inertia::render('MedicalOrders/Index', [
            'medicalOrders' => $transformedOrders,
            'filters' => [
                'search' => request('search', ''),
            ],
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', MedicalOrder::class);

        $patients = \App\Models\Patient::with('user')->get()->map(function ($patient) {
            return [
                'id' => $patient->id,
                'name' => $patient->user?->name ?? 'Unknown Patient',
            ];
        });

        $staff = \App\Models\Staff::with('user')->get()->map(function ($staff) {
            return [
                'id' => $staff->id,
                'name' => $staff->user?->name ?? 'Unknown Staff',
            ];
        });

        $labPanels = \App\Models\LabPanel::where('is_active', true)
            ->with(['labPanelItems.inventory'])
            ->get()
            ->map(function ($panel) {
                return [
                    'id' => $panel->id,
                    'name' => $panel->name,
                    'description' => $panel->description,
                    'price' => $panel->price,
                    'items' => $panel->labPanelItems->map(function ($item) {
                        return [
                            'id' => $item->inventory_id,
                            'item_name' => $item->inventory->item_name ?? 'Unknown',
                            'quantity_required' => $item->quantity_required,
                            'notes' => $item->notes,
                        ];
                    }),
                ];
            });

        $inventoryItems = \App\Models\Inventory::all()->map(function ($item) {
            return [
                'id' => $item->id,
                'item_name' => $item->item_name,
                'type_of_supply' => $item->type_of_supply->value,
                'type_label' => $item->type_of_supply->label(),
                'unit' => $item->unit,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'selling_price' => $item->selling_price,
            ];
        });

        $rxMedicines = \App\Models\Inventory::where('type_of_supply', \App\Enums\SupplyTypeEnum::RX_MEDICINE)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'item_name' => $item->item_name,
                    'description' => $item->description,
                    'dose_unit' => $item->dose_unit,
                    'quantity' => $item->quantity,
                    'category' => $item->category,
                    'unit_price' => $item->unit_price,
                    'selling_price' => $item->selling_price,
                ];
            });

        $medicalServices = \App\Models\MedicalService::all()->map(function ($service) {
            return [
                'id' => $service->id,
                'name' => $service->name,
                'description' => $service->description,
                'type' => $service->type,
                'price' => $service->price,
            ];
        });

        return Inertia::render('MedicalOrders/Create', [
            'patients' => $patients,
            'staff' => $staff,
            'labPanels' => $labPanels,
            'inventoryItems' => $inventoryItems,
            'rxMedicines' => $rxMedicines,
            'medicalServices' => $medicalServices,
        ]);
    }

    public function store(StoreMedicalOrderRequest $request): RedirectResponse
    {
        $order = MedicalOrder::create($request->validated());

        // Create order items if provided
        if ($request->has('order_items') && is_array($request->order_items)) {
            foreach ($request->order_items as $item) {
                $order->orderItems()->create([
                    'inventory_id' => $item['inventory_id'] ?? null,
                    'item_type' => $item['item_type'],
                    'item_name' => $item['item_name'],
                    'details' => $item['details'] ?? null,
                    'dosage' => $item['dosage'] ?? null,
                    'frequency' => $item['frequency'] ?? null,
                    'route' => $item['route'] ?? null,
                    'quantity_required' => $item['quantity_required'] ?? 1,
                    'status' => \App\Enums\MedicalOrderStatusEnum::PENDING->value,
                    'notes' => $item['notes'] ?? null,
                ]);
            }
        }

        return redirect()->route('medical-orders.index')->with('success', 'Medical order created successfully.');
    }

    public function show(MedicalOrder $medicalOrder): Response
    {
        $this->authorize('view', $medicalOrder);
        $medicalOrder->load(['patient.user', 'staff.user', 'orderItems.inventory', 'visit.medicalRecord']);

        $labPanels = \App\Models\LabPanel::where('is_active', true)
            ->with(['labPanelItems.inventory'])
            ->get()
            ->map(function ($panel) {
                return [
                    'id' => $panel->id,
                    'name' => $panel->name,
                    'description' => $panel->description,
                    'price' => $panel->price,
                    'items' => $panel->labPanelItems->map(function ($item) {
                        return [
                            'id' => $item->inventory_id,
                            'item_name' => $item->inventory->item_name ?? 'Unknown',
                            'quantity_required' => $item->quantity_required,
                            'notes' => $item->notes,
                        ];
                    }),
                ];
            });

        $transformedOrder = [
            'id' => $medicalOrder->id,
            'patient_id' => $medicalOrder->patient_id,
            'patient_name' => $medicalOrder->patient?->user?->name ?? 'Unknown Patient',
            'staff_id' => $medicalOrder->staff_id,
            'staff_name' => $medicalOrder->staff?->user?->name ?? 'Unknown Staff',
            'order_details' => $medicalOrder->order_details,
            'status' => $medicalOrder->status->value,
            'status_label' => $medicalOrder->status->label(),
            'priority' => $medicalOrder->priority->value,
            'priority_label' => $medicalOrder->priority->label(),
            'notes' => $medicalOrder->notes,
            'ordered_at' => $medicalOrder->ordered_at->toDateString(),
            'completed_at' => $medicalOrder->completed_at?->toDateString(),
            'created_at' => $medicalOrder->created_at,
            'updated_at' => $medicalOrder->updated_at,
            'medical_record_id' => $medicalOrder->visit?->medicalRecord?->id,
            'order_items' => $medicalOrder->orderItems->map(function ($item) {
                return [
                    'id' => $item->id,
                    'item_type' => $item->item_type,
                    'item_name' => $item->item_name,
                    'details' => $item->details,
                    'dosage' => $item->dosage,
                    'frequency' => $item->frequency,
                    'route' => $item->route,
                    'quantity_required' => $item->quantity_required,
                    'selling_price' => $item->inventory?->selling_price,
                    'status' => $item->status->value,
                    'status_label' => $item->status->label(),
                    'notes' => $item->notes,
                    'completed_at' => $item->completed_at?->toDateString(),
                    'inventory_item' => $item->inventory ? [
                        'id' => $item->inventory->id,
                        'item_name' => $item->inventory->item_name,
                        'quantity' => $item->inventory->quantity,
                    ] : null,
                ];
            }),
        ];

        return Inertia::render('MedicalOrders/Show', [
            'medicalOrder' => $transformedOrder,
            'labPanels' => $labPanels,
        ]);
    }

    public function edit(MedicalOrder $medicalOrder): Response
    {
        $this->authorize('update', $medicalOrder);

        $medicalOrder->load(['patient.user', 'staff.user', 'orderItems.inventory']);

        $patients = \App\Models\Patient::with('user')->get()->map(function ($patient) {
            return [
                'id' => $patient->id,
                'name' => $patient->user?->name ?? 'Unknown Patient',
            ];
        });

        $staff = \App\Models\Staff::with('user')->get()->map(function ($staff) {
            return [
                'id' => $staff->id,
                'name' => $staff->user?->name ?? 'Unknown Staff',
            ];
        });

        $labPanels = \App\Models\LabPanel::where('is_active', true)
            ->with(['labPanelItems.inventory'])
            ->get()
            ->map(function ($panel) {
                return [
                    'id' => $panel->id,
                    'name' => $panel->name,
                    'description' => $panel->description,
                    'price' => $panel->price,
                    'items' => $panel->labPanelItems->map(function ($item) {
                        return [
                            'id' => $item->inventory_id,
                            'item_name' => $item->inventory->item_name ?? 'Unknown',
                            'quantity_required' => $item->quantity_required,
                            'notes' => $item->notes,
                        ];
                    }),
                ];
            });

        $inventoryItems = \App\Models\Inventory::all()->map(function ($item) {
            return [
                'id' => $item->id,
                'item_name' => $item->item_name,
                'type_of_supply' => $item->type_of_supply->value,
                'type_label' => $item->type_of_supply->label(),
                'unit' => $item->unit,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'selling_price' => $item->selling_price,
            ];
        });

        $rxMedicines = \App\Models\Inventory::where('type_of_supply', \App\Enums\SupplyTypeEnum::RX_MEDICINE)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'item_name' => $item->item_name,
                    'description' => $item->description,
                    'dose_unit' => $item->dose_unit,
                    'quantity' => $item->quantity,
                    'category' => $item->category,
                    'unit_price' => $item->unit_price,
                    'selling_price' => $item->selling_price,
                ];
            });

        $medicalServices = \App\Models\MedicalService::all()->map(function ($service) {
            return [
                'id' => $service->id,
                'name' => $service->name,
                'description' => $service->description,
                'type' => $service->type,
                'price' => $service->price,
            ];
        });

        $transformedOrder = [
            'id' => $medicalOrder->id,
            'patient_id' => $medicalOrder->patient_id,
            'staff_id' => $medicalOrder->staff_id,
            'order_details' => $medicalOrder->order_details,
            'status' => $medicalOrder->status->value,
            'priority' => $medicalOrder->priority->value,
            'notes' => $medicalOrder->notes,
            'ordered_at' => $medicalOrder->ordered_at->toDateString(),
            'completed_at' => $medicalOrder->completed_at?->toDateString(),
            'order_items' => $medicalOrder->orderItems->map(function ($item) {
                return [
                    'id' => $item->id,
                    'inventory_id' => $item->inventory_id,
                    'item_type' => $item->item_type,
                    'item_name' => $item->item_name,
                    'details' => $item->details,
                    'dosage' => $item->dosage,
                    'frequency' => $item->frequency,
                    'route' => $item->route,
                    'quantity_required' => $item->quantity_required,
                    'status' => $item->status->value,
                    'notes' => $item->notes,
                ];
            }),
        ];

        return Inertia::render('MedicalOrders/Edit', [
            'medicalOrder' => $transformedOrder,
            'patients' => $patients,
            'staff' => $staff,
            'labPanels' => $labPanels,
            'inventoryItems' => $inventoryItems,
            'rxMedicines' => $rxMedicines,
            'medicalServices' => $medicalServices,
        ]);
    }

    public function update(UpdateMedicalOrderRequest $request, MedicalOrder $medicalOrder): RedirectResponse
    {
        $medicalOrder->update($request->validated());

        // Sync order items if provided
        if ($request->has('order_items') && is_array($request->order_items)) {
            // Delete existing items
            $medicalOrder->orderItems()->delete();

            // Create new items
            foreach ($request->order_items as $item) {
                $medicalOrder->orderItems()->create([
                    'inventory_id' => $item['inventory_id'] ?? null,
                    'item_type' => $item['item_type'],
                    'item_name' => $item['item_name'],
                    'details' => $item['details'] ?? null,
                    'dosage' => $item['dosage'] ?? null,
                    'frequency' => $item['frequency'] ?? null,
                    'route' => $item['route'] ?? null,
                    'quantity_required' => $item['quantity_required'] ?? 1,
                    'status' => $item['status'] ?? 'pending',
                    'notes' => $item['notes'] ?? null,
                ]);
            }
        }

        return redirect()->route('medical-orders.index')->with('success', 'Medical order updated successfully.');
    }

    public function destroy(MedicalOrder $medicalOrder): RedirectResponse
    {
        $this->authorize('delete', $medicalOrder);
        $medicalOrder->delete();

        return redirect()->route('medical-orders.index')->with('success', 'Medical order deleted successfully.');
    }

    /**
     * Process a medical order with updates.
     */
    public function processWithUpdate(UpdateMedicalOrderRequest $request, MedicalOrder $medicalOrder): RedirectResponse
    {
        $this->authorize('update', $medicalOrder);

        // Only allow processing if the order is pending
        if ($medicalOrder->status !== \App\Enums\MedicalOrderStatusEnum::PENDING) {
            return redirect()->back()->with('error', 'This medical order cannot be processed.');
        }

        // Update the medical order details
        $medicalOrder->update($request->validated());

        // Sync order items if provided
        if ($request->has('order_items') && is_array($request->order_items)) {
            // Delete existing items
            $medicalOrder->orderItems()->delete();

            // Create new items
            foreach ($request->order_items as $item) {
                $medicalOrder->orderItems()->create([
                    'inventory_id' => $item['inventory_id'] ?? null,
                    'item_type' => $item['item_type'],
                    'item_name' => $item['item_name'],
                    'details' => $item['details'] ?? null,
                    'dosage' => $item['dosage'] ?? null,
                    'frequency' => $item['frequency'] ?? null,
                    'route' => $item['route'] ?? null,
                    'quantity_required' => $item['quantity_required'] ?? 1,
                    'status' => $item['status'] ?? 'pending',
                    'notes' => $item['notes'] ?? null,
                ]);
            }
        }

        $medicalOrder->load('orderItems');

        // Check if the medical order has any items
        if ($medicalOrder->orderItems->isEmpty()) {
            return redirect()->back()->with('error', 'Cannot process medical order with no items. Please add at least one item before processing.');
        }

        // Update the status to processed
        $medicalOrder->update([
            'status' => \App\Enums\MedicalOrderStatusEnum::PROCESSED,
        ]);

        return redirect()->route('medical-orders.already-processed', $medicalOrder->id)
            ->with('success', 'Medical order processed successfully.');
    }

    /**
     * Show the process page for a medical order.
     */
    public function processPage(MedicalOrder $medicalOrder)
    {
        $this->authorize('update', $medicalOrder);

        // If the order is already processed or completed, redirect to already processed page
        if ($medicalOrder->status !== \App\Enums\MedicalOrderStatusEnum::PENDING) {
            return redirect()->route('medical-orders.already-processed', $medicalOrder);
        }

        $medicalOrder->load(['patient.user', 'staff.user', 'orderItems.inventory']);

        $labPanels = \App\Models\LabPanel::where('is_active', true)
            ->with(['labPanelItems.inventory'])
            ->get()
            ->map(function ($panel) {
                return [
                    'id' => $panel->id,
                    'name' => $panel->name,
                    'description' => $panel->description,
                    'price' => $panel->price,
                    'items' => $panel->labPanelItems->map(function ($item) {
                        return [
                            'id' => $item->inventory_id,
                            'item_name' => $item->inventory->item_name ?? 'Unknown',
                            'quantity_required' => $item->quantity_required,
                            'notes' => $item->notes,
                        ];
                    }),
                ];
            });

        $inventoryItems = \App\Models\Inventory::all()->map(function ($item) {
            return [
                'id' => $item->id,
                'item_name' => $item->item_name,
                'type_of_supply' => $item->type_of_supply->value,
                'type_label' => $item->type_of_supply->label(),
                'unit' => $item->unit,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'selling_price' => $item->selling_price,
            ];
        });

        $rxMedicines = \App\Models\Inventory::where('type_of_supply', \App\Enums\SupplyTypeEnum::RX_MEDICINE)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'item_name' => $item->item_name,
                    'description' => $item->description,
                    'dose_unit' => $item->dose_unit,
                    'quantity' => $item->quantity,
                    'category' => $item->category,
                    'unit_price' => $item->unit_price,
                    'selling_price' => $item->selling_price,
                ];
            });

        $transformedOrder = [
            'id' => $medicalOrder->id,
            'patient_id' => $medicalOrder->patient_id,
            'patient_name' => $medicalOrder->patient?->user?->name ?? 'Unknown Patient',
            'staff_id' => $medicalOrder->staff_id,
            'staff_name' => $medicalOrder->staff?->user?->name ?? 'Unknown Staff',
            'order_details' => $medicalOrder->order_details,
            'status' => $medicalOrder->status->value,
            'status_label' => $medicalOrder->status->label(),
            'priority' => $medicalOrder->priority->value,
            'priority_label' => $medicalOrder->priority->label(),
            'notes' => $medicalOrder->notes,
            'ordered_at' => $medicalOrder->ordered_at->toDateString(),
            'completed_at' => $medicalOrder->completed_at?->toDateString(),
            'created_at' => $medicalOrder->created_at,
            'updated_at' => $medicalOrder->updated_at,
            'orderItems' => $medicalOrder->orderItems->map(function ($item) {
                return [
                    'id' => $item->id,
                    'item_type' => $item->item_type,
                    'item_name' => $item->item_name,
                    'details' => $item->details,
                    'dosage' => $item->dosage,
                    'frequency' => $item->frequency,
                    'route' => $item->route,
                    'quantity_required' => $item->quantity_required,
                    'status' => $item->status->value,
                    'notes' => $item->notes,
                    'inventory_id' => $item->inventory_id,
                    'unit_price' => $item->inventory?->unit_price,
                    'selling_price' => $item->inventory?->selling_price,
                ];
            }),
        ];

        return Inertia::render('MedicalOrders/Process', [
            'medicalOrder' => $transformedOrder,
            'labPanels' => $labPanels,
            'inventoryItems' => $inventoryItems,
            'rxMedicines' => $rxMedicines,
        ]);
    }

    /**
     * Show the already processed page for a medical order.
     */
    public function alreadyProcessedPage(MedicalOrder $medicalOrder): Response
    {
        $this->authorize('view', $medicalOrder);
        $medicalOrder->load(['patient.user', 'staff.user', 'orderItems.inventory', 'visit.medicalRecord']);

        $labPanels = \App\Models\LabPanel::where('is_active', true)
            ->with(['labPanelItems.inventory'])
            ->get()
            ->map(function ($panel) {
                return [
                    'id' => $panel->id,
                    'name' => $panel->name,
                    'description' => $panel->description,
                    'price' => $panel->price,
                    'items' => $panel->labPanelItems->map(function ($item) {
                        return [
                            'id' => $item->inventory_id,
                            'item_name' => $item->inventory->item_name ?? 'Unknown',
                            'quantity_required' => $item->quantity_required,
                            'notes' => $item->notes,
                        ];
                    }),
                ];
            });

        $transformedOrder = [
            'id' => $medicalOrder->id,
            'patient_id' => $medicalOrder->patient_id,
            'patient_name' => $medicalOrder->patient?->user?->name ?? 'Unknown Patient',
            'staff_id' => $medicalOrder->staff_id,
            'staff_name' => $medicalOrder->staff?->user?->name ?? 'Unknown Staff',
            'order_details' => $medicalOrder->order_details,
            'status' => $medicalOrder->status->value,
            'status_label' => $medicalOrder->status->label(),
            'priority' => $medicalOrder->priority->value,
            'priority_label' => $medicalOrder->priority->label(),
            'notes' => $medicalOrder->notes,
            'ordered_at' => $medicalOrder->ordered_at->toDateString(),
            'completed_at' => $medicalOrder->completed_at?->toDateString(),
            'created_at' => $medicalOrder->created_at,
            'updated_at' => $medicalOrder->updated_at,
            'medical_record_id' => $medicalOrder->visit?->medicalRecord?->id,
            'order_items' => $medicalOrder->orderItems->map(function ($item) {
                return [
                    'id' => $item->id,
                    'item_type' => $item->item_type,
                    'item_name' => $item->item_name,
                    'details' => $item->details,
                    'dosage' => $item->dosage,
                    'frequency' => $item->frequency,
                    'route' => $item->route,
                    'quantity_required' => $item->quantity_required,
                    'selling_price' => $item->inventory?->selling_price,
                    'status' => $item->status->value,
                    'status_label' => $item->status->label(),
                    'notes' => $item->notes,
                    'completed_at' => $item->completed_at?->toDateString(),
                    'inventory_item' => $item->inventory ? [
                        'id' => $item->inventory->id,
                        'item_name' => $item->inventory->item_name,
                        'quantity' => $item->inventory->quantity,
                    ] : null,
                ];
            }),
        ];

        return Inertia::render('MedicalOrders/AlreadyProcessed', [
            'medicalOrder' => $transformedOrder,
            'labPanels' => $labPanels,
        ]);
    }

    /**
     * Show the complete process page for a medical order.
     */
    public function completePage(MedicalOrder $medicalOrder): Response
    {
        $this->authorize('update', $medicalOrder);
        $medicalOrder->load(['patient.user', 'staff.user', 'orderItems.inventory']);

        $transformedOrder = [
            'id' => $medicalOrder->id,
            'patient_id' => $medicalOrder->patient_id,
            'patient_name' => $medicalOrder->patient?->user?->name ?? 'Unknown Patient',
            'staff_id' => $medicalOrder->staff_id,
            'staff_name' => $medicalOrder->staff?->user?->name ?? 'Unknown Staff',
            'order_details' => $medicalOrder->order_details,
            'status' => $medicalOrder->status->value,
            'status_label' => $medicalOrder->status->label(),
            'priority' => $medicalOrder->priority->value,
            'priority_label' => $medicalOrder->priority->label(),
            'notes' => $medicalOrder->notes,
            'ordered_at' => $medicalOrder->ordered_at->toDateString(),
            'completed_at' => $medicalOrder->completed_at?->toDateString(),
            'created_at' => $medicalOrder->created_at,
            'updated_at' => $medicalOrder->updated_at,
            'order_items' => $medicalOrder->orderItems->map(function ($item) {
                return [
                    'id' => $item->id,
                    'item_type' => $item->item_type,
                    'item_name' => $item->item_name,
                    'details' => $item->details,
                    'dosage' => $item->dosage,
                    'frequency' => $item->frequency,
                    'route' => $item->route,
                    'quantity_required' => $item->quantity_required,
                    'selling_price' => $item->inventory?->selling_price,
                    'status' => $item->status->value,
                    'status_label' => $item->status->label(),
                    'notes' => $item->notes,
                    'completed_at' => $item->completed_at?->toDateString(),
                    'inventory_item' => $item->inventory ? [
                        'id' => $item->inventory->id,
                        'item_name' => $item->inventory->item_name,
                        'quantity' => $item->inventory->quantity,
                    ] : null,
                ];
            }),
        ];

        return Inertia::render('MedicalOrders/Complete', [
            'medicalOrder' => $transformedOrder,
        ]);
    }

    /**
     * Complete a medical order.
     */
    public function complete(MedicalOrder $medicalOrder): RedirectResponse
    {
        $this->authorize('update', $medicalOrder);

        $medicalOrder->update([
            'status' => \App\Enums\MedicalOrderStatusEnum::COMPLETED,
            'completed_at' => now(),
        ]);

        return redirect()->route('medical-orders.show', $medicalOrder)->with('success', 'Medical order completed successfully.');
    }

    /**
     * Complete an individual order item.
     */
    public function completeItem(MedicalOrder $medicalOrder, \App\Models\MedicalOrderInventory $item): RedirectResponse
    {
        $this->authorize('update', $medicalOrder);

        // Ensure the item belongs to this medical order
        if ($item->medical_order_id !== $medicalOrder->id) {
            abort(404);
        }

        $item->update([
            'status' => \App\Enums\MedicalOrderStatusEnum::COMPLETED,
            'completed_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Order item completed successfully.');
    }
}
