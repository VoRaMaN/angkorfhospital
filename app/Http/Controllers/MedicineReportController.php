<?php

namespace App\Http\Controllers;

use App\Enums\MedicalOrderStatusEnum;
use App\Models\MedicalOrder;
use App\Models\MedicalOrderInventory;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MedicineReportController extends Controller
{
    public function index(Request $request): Response
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $medicineUsage = [];

        if ($startDate && $endDate) {
            // Get all RX medicine items within the date range
            $medicineUsage = MedicalOrderInventory::with(['medicalOrder.patient.user', 'medicalOrder.visit'])
                ->where('item_type', 'rx_medicine')
                ->whereHas('medicalOrder', function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('ordered_at', [$startDate.' 00:00:00', $endDate.' 23:59:59']);
                })
                ->get()
                ->groupBy(function ($item) {
                    return $item->medicalOrder->patient_id;
                })
                ->map(function ($items, $patientId) {
                    $patient = $items->first()->medicalOrder->patient;

                    return [
                        'patient_id' => $patientId,
                        'patient_name' => $patient?->full_name ?: 'Unknown Patient',
                        'medicines' => $items->map(function ($item) {
                            return [
                                'medicine_name' => $item->item_name,
                                'quantity' => $item->quantity_required,
                                'type' => $item->item_type,
                                'date' => $item->medicalOrder->ordered_at->format('d/m/y'),
                                'unit_price' => $item->unit_price,
                                'selling_price' => $item->selling_price,
                                'total_cost' => $item->selling_price * $item->quantity_required,
                            ];
                        })->values(),
                        'total_medicines' => $items->sum('quantity_required'),
                        'total_cost' => $items->sum(function ($item) {
                            return $item->selling_price * $item->quantity_required;
                        }),
                    ];
                })
                ->values();
        }

        // Always load today's dispensing queue
        $todayDispensing = MedicalOrder::with(['patient', 'orderItems'])
            ->whereHas('orderItems', function ($q) {
                $q->where('item_type', 'rx_medicine');
            })
            ->whereDate('ordered_at', today())
            ->whereNotIn('status', [MedicalOrderStatusEnum::CANCEL, MedicalOrderStatusEnum::REJECTED])
            ->latest('ordered_at')
            ->get()
            ->map(function ($order) {
                $patient = $order->patient;
                $rxItems = $order->orderItems->where('item_type', 'rx_medicine');
                $allFinished = $rxItems->every(fn ($item) => $item->status === MedicalOrderStatusEnum::COMPLETED);

                return [
                    'id' => $order->id,
                    'patient_id' => $patient?->id,
                    'patient_name' => $patient?->full_name ?: 'Unknown Patient',
                    'status' => $order->status->value,
                    'status_label' => $order->status->label(),
                    'status_color' => $order->status->color(),
                    'is_finished' => $allFinished,
                    'ordered_at' => $order->ordered_at?->format('H:i'),
                    'medicines' => $rxItems->map(fn ($item) => [
                        'medicine_name' => $item->item_name,
                        'quantity' => $item->quantity_required,
                        'unit_price' => $item->unit_price,
                        'selling_price' => $item->selling_price,
                        'total_cost' => $item->selling_price * $item->quantity_required,
                        'date' => $order->ordered_at?->format('d/m/y'),
                    ])->values(),
                ];
            });

        return Inertia::render('Reports/MedicineReport', [
            'medicineUsage' => $medicineUsage,
            'todayDispensing' => $todayDispensing,
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
        ]);
    }

    public function finish(int $id): \Illuminate\Http\RedirectResponse
    {
        $order = MedicalOrder::with('orderItems.inventory')->findOrFail($id);

        $inventoryService = app(\App\Services\InventoryService::class);

        foreach ($order->orderItems as $item) {
            if ($item->item_type !== 'rx_medicine' || $item->status === MedicalOrderStatusEnum::COMPLETED) {
                continue;
            }

            // Deduct medicine from stock before marking completed
            if ($item->inventory_id && $item->inventory) {
                $quantity = $item->quantity_required ?? 1;
                if ($item->inventory->quantity >= $quantity) {
                    $inventoryService->removeStock(
                        $item->inventory,
                        $quantity,
                        "Dispensed to patient via Medical Order #{$order->id}"
                    );
                }
            }

            $item->update([
                'status' => MedicalOrderStatusEnum::COMPLETED->value,
                'completed_at' => now(),
            ]);
        }

        // If all order items are now completed, mark the whole order as COMPLETED too
        $order->refresh();
        $anyPending = $order->orderItems()->whereNull('completed_at')->exists();
        if (! $anyPending) {
            $order->update([
                'status' => MedicalOrderStatusEnum::COMPLETED,
                'completed_at' => now(),
            ]);
        }

        return back()->with('success', 'Medicine handed over to patient and stock updated.');
    }

    public function export(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $patientId = $request->input('patient_id');

        if (! $startDate || ! $endDate) {
            return back()->with('error', 'Please select date range');
        }

        $query = MedicalOrderInventory::with(['medicalOrder.patient.user', 'medicalOrder.visit'])
            ->where('item_type', 'rx_medicine')
            ->whereHas('medicalOrder', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('ordered_at', [$startDate.' 00:00:00', $endDate.' 23:59:59']);
            });

        if ($patientId) {
            $query->whereHas('medicalOrder', function ($query) use ($patientId) {
                $query->where('patient_id', $patientId);
            });
        }

        $items = $query->get();

        // Create CSV
        $filename = 'medicine_report_'.$startDate.'_to_'.$endDate.'.csv';
        $headers = [
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Content-Disposition' => 'inline; filename="'.$filename.'"',
        ];

        $callback = function () use ($items) {
            $file = fopen('php://output', 'w');

            // Add CSV headers
            fputcsv($file, ['Patient Name', 'Medicine Name', 'Quantity', 'Type', 'Date', 'Unit Price', 'Selling Price', 'Total Cost']);

            // Add data
            foreach ($items as $item) {
                $patient = $item->medicalOrder->patient;
                fputcsv($file, [
                    $patient?->full_name ?: 'Unknown Patient',
                    $item->item_name,
                    $item->quantity_required,
                    $item->item_type,
                    $item->medicalOrder->ordered_at->format('d/m/y'),
                    $item->unit_price,
                    $item->selling_price,
                    $item->selling_price * $item->quantity_required,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
