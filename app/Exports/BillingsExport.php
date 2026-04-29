<?php

namespace App\Exports;

use App\Models\Billing;

class BillingsExport
{
    use \App\Traits\RendersExportHtml;

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

        $headers = ['Bill No', 'Billing Date', 'Patient ID', 'Patient Name', 'Medical Order Items', 'Total Amount', 'Status'];
        $rows = $this->buildRows($billings);
        $filename = 'billings-'.now()->format('Y-m-d-H-i-s').'.csv';

        return $this->renderExportHtml('Billings Export', $headers, $rows, $this->buildCsvString($headers, $rows), $filename);
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
                $billing->bill_no ?? '',
                $billing->billing_date ? $billing->billing_date->format('d/m/Y') : '',
                $patient ? $patient->id : '',
                $title.$name,
                implode(', ', array_unique($itemNames)),
                number_format((float) $billing->amount, 2),
                $billing->status instanceof \App\Enums\BillingStatusEnum ? ucfirst($billing->status->value) : ucfirst($billing->status),
            ];
        }

        return $rows;
    }
}
