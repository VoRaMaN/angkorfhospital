<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMedicalOrderRequest;
use App\Http\Requests\UpdateMedicalOrderRequest;
use App\Models\MedicalOrder;
use App\Services\MedicalOrderBillingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
        $medicalOrder->load(['patient.user', 'staff.user', 'orderItems.inventory', 'visit.medicalRecord', 'billings']);

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
            'billing' => $medicalOrder->billings->isNotEmpty() ? (function ($billing) {
                return [
                    'id' => $billing->id,
                    'amount' => $billing->amount,
                    'status' => $billing->status,
                    'status_label' => ucfirst($billing->status),
                    'status_color' => match ($billing->status) {
                        'pending' => 'text-yellow-600',
                        'paid' => 'text-green-600',
                        'overdue' => 'text-red-600',
                        'partial' => 'text-blue-600',
                        'written_off' => 'text-gray-600',
                        'cancelled' => 'text-gray-600',
                        default => 'text-gray-600',
                    },
                    'billed_at' => $billing->billing_date?->toDateString(),
                    'due_date' => $billing->due_date?->toDateString(),
                    'paid_at' => $billing->paid_at?->toDateString(),
                    'notes' => $billing->notes,
                ];
            })($medicalOrder->billings->sortByDesc('created_at')->first()) : null,
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
                $unitPrice = $item->inventory?->unit_price;
                $sellingPrice = $item->inventory?->selling_price;

                // For medical services without inventory, look up price from MedicalService
                if (! $item->inventory_id && in_array($item->item_type, ['procedure', 'imaging', 'consultation', 'therapy'])) {
                    $medicalService = \App\Models\MedicalService::where('name', $item->item_name)->first();
                    if ($medicalService) {
                        $unitPrice = $medicalService->price;
                        $sellingPrice = $medicalService->price;
                    }
                }

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
                    'unit_price' => $unitPrice,
                    'selling_price' => $sellingPrice,
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

    public function processWithUpdate(UpdateMedicalOrderRequest $request, MedicalOrder $medicalOrder): RedirectResponse
    {
        $this->authorize('process', $medicalOrder);

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
            'status' => \App\Enums\MedicalOrderStatusEnum::PROCESSING,
        ]);

        // Update visit status to in_progress when medical order is processing
        if ($medicalOrder->visit) {
            $medicalOrder->visit->update(['status' => \App\Models\Visit::STATUS_IN_PROGRESS]);
        }

        return redirect()->route('medical-orders.processing-page', $medicalOrder->id)
            ->with('success', 'Medical order processed successfully.');
    }

    public function processPage(MedicalOrder $medicalOrder)
    {
        $this->authorize('process', $medicalOrder);

        // If the order is already processed or completed, redirect to already processed page
        if ($medicalOrder->status !== \App\Enums\MedicalOrderStatusEnum::PENDING) {
            return redirect()->route('medical-orders.processing-page', $medicalOrder);
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
            'medicalServices' => $medicalServices,
        ]);
    }

    /**
     * Show the already processed page for a medical order.
     */
    public function processingPage(MedicalOrder $medicalOrder): Response
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

        return Inertia::render('MedicalOrders/Processing', [
            'medicalOrder' => $transformedOrder,
            'labPanels' => $labPanels,
        ]);
    }

    public function completePage(MedicalOrder $medicalOrder): Response|\Illuminate\Http\RedirectResponse
    {
        $this->authorize('complete', $medicalOrder);

        // Only allow access if the order is processed (not already completed)
        if ($medicalOrder->status === \App\Enums\MedicalOrderStatusEnum::COMPLETED) {
            return redirect()->route('medical-orders.show', $medicalOrder)
                ->with('error', 'This medical order has already been completed.');
        }

        // Only allow access if the order is in processed status
        if ($medicalOrder->status !== \App\Enums\MedicalOrderStatusEnum::PROCESSED) {
            return redirect()->route('medical-orders.show', $medicalOrder)
                ->with('error', 'This medical order is not ready for completion.');
        }

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

    public function complete(MedicalOrder $medicalOrder): RedirectResponse
    {
        $this->authorize('complete', $medicalOrder);

        $medicalOrder->update([
            'status' => \App\Enums\MedicalOrderStatusEnum::COMPLETED,
            'completed_at' => now(),
        ]);

        return redirect()->route('medical-orders.show', $medicalOrder)->with('success', 'Medical order completed successfully.');
    }

    public function processAndBill(MedicalOrder $medicalOrder): RedirectResponse
    {
        $this->authorize('processAndBill', $medicalOrder);

        // Only allow processing if the order is pending or processed
        if (! in_array($medicalOrder->status, [\App\Enums\MedicalOrderStatusEnum::PENDING, \App\Enums\MedicalOrderStatusEnum::PROCESSING, \App\Enums\MedicalOrderStatusEnum::PROCESSED])) {
            return redirect()->back()->with('error', 'This medical order cannot be processed for billing.');
        }

        $billingService = app(MedicalOrderBillingService::class);

        // Check if order can be processed (sufficient inventory)
        $canProcess = $billingService->canProcessOrder($medicalOrder);
        if (! $canProcess['can_process']) {
            $issues = implode(', ', $canProcess['issues']);

            return redirect()->back()->with('error', "Cannot process order due to inventory issues: {$issues}");
        }

        try {
            // Process the order and create billing
            $billing = $billingService->processOrderAndCreateBilling(
                $medicalOrder,
                'Processed and billed on '.now()->format('Y-m-d H:i')
            );

            // Status is already updated in the service, no need to update again

            return redirect()->route('medical-orders.show', $medicalOrder)
                ->with('success', 'Medical order processed successfully. Billing created with total amount: $'.number_format((float) $billing->amount, 2));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to process medical order: '.$e->getMessage());
        }
    }

    public function confirmProcessed(MedicalOrder $medicalOrder): RedirectResponse
    {
        $this->authorize('confirmProcessed', $medicalOrder);

        // Only allow confirming if the order is processing
        if ($medicalOrder->status !== \App\Enums\MedicalOrderStatusEnum::PROCESSING) {
            return redirect()->back()->with('error', 'This medical order cannot be confirmed as processed.');
        }

        // Update the status to processed
        $medicalOrder->update([
            'status' => \App\Enums\MedicalOrderStatusEnum::PROCESSED,
        ]);

        // Update visit status to awaiting_accountant when medical order is processed
        if ($medicalOrder->visit) {
            $medicalOrder->visit->update(['status' => \App\Models\Visit::STATUS_AWAITING_ACCOUNTANT]);
        }

        return redirect()->route('medical-orders.complete-page', $medicalOrder)
            ->with('success', 'Medical order confirmed as processed successfully.');
    }

    /**
     * Get order cost breakdown.
     */
    public function getCostBreakdown(MedicalOrder $medicalOrder): \Illuminate\Http\JsonResponse
    {
        $this->authorize('view', $medicalOrder);

        $billingService = app(MedicalOrderBillingService::class);
        $breakdown = $billingService->getOrderCostBreakdown($medicalOrder);

        return response()->json($breakdown);
    }

    public function sendBack(Request $request, MedicalOrder $medicalOrder): RedirectResponse
    {
        $this->authorize('sendBack', $medicalOrder);

        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        // Update the status back to processing and add the reason to notes
        $currentNotes = $medicalOrder->notes ? $medicalOrder->notes."\n\n" : '';
        $revisionNote = 'Sent back for revision on '.now()->format('Y-m-d H:i').":\n".$request->reason;

        $medicalOrder->update([
            'status' => \App\Enums\MedicalOrderStatusEnum::PROCESSING,
            'notes' => $currentNotes.$revisionNote,
        ]);

        // Reset all order items back to pending status when sent back for revision
        $medicalOrder->orderItems()->update([
            'status' => \App\Enums\MedicalOrderStatusEnum::PENDING->value,
            'completed_at' => null,
        ]);

        // Update visit status to sent_back when medical order is sent back to processing
        if ($medicalOrder->visit) {
            $medicalOrder->visit->update(['status' => \App\Enums\VisitStatusEnum::SENT_BACK]);
        }

        return redirect()->back()->with('success', 'Medical order has been sent back for revision.');
    }

    public function completeItem(MedicalOrder $medicalOrder, \App\Models\MedicalOrderInventory $item): RedirectResponse
    {
        $this->authorize('completeItem', $medicalOrder);

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

    /**
     * Generate a comprehensive medical order report as PDF.
     */
    public function generateReport(MedicalOrder $medicalOrder): \Illuminate\Http\Response
    {
        $this->authorize('view', $medicalOrder);

        $medicalOrder->load([
            'patient.user',
            'staff.user',
            'orderItems.inventory',
            'visit.patient.user',
            'visit.staff.user',
            'visit.appointment',
            'medicalRecords',
            'billings',
        ]);

        // Compile report data
        $report = [
            'order_info' => [
                'id' => $medicalOrder->id,
                'order_type' => $medicalOrder->order_type,
                'status' => $medicalOrder->status,
                'priority' => $medicalOrder->priority,
                'order_details' => $medicalOrder->order_details,
                'notes' => $medicalOrder->notes,
                'ordered_at' => $medicalOrder->ordered_at,
                'completed_at' => $medicalOrder->completed_at,
                'created_at' => $medicalOrder->created_at,
                'updated_at' => $medicalOrder->updated_at,
            ],
            'patient_info' => [
                'id' => $medicalOrder->patient->id,
                'name' => $medicalOrder->patient->user?->name ?? $medicalOrder->patient->first_name.' '.$medicalOrder->patient->last_name,
                'date_of_birth' => $medicalOrder->patient->date_of_birth,
                'gender' => $medicalOrder->patient->gender,
                'phone_number' => $medicalOrder->patient->phone_number,
                'email' => $medicalOrder->patient->email ?? $medicalOrder->patient->user?->email,
            ],
            'staff_info' => $medicalOrder->staff ? [
                'id' => $medicalOrder->staff->id,
                'name' => $medicalOrder->staff->user?->name ?? $medicalOrder->staff->first_name.' '.$medicalOrder->staff->last_name,
                'role' => $medicalOrder->staff->role?->name ?? 'Unknown',
            ] : null,
            'appointment_info' => $medicalOrder->visit?->appointment ? [
                'id' => $medicalOrder->visit->appointment->id,
                'appointment_date_time' => $medicalOrder->visit->appointment->appointment_date_time,
                'duration_minutes' => $medicalOrder->visit->appointment->duration_minutes,
                'reason_for_visit' => $medicalOrder->visit->appointment->reason_for_visit,
                'status' => $medicalOrder->visit->appointment->status,
                'notes' => $medicalOrder->visit->appointment->notes,
            ] : null,
            'visit_info' => $medicalOrder->visit ? [
                'id' => $medicalOrder->visit->id,
                'visit_date_time' => $medicalOrder->visit->visit_date_time,
                'status' => $medicalOrder->visit->status,
                'notes' => $medicalOrder->visit->notes,
            ] : null,
            'order_items' => $medicalOrder->orderItems->map(function ($item) {
                return [
                    'id' => $item->id,
                    'item_type' => $item->item_type,
                    'item_name' => $item->inventory?->item_name ?? $item->item_name ?? 'Unknown Item',
                    'quantity_required' => $item->quantity_required,
                    'quantity_used' => $item->quantity_used,
                    'unit' => $item->inventory?->unit ?? 'units',
                    'unit_price' => $item->inventory?->unit_price ?? 0,
                    'total_cost' => ($item->quantity_used ?? $item->quantity_required ?? 1) * ($item->inventory?->unit_price ?? 0),
                    'status' => $item->status,
                    'notes' => $item->notes,
                    'completed_at' => $item->completed_at,
                ];
            }),
            'medical_records' => $medicalOrder->medicalRecords->map(function ($record) {
                return [
                    'id' => $record->id,
                    'diagnosis' => $record->diagnosis,
                    'treatment' => $record->treatment,
                    'notes' => $record->notes,
                    'date_of_service' => $record->date_of_service,
                    'created_at' => $record->created_at,
                ];
            }),
            'billings' => $medicalOrder->billings->map(function ($billing) {
                return [
                    'id' => $billing->id,
                    'amount' => $billing->amount,
                    'status' => $billing->status,
                    'billing_date' => $billing->billing_date,
                    'due_date' => $billing->due_date,
                    'paid_at' => $billing->paid_at,
                    'notes' => $billing->notes,
                ];
            }),
        ];

        // Calculate totals
        $report['totals'] = [
            'total_items' => $medicalOrder->orderItems->count(),
            'completed_items' => $medicalOrder->orderItems->where('status', 'completed')->count(),
            'total_cost' => $medicalOrder->orderItems->sum(function ($item) {
                return ($item->quantity_used ?? $item->quantity_required ?? 1) * ($item->inventory?->unit_price ?? 0);
            }),
            'total_billed' => $medicalOrder->billings->sum('amount'),
        ];

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('medical-order-report', compact('report', 'medicalOrder'));

        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'DejaVu Sans',
            'dpi' => 96,
            'isPhpEnabled' => true,
        ]);

        $filename = 'medical-order-report-'.$medicalOrder->id.'-'.now()->format('Y-m-d').'.pdf';

        return $pdf->download($filename);
    }
}
