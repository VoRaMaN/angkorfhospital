<?php

namespace App\Exports;

use App\Models\Billing;

class BillingsExport
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function download()
    {
        $query = Billing::with(['patient', 'medicalOrder.orderItems.inventory']);

        if (! empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('patient_id', 'like', "%{$search}%")
                    ->orWhereHas('patient', function ($pq) use ($search) {
                        $pq->where('name', 'like', "%{$search}%")
                            ->orWhere('surname', 'like', "%{$search}%");
                    })
                    ->orWhere('appointment_id', 'like', "%{$search}%")
                    ->orWhere('visit_id', 'like', "%{$search}%")
                    ->orWhere('medical_order_id', 'like', "%{$search}%");
            });
        }

        if (! empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (! empty($this->filters['start_date'])) {
            $query->whereDate('billing_date', '>=', $this->filters['start_date']);
        }

        if (! empty($this->filters['end_date'])) {
            $query->whereDate('billing_date', '<=', $this->filters['end_date']);
        }

        if (empty($this->filters['start_date']) && empty($this->filters['end_date'])) {
            $query->whereDate('billing_date', today());
        }

        $billings = $query->orderBy('billing_date')->get();

        $rows = $this->buildRows($billings);
        $filename = 'billings-'.now()->format('Y-m-d-H-i-s').'.csv';
        $csv = $this->generateCsv($rows);

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    private function buildRows($billings): array
    {
        $rows = [];
        foreach ($billings as $billing) {
            $patient = $billing->patient;
            $title = $patient && $patient->title ? $patient->title.' ' : '';
            $name = $patient ? trim(($patient->name ?? '').' '.($patient->surname ?? '')) : 'Unknown Patient';

            $itemNames = [];
            if ($billing->medicalOrder && $billing->medicalOrder->orderItems->isNotEmpty()) {
                foreach ($billing->medicalOrder->orderItems as $item) {
                    $itemNames[] = $item->item_name ?? $item->inventory?->item_name ?? 'Unknown Item';
                }
            }

            $rows[] = [
                'bill_no' => $billing->bill_no ?? '',
                'date' => $billing->billing_date ? $billing->billing_date->format('d/m/Y') : '',
                'patient_id' => $patient ? $patient->id : '',
                'patient' => $title.$name,
                'items' => implode(', ', array_unique($itemNames)),
                'amount' => number_format((float) $billing->amount, 2),
                'status' => $billing->status instanceof \App\Enums\BillingStatusEnum ? ucfirst($billing->status->value) : ucfirst($billing->status),
            ];
        }

        return $rows;
    }

    private function generateCsv(array $rows): string
    {
        $headers = ['Bill No', 'Billing Date', 'Patient ID', 'Patient Name', 'Medical Order Items', 'Total Amount', 'Status'];
        $keys = ['bill_no', 'date', 'patient_id', 'patient', 'items', 'amount', 'status'];

        $escape = function ($field) {
            $field = str_replace('"', '""', (string) $field);
            if (str_contains($field, ',') || str_contains($field, '"') || str_contains($field, "\n")) {
                $field = '"'.$field.'"';
            }

            return $field;
        };

        $csv = implode(',', array_map($escape, $headers))."\n";
        foreach ($rows as $row) {
            $csv .= implode(',', array_map($escape, array_map(fn ($k) => $row[$k], $keys)))."\n";
        }

        return $csv;
    }
}
