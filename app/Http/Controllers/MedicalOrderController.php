<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMedicalOrderRequest;
use App\Http\Requests\UpdateMedicalOrderRequest;
use App\Models\MedicalOrder;
use App\Models\MedicalRecord;
use App\Models\Patch;
use App\Models\Visit;
use App\Services\MedicalOrderBillingService;
use App\Services\MedicalOrderProcessingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MedicalOrderController extends Controller
{
    public function index(): Response
    {
        $this->authorize('viewAny', MedicalOrder::class);

        // Auto-archive: mark expired active medical orders as completed
        MedicalOrder::whereIn('status', [
            \App\Enums\MedicalOrderStatusEnum::PENDING,
            \App\Enums\MedicalOrderStatusEnum::PROCESSING,
            \App\Enums\MedicalOrderStatusEnum::PROCESSED,
        ])
            ->whereDate('ordered_at', '<', today())
            ->update(['status' => \App\Enums\MedicalOrderStatusEnum::COMPLETED]);

        $query = MedicalOrder::with(['patient.user', 'staff.user', 'orderItems', 'visit.patient.user', 'visit.staff.user'])
            ->whereNotIn('status', [
                \App\Enums\MedicalOrderStatusEnum::PAID,
                \App\Enums\MedicalOrderStatusEnum::COMPLETED,
                \App\Enums\MedicalOrderStatusEnum::CANCEL,
                \App\Enums\MedicalOrderStatusEnum::REJECTED,
            ])
            ->where(function ($query) {
                $query->whereNotNull('patient_id')
                    ->orWhereHas('visit', function ($q) {
                        $q->whereNotNull('patient_id');
                    });
            });

        // Date filter - defaults to today
        $dateFilter = request('date');
        if ($dateFilter) {
            $query->whereDate('ordered_at', $dateFilter);
        } else {
            $query->whereDate('ordered_at', today());
        }

        // Search filter
        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('patient.user', function ($patientQuery) use ($search) {
                    $patientQuery->where('name', 'like', '%'.$search.'%');
                })
                    ->orWhereHas('patient', function ($patientQuery) use ($search) {
                        $patientQuery->where('name', 'like', '%'.$search.'%')
                            ->orWhere('surname', 'like', '%'.$search.'%');
                    })
                    ->orWhereHas('staff.user', function ($staffQuery) use ($search) {
                        $staffQuery->where('name', 'like', '%'.$search.'%');
                    });
            });
        }

        $medicalOrders = $query->paginate(15);

        // Transform medical orders for the frontend
        $transformedOrders = $medicalOrders->getCollection()->map(function ($order) {
            // Get patient from order or visit
            $patient = $order->patient ?? $order->visit?->patient;
            $patientName = $patient?->full_name ?: 'Unknown Patient';

            // Get staff from order or visit
            $staff = $order->staff ?? $order->visit?->staff;
            $staffName = $staff?->user?->name ?? 'Unknown Staff';

            return [
                'id' => $order->id,
                'patient_id' => $patient?->id,
                'patient_name' => $patientName,
                'staff_id' => $staff?->id,
                'staff_name' => $staffName,
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
                'date' => request('date', today()->toDateString()),
            ],
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', MedicalOrder::class);

        $patients = \App\Models\Patient::all()->map(function ($patient) {
            return [
                'id' => $patient->id,
                'name' => $patient->full_name ?: 'Unknown Patient',
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

        $patches = Patch::with('inventories')
            ->get()
            ->map(function (Patch $patch) {
                $items = $patch->inventories->map(fn ($item) => [
                    'id' => $item->id,
                    'item_name' => $item->item_name,
                    'unit_price' => $item->unit_price,
                    'type_of_supply' => $item->type_of_supply->value,
                    'category' => $item->category,
                ]);

                return [
                    'id' => $patch->id,
                    'name' => $patch->name,
                    'total_unit_price' => $patch->total_unit_price,
                    'items' => $items->values(),
                ];
            });

        return Inertia::render('MedicalOrders/Create', [
            'patients' => $patients,
            'staff' => $staff,
            'labPanels' => $labPanels,
            'inventoryItems' => $inventoryItems,
            'rxMedicines' => $rxMedicines,
            'medicalServices' => $medicalServices,
            'patches' => $patches,
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
                    'unit_price' => $item['unit_price'] ?? 0,
                    'selling_price' => $item['selling_price'] ?? 0,
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
            'patient_name' => $medicalOrder->patient?->full_name ?: 'Unknown Patient',
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
                    'status_label' => $billing->status,
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

        $medicalOrder->load(['patient', 'staff.user', 'orderItems.inventory', 'visit.patient', 'visit.staff.user']);

        $patients = \App\Models\Patient::all()->map(function ($patient) {
            return [
                'id' => $patient->id,
                'name' => $patient->full_name ?: 'Unknown Patient',
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
                // Use stored price if > 0, else fall back to inventory price
                $unitPrice = $item->unit_price > 0
                    ? (float) $item->unit_price
                    : ($item->inventory?->unit_price ?? 0);
                $sellingPrice = $item->selling_price > 0
                    ? (float) $item->selling_price
                    : ($item->inventory?->selling_price ?? 0);

                // For medical services without inventory, look up price from MedicalService
                if ($sellingPrice <= 0 && ! $item->inventory_id && in_array($item->item_type, ['procedure', 'imaging', 'consultation', 'therapy'])) {
                    $medicalService = \App\Models\MedicalService::where('name', $item->item_name)->first();
                    if ($medicalService) {
                        $unitPrice = (float) $medicalService->price;
                        $sellingPrice = (float) $medicalService->price;
                    }
                }

                // For special items with no stored price, look up from SpecialItem or MedicineGroup
                if ($sellingPrice <= 0 && $item->item_type === 'special_item') {
                    $specialItem = \App\Models\SpecialItem::where('name', $item->item_name)->first();
                    if ($specialItem) {
                        $unitPrice = (float) $specialItem->unit_price;
                        $sellingPrice = (float) $specialItem->unit_price;
                    }

                    if ($sellingPrice <= 0) {
                        $medicineGroup = \App\Models\MedicineGroup::where('name', $item->item_name)->first();
                        if ($medicineGroup) {
                            $price = (float) $medicineGroup->total_price;
                            $unitPrice = $price;
                            $sellingPrice = $price;
                        }
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
            'medicineGroups' => \App\Models\MedicineGroup::where('is_active', true)
                ->with(['items.inventory'])
                ->get()
                ->map(function ($group) {
                    $totalPrice = $group->custom_price;
                    if (! $totalPrice) {
                        $totalPrice = $group->items->sum(function ($item) {
                            return ($item->inventory->selling_price ?? 0) * $item->quantity;
                        });
                    }

                    return [
                        'id' => $group->id,
                        'name' => $group->name,
                        'description' => $group->description,
                        'custom_price' => $group->custom_price,
                        'total_price' => $totalPrice,
                        'items' => $group->items->map(function ($item) {
                            return [
                                'id' => $item->inventory_id,
                                'item_name' => $item->inventory->item_name ?? 'Unknown',
                                'dosage' => $item->dosage,
                                'frequency' => $item->frequency,
                                'quantity' => $item->quantity,
                                'unit_price' => $item->inventory->unit_price ?? 0,
                                'selling_price' => $item->inventory->selling_price ?? 0,
                            ];
                        }),
                    ];
                }),
            'specialItems' => \App\Models\SpecialItem::where('is_active', true)
                ->with(['items.inventory'])
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                        'description' => $item->description,
                        'unit_price' => (float) $item->unit_price,
                        'items' => $item->items->map(function ($subItem) {
                            return [
                                'id' => $subItem->inventory_id,
                                'item_name' => $subItem->inventory->item_name ?? 'Unknown',
                                'quantity' => $subItem->quantity,
                            ];
                        }),
                    ];
                }),
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
                    'unit_price' => $item['unit_price'] ?? 0,
                    'selling_price' => $item['selling_price'] ?? 0,
                    'status' => $item['status'] ?? 'pending',
                    'notes' => $item['notes'] ?? null,
                ]);
            }
        }

        // Recalculate any linked pending billing so the amount stays in sync
        if ($request->has('order_items')) {
            $medicalOrder->load('orderItems');
            $linkedBilling = \App\Models\Billing::where('medical_order_id', $medicalOrder->id)
                ->whereIn('status', ['pending', 'revision'])
                ->first();

            if ($linkedBilling) {
                $billingService = app(MedicalOrderBillingService::class);
                $newAmount = $billingService->calculateOrderTotal($medicalOrder);
                $linkedBilling->update(['amount' => $newAmount]);
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
                    'unit_price' => $item['unit_price'] ?? 0,
                    'selling_price' => $item['selling_price'] ?? 0,
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

        // Process the order: notify departments, reduce stock, create billing
        $processingService = app(MedicalOrderProcessingService::class);
        $result = $processingService->processOrder($medicalOrder);

        if (! $result['success']) {
            return redirect()->back()->with('error', $result['error']);
        }

        $successMessage = sprintf(
            'Medical order processed successfully! %d department(s) notified. Total amount: $%s',
            count($result['notifications_sent']),
            number_format($result['total_amount'], 2)
        );

        // Only redirect to billing if the user has permission to view it
        if ($request->user()->can('view_billing') || $request->user()->hasRole('admin')) {
            return redirect()->route('billings.show', $result['billing_id'])
                ->with('success', $successMessage);
        }

        return redirect()->route('medical-orders.show', $medicalOrder)
            ->with('success', 'Medical order processed successfully. Sent to account.');
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

        $patches = Patch::with('inventories')
            ->get()
            ->map(function (Patch $patch) {
                $items = $patch->inventories->map(fn ($item) => [
                    'id' => $item->id,
                    'item_name' => $item->item_name,
                    'unit_price' => $item->unit_price,
                    'selling_price' => $item->selling_price,
                    'type_of_supply' => $item->type_of_supply->value,
                    'category' => $item->category,
                ]);

                return [
                    'id' => $patch->id,
                    'name' => $patch->name,
                    'total_unit_price' => $patch->total_unit_price,
                    'items' => $items->values(),
                ];
            });

        $medicineGroups = \App\Models\MedicineGroup::where('is_active', true)
            ->with(['items.inventory'])
            ->get()
            ->map(function ($group) {
                // Calculate total price
                $totalPrice = $group->custom_price;
                if (! $totalPrice) {
                    $totalPrice = $group->items->sum(function ($item) {
                        return ($item->inventory->selling_price ?? 0) * $item->quantity;
                    });
                }

                return [
                    'id' => $group->id,
                    'name' => $group->name,
                    'description' => $group->description,
                    'custom_price' => $group->custom_price,
                    'total_price' => $totalPrice,
                    'items' => $group->items->map(function ($item) {
                        return [
                            'id' => $item->inventory_id,
                            'item_name' => $item->inventory->item_name ?? 'Unknown',
                            'dosage' => $item->dosage,
                            'frequency' => $item->frequency,
                            'quantity' => $item->quantity,
                            'unit_price' => $item->inventory->unit_price ?? 0,
                            'selling_price' => $item->inventory->selling_price ?? 0,
                        ];
                    }),
                ];
            });

        $specialItems = \App\Models\SpecialItem::where('is_active', true)
            ->with(['items.inventory'])
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'description' => $item->description,
                    'unit_price' => (float) $item->unit_price,
                    'items' => $item->items->map(function ($subItem) {
                        return [
                            'id' => $subItem->inventory_id,
                            'item_name' => $subItem->inventory->item_name ?? 'Unknown',
                            'quantity' => $subItem->quantity,
                        ];
                    }),
                ];
            });

        $transformedOrder = [
            'id' => $medicalOrder->id,
            'patient_id' => $medicalOrder->patient_id,
            'patient_name' => $medicalOrder->patient?->full_name ?: 'Unknown Patient',
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
                // Use stored price if > 0, else fall back to inventory price
                $unitPrice = $item->unit_price > 0
                    ? (float) $item->unit_price
                    : ($item->inventory?->unit_price ?? 0);
                $sellingPrice = $item->selling_price > 0
                    ? (float) $item->selling_price
                    : ($item->inventory?->selling_price ?? 0);

                // For non-inventory service types with no price, look up from MedicalService
                if ($sellingPrice <= 0 && ! $item->inventory_id && in_array($item->item_type, ['procedure', 'imaging', 'consultation', 'therapy'])) {
                    $medicalService = \App\Models\MedicalService::where('name', $item->item_name)->first();
                    if ($medicalService) {
                        $unitPrice = (float) $medicalService->price;
                        $sellingPrice = (float) $medicalService->price;
                    }
                }

                // For special items with no price, look up from SpecialItem or MedicineGroup
                if ($sellingPrice <= 0 && $item->item_type === 'special_item') {
                    $specialItem = \App\Models\SpecialItem::where('name', $item->item_name)->first();
                    if ($specialItem) {
                        $unitPrice = (float) $specialItem->unit_price;
                        $sellingPrice = (float) $specialItem->unit_price;
                    }

                    if ($sellingPrice <= 0) {
                        $medicineGroup = \App\Models\MedicineGroup::where('name', $item->item_name)->first();
                        if ($medicineGroup) {
                            $price = (float) $medicineGroup->total_price;
                            $unitPrice = $price;
                            $sellingPrice = $price;
                        }
                    }
                }

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
                    'unit_price' => $unitPrice,
                    'selling_price' => $sellingPrice,
                ];
            }),
        ];

        return Inertia::render('MedicalOrders/Process', [
            'medicalOrder' => $transformedOrder,
            'labPanels' => $labPanels,
            'inventoryItems' => $inventoryItems,
            'rxMedicines' => $rxMedicines,
            'medicalServices' => $medicalServices,
            'patches' => $patches,
            'medicineGroups' => $medicineGroups,
            'specialItems' => $specialItems,
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
            'patient_name' => $medicalOrder->patient?->full_name ?: 'Unknown Patient',
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
                    'selling_price' => $item->selling_price > 0 ? $item->selling_price : $item->inventory?->selling_price,
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

        // Allow access if the order is in processed status
        if (! in_array($medicalOrder->status, [\App\Enums\MedicalOrderStatusEnum::PROCESSED])) {
            return redirect()->route('medical-orders.show', $medicalOrder)
                ->with('error', 'This medical order is not ready for completion.');
        }

        $medicalOrder->load(['patient.user', 'staff.user', 'orderItems.inventory', 'visit.medicalRecord', 'billings', 'visit.patient.user', 'visit.staff.user']);

        $transformedOrder = [
            'id' => $medicalOrder->id,
            'patient_id' => $medicalOrder->patient_id,
            'patient_name' => $medicalOrder->patient?->full_name ?: ($medicalOrder->visit?->patient?->full_name ?: 'Unknown Patient'),
            'staff_id' => $medicalOrder->staff_id,
            'staff_name' => $medicalOrder->staff?->user?->name ?? $medicalOrder->visit?->staff?->user?->name ?? 'Unknown Staff',
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
                    'selling_price' => $item->selling_price > 0 ? $item->selling_price : $item->inventory?->selling_price,
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
            // Check if a billing already exists for this order (revision flow)
            $existingBilling = \App\Models\Billing::where('medical_order_id', $medicalOrder->id)
                ->whereIn('status', ['pending', 'revision'])
                ->first();

            if ($existingBilling) {
                // Revision flow: recalculate amount and update existing billing
                $totalAmount = $billingService->calculateOrderTotal($medicalOrder);

                // Update the medical order
                $medicalOrder->update([
                    'status' => \App\Enums\MedicalOrderStatusEnum::COMPLETED,
                    'completed_at' => now(),
                ]);

                // Update all order items to completed
                $medicalOrder->orderItems()->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                ]);

                // Reduce inventory stock for any new items
                $billingService->reduceInventoryStock($medicalOrder);

                // Update billing amount and reset to pending
                $currentNotes = $existingBilling->notes ? $existingBilling->notes."\n\n" : '';
                $existingBilling->update([
                    'amount' => $totalAmount,
                    'status' => \App\Enums\BillingStatusEnum::PENDING,
                    'notes' => $currentNotes.'Recalculated after revision on '.now()->format('Y-m-d H:i').'. New amount: $'.number_format($totalAmount, 2),
                ]);

                Visit::where('id', $medicalOrder->visit_id)->update(['status' => Visit::STATUS_AWAITING_ACCOUNTANT]);

                if (auth()->user()->can('view_billing') || auth()->user()->hasRole('admin')) {
                    return redirect()->route('billings.show', $existingBilling)
                        ->with('success', 'Medical order revised and billing recalculated. New total: $'.number_format($totalAmount, 2));
                }

                return redirect()->route('medical-orders.show', $medicalOrder)
                    ->with('success', 'Medical order revised. Sent to account.');
            }

            // Normal flow: process the order and create billing
            $billing = $billingService->processOrderAndCreateBilling(
                $medicalOrder,
                'Processed and billed on '.now()->format('Y-m-d H:i')
            );

            // Generate medical record after billing is created
            $this->generateMedicalRecord($medicalOrder, $billing);

            Visit::where('id', $medicalOrder->visit_id)->update(['status' => Visit::STATUS_COMPLETED]);

            if (auth()->user()->can('view_billing') || auth()->user()->hasRole('admin')) {
                return redirect()->route('billings.show', $billing)
                    ->with('success', 'Medical order processed successfully. Billing created with total amount: $'.number_format((float) $billing->amount, 2));
            }

            return redirect()->route('medical-orders.show', $medicalOrder)
                ->with('success', 'Medical order processed successfully. Sent to account.');
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

        // If all items are now completed, mark the whole order as COMPLETED too
        $anyPending = $medicalOrder->orderItems()->whereNull('completed_at')->exists();
        if (! $anyPending) {
            $medicalOrder->update([
                'status' => \App\Enums\MedicalOrderStatusEnum::COMPLETED,
                'completed_at' => now(),
            ]);
        }

        return redirect()->back()->with('success', 'Order item completed successfully.');
    }

    public function saveLabResult(MedicalOrder $medicalOrder, \App\Models\MedicalOrderInventory $item, Request $request): RedirectResponse
    {
        $this->authorize('completeItem', $medicalOrder);

        if ($item->medical_order_id !== $medicalOrder->id) {
            abort(404);
        }

        $validated = $request->validate([
            'result_value' => 'nullable|string|max:255',
            'result_unit' => 'nullable|string|max:100',
            'result_notes' => 'nullable|string|max:2000',
        ]);

        $item->update([
            'result_value' => $validated['result_value'],
            'result_unit' => $validated['result_unit'],
            'result_notes' => $validated['result_notes'],
            'status' => \App\Enums\MedicalOrderStatusEnum::COMPLETED,
            'completed_at' => now(),
        ]);

        // If all items are now completed, mark the whole order COMPLETED too
        $anyPending = $medicalOrder->orderItems()->whereNull('completed_at')->exists();
        if (! $anyPending) {
            $medicalOrder->update([
                'status' => \App\Enums\MedicalOrderStatusEnum::COMPLETED,
                'completed_at' => now(),
            ]);
        }

        return redirect()->back()->with('success', 'Lab result saved successfully.');
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
                'appointment_date_time' => $medicalOrder->visit->appointment->appointment_date_time->setTimezone('Asia/Phnom_Penh')->format('d/m/y H:i'),
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

    /**
     * Generate medical record from medical order and billing information.
     */
    private function generateMedicalRecord(MedicalOrder $medicalOrder, \App\Models\Billing $billing): void
    {
        // Check if medical record already exists for this medical order
        $existingRecord = MedicalRecord::where('medical_order_id', $medicalOrder->id)->first();
        if ($existingRecord) {
            return; // Don't create duplicate records
        }

        // Load necessary relationships
        $medicalOrder->load(['visit.appointment', 'orderItems.inventory', 'staff.user', 'patient.user']);

        // Generate diagnosis from order details and visit/appointment notes
        $diagnosis = $this->generateDiagnosis($medicalOrder);

        // Generate treatment summary from order items
        $treatment = $this->generateTreatment($medicalOrder);

        // Generate comprehensive notes
        $notes = $this->generateMedicalNotes($medicalOrder, $billing);

        // Create medical record
        MedicalRecord::create([
            'appointment_id' => $medicalOrder->visit?->appointment?->id,
            'visit_id' => $medicalOrder->visit?->id,
            'medical_order_id' => $medicalOrder->id,
            'diagnosis' => $diagnosis,
            'treatment' => $treatment,
            'notes' => $notes,
            'date_of_service' => $medicalOrder->completed_at?->toDateString() ?? now()->toDateString(),
        ]);
    }

    /**
     * Generate diagnosis information from medical order context.
     */
    private function generateDiagnosis(MedicalOrder $medicalOrder): string
    {
        $diagnosisParts = [];

        // Get diagnosis from visit notes
        if ($medicalOrder->visit && $medicalOrder->visit->notes) {
            $diagnosisParts[] = "Visit Notes: {$medicalOrder->visit->notes}";
        }

        // Get diagnosis from appointment notes
        if ($medicalOrder->visit?->appointment && $medicalOrder->visit->appointment->notes) {
            $diagnosisParts[] = "Appointment Notes: {$medicalOrder->visit->appointment->notes}";
        }

        // Extract diagnosis from order details
        if ($medicalOrder->order_details) {
            $diagnosisParts[] = "Order Assessment: {$medicalOrder->order_details}";
        }

        // If no specific diagnosis found, generate based on ordered items
        if (empty($diagnosisParts)) {
            $itemTypes = $medicalOrder->orderItems->pluck('item_type')->unique();
            $diagnosisParts[] = 'Diagnostic evaluation including: '.implode(', ', $itemTypes->toArray());
        }

        return implode('; ', $diagnosisParts);
    }

    /**
     * Generate treatment summary from medical order items.
     */
    private function generateTreatment(MedicalOrder $medicalOrder): string
    {
        $treatmentParts = [];

        // Group items by type for better organization
        $groupedItems = $medicalOrder->orderItems->groupBy('item_type');

        foreach ($groupedItems as $itemType => $items) {
            $typeLabel = $this->getTreatmentTypeLabel($itemType);
            $itemDescriptions = [];

            foreach ($items as $item) {
                $description = $item->inventory?->item_name ?? $item->item_name ?? 'Unknown Item';

                // Add dosage/frequency/route if available
                $details = [];
                if ($item->dosage) {
                    $details[] = $item->dosage;
                }
                if ($item->frequency) {
                    $details[] = $item->frequency;
                }
                if ($item->route) {
                    $details[] = $item->route;
                }

                if ($details) {
                    $description .= ' ('.implode(', ', $details).')';
                }

                // Add quantity
                $quantity = $item->quantity_used ?? $item->quantity_required ?? 1;
                if ($quantity > 1) {
                    $description .= " - {$quantity} units";
                }

                $itemDescriptions[] = $description;
            }

            $treatmentParts[] = "{$typeLabel}: ".implode(', ', $itemDescriptions);
        }

        return implode('; ', $treatmentParts);
    }

    /**
     * Get human-readable label for treatment type.
     */
    private function getTreatmentTypeLabel(string $itemType): string
    {
        return match ($itemType) {
            'lab' => 'Laboratory Tests',
            'rx_medicine' => 'Prescribed Medications',
            'procedure' => 'Medical Procedures',
            'imaging' => 'Imaging Studies',
            'supply' => 'Medical Supplies',
            'therapy' => 'Therapy Services',
            'consultation' => 'Consultations',
            default => ucfirst($itemType),
        };
    }

    /**
     * Generate comprehensive medical notes.
     */
    private function generateMedicalNotes(MedicalOrder $medicalOrder, \App\Models\Billing $billing): string
    {
        $notes = [];

        // Add order priority if high
        if ($medicalOrder->priority && in_array($medicalOrder->priority->value, ['urgent', 'stat'])) {
            $notes[] = ucfirst($medicalOrder->priority->value).' priority order';
        }

        // Add completion information
        if ($medicalOrder->completed_at) {
            $notes[] = "Order completed on {$medicalOrder->completed_at->format('M j, Y \a\t g:i A')}";
        }

        // Add billing information
        $notes[] = "Total billed amount: \${$billing->amount}";
        $notes[] = "Billing status: {$billing->status->value}";

        // Add staff information
        if ($medicalOrder->staff) {
            $staffName = $medicalOrder->staff->user?->name ??
                ($medicalOrder->staff->first_name.' '.$medicalOrder->staff->last_name);
            $notes[] = "Ordering physician: {$staffName}";
        }

        // Add any additional order notes
        if ($medicalOrder->notes) {
            $notes[] = "Additional notes: {$medicalOrder->notes}";
        }

        return implode('; ', $notes);
    }

    /**
     * Generate medical record PDF report.
     */
    public function generateMedicalRecordReport(MedicalRecord $medicalRecord): \Illuminate\Http\Response
    {
        $this->authorize('view', $medicalRecord);

        $medicalRecord->load([
            'appointment',
            'visit.patient.user',
            'visit.staff.user',
            'medicalOrder.patient.user',
            'medicalOrder.staff.user',
            'medicalOrder.orderItems.inventory',
        ]);

        // Compile report data
        $report = [
            'record_info' => [
                'id' => $medicalRecord->id,
                'diagnosis' => $medicalRecord->diagnosis,
                'treatment' => $medicalRecord->treatment,
                'notes' => $medicalRecord->notes,
                'date_of_service' => $medicalRecord->date_of_service,
                'created_at' => $medicalRecord->created_at,
                'updated_at' => $medicalRecord->updated_at,
            ],
            'patient_info' => [
                'id' => $medicalRecord->medicalOrder?->patient?->id ?? $medicalRecord->visit?->patient?->id ?? 'N/A',
                'name' => $medicalRecord->medicalOrder?->patient?->user?->name ??
                    ($medicalRecord->medicalOrder?->patient?->first_name.' '.$medicalRecord->medicalOrder?->patient?->last_name) ??
                    $medicalRecord->visit?->patient?->user?->name ??
                    ($medicalRecord->visit?->patient?->first_name.' '.$medicalRecord->visit?->patient?->last_name) ??
                    'Unknown Patient',
                'date_of_birth' => $medicalRecord->medicalOrder?->patient?->date_of_birth ?? $medicalRecord->visit?->patient?->date_of_birth ?? 'N/A',
                'gender' => $medicalRecord->medicalOrder?->patient?->gender ?? $medicalRecord->visit?->patient?->gender ?? 'N/A',
                'phone_number' => $medicalRecord->medicalOrder?->patient?->phone_number ?? $medicalRecord->visit?->patient?->phone_number ?? 'N/A',
                'email' => $medicalRecord->medicalOrder?->patient?->email ??
                    $medicalRecord->medicalOrder?->patient?->user?->email ??
                    $medicalRecord->visit?->patient?->email ??
                    $medicalRecord->visit?->patient?->user?->email ?? 'N/A',
            ],
            'staff_info' => $medicalRecord->medicalOrder?->staff ? [
                'name' => $medicalRecord->medicalOrder->staff->user?->name ??
                    ($medicalRecord->medicalOrder->staff->first_name.' '.$medicalRecord->medicalOrder->staff->last_name),
                'role' => $medicalRecord->medicalOrder->staff->role?->name ?? 'Unknown',
            ] : ($medicalRecord->visit?->staff ? [
                'name' => $medicalRecord->visit->staff->user?->name ??
                    ($medicalRecord->visit->staff->first_name.' '.$medicalRecord->visit->staff->last_name),
                'role' => $medicalRecord->visit->staff->role?->name ?? 'Unknown',
            ] : null),
            'visit_info' => $medicalRecord->visit ? [
                'id' => $medicalRecord->visit->id,
                'visit_date_time' => $medicalRecord->visit->visit_date_time,
                'status' => $medicalRecord->visit->status->value,
                'staff_name' => $medicalRecord->visit->staff?->user?->name ??
                    ($medicalRecord->visit->staff?->first_name.' '.$medicalRecord->visit->staff?->last_name) ?? 'N/A',
                'notes' => $medicalRecord->visit->notes,
            ] : null,
            'appointment_info' => $medicalRecord->appointment ? [
                'id' => $medicalRecord->appointment->id,
                'appointment_date_time' => $medicalRecord->appointment->appointment_date_time,
                'reason_for_visit' => $medicalRecord->appointment->reason_for_visit,
                'status' => $medicalRecord->appointment->status->value,
            ] : null,
            'medical_orders' => $medicalRecord->medicalOrder ? [
                [
                    'id' => $medicalRecord->medicalOrder->id,
                    'order_type' => $medicalRecord->medicalOrder->order_type,
                    'status' => $medicalRecord->medicalOrder->status->value,
                    'priority' => $medicalRecord->medicalOrder->priority,
                    'order_details' => $medicalRecord->medicalOrder->order_details,
                    'ordered_at' => $medicalRecord->medicalOrder->ordered_at,
                ],
            ] : [],
            'medical_services' => [], // Could be populated if needed
        ];

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('medical-record-report', compact('report', 'medicalRecord'));

        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'DejaVu Sans',
            'dpi' => 96,
            'isPhpEnabled' => true,
        ]);

        $filename = 'medical-record-'.$medicalRecord->id.'-'.now()->format('Y-m-d').'.pdf';

        return $pdf->download($filename);
    }
}
