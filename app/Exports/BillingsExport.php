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
        $query = Billing::with(['patient', 'doctor.user']);

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

        $csvContent = $this->generateCsv($billings);

        $filename = 'billings-'.now()->format('Y-m-d-H-i-s').'.csv';

        return response($csvContent)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="'.$filename.'"');
    }

    private function generateCsv($billings)
    {
        $headers = [
            'Bill No',
            'Billing Date',
            'Due Date',
            'Patient ID',
            'Patient Name',
            'Appointment',
            'Visit',
            'Medical Order',
            'Total Amount',
            'Status',
            'Notes',
        ];

        $rows = [$headers];

        foreach ($billings as $billing) {
            $patient = $billing->patient;
            $title = $patient && $patient->title ? $patient->title.' ' : '';
            $name = $patient ? trim(($patient->name ?? '').' '.($patient->surname ?? '')) : 'Unknown Patient';

            $rows[] = [
                $billing->bill_no ?? '',
                $billing->billing_date ? $billing->billing_date->format('d/m/Y') : '',
                $billing->billing_date ? $billing->billing_date->addDays(30)->format('d/m/Y') : '',
                $patient ? $patient->id : '',
                $title.$name,
                $billing->appointment_id ? 'Yes' : 'No',
                $billing->visit_id ? 'Yes' : 'No',
                $billing->medical_order_id ? 'Yes' : 'No',
                number_format((float) $billing->amount, 2),
                $billing->status instanceof \App\Enums\BillingStatusEnum ? ucfirst($billing->status->value) : ucfirst($billing->status),
                $billing->notes ?? '',
            ];
        }

        $csv = '';
        foreach ($rows as $row) {
            $csv .= implode(',', array_map(function ($field) {
                $field = str_replace('"', '""', (string) $field);
                if (str_contains($field, ',') || str_contains($field, '"') || str_contains($field, "\n")) {
                    $field = '"'.$field.'"';
                }

                return $field;
            }, $row))."\n";
        }

        return $csv;
    }
}
