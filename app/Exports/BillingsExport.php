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
        $csvContent = $this->generateCsvFromRows($rows);
        $html = $this->generateHtml($rows, $filename, $csvContent);

        return response($html)->header('Content-Type', 'text/html; charset=UTF-8');
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

    private function generateHtml(array $rows, string $filename, string $csvContent): string
    {
        $headers = ['Bill No', 'Billing Date', 'Patient ID', 'Patient Name', 'Medical Order Items', 'Total Amount', 'Status'];
        $keys = ['bill_no', 'date', 'patient_id', 'patient', 'items', 'amount', 'status'];

        $thead = '<tr>'.implode('', array_map(fn ($h) => '<th>'.htmlspecialchars($h).'</th>', $headers)).'</tr>';

        $tbody = '';
        foreach ($rows as $row) {
            $tbody .= '<tr>'.implode('', array_map(fn ($k) => '<td>'.htmlspecialchars($row[$k]).'</td>', $keys)).'</tr>';
        }

        $total = count($rows);
        $csvBase64 = base64_encode($csvContent);

        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Billings Export</title>
<style>
  body { font-family: Arial, sans-serif; padding: 24px; background: #f9fafb; color: #111; }
  h1 { font-size: 20px; margin-bottom: 4px; }
  p { color: #555; font-size: 14px; margin-bottom: 16px; }
  .toolbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }
  .count { font-size: 13px; color: #666; }
  a.download-btn {
    background: #1e40af; color: #fff; padding: 8px 16px; border-radius: 6px;
    text-decoration: none; font-size: 13px; font-weight: 600;
  }
  a.download-btn:hover { background: #1d3899; }
  table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,.1); }
  th { background: #1e40af; color: #fff; padding: 10px 12px; text-align: left; font-size: 13px; }
  td { padding: 9px 12px; font-size: 13px; border-bottom: 1px solid #e5e7eb; }
  tr:last-child td { border-bottom: none; }
  tr:hover td { background: #f0f4ff; }
</style>
</head>
<body>
<h1>Billings Export</h1>
<p>Generated on {$this->now()}</p>
<div class="toolbar">
  <span class="count">{$total} record(s)</span>
  <a class="download-btn" href="data:text/csv;base64,{$csvBase64}" download="{$filename}">Download CSV</a>
</div>
<table>
  <thead>{$thead}</thead>
  <tbody>{$tbody}</tbody>
</table>
</body>
</html>
HTML;
    }

    private function now(): string
    {
        return now()->format('d/m/Y H:i:s');
    }

    private function generateCsvFromRows(array $rows): string
    {
        $headers = ['Bill No', 'Billing Date', 'Patient ID', 'Patient Name', 'Medical Order Items', 'Total Amount', 'Status'];
        $keys = ['bill_no', 'date', 'patient_id', 'patient', 'items', 'amount', 'status'];

        $allRows = [array_combine($keys, $headers)];
        foreach ($rows as $row) {
            $allRows[] = $row;
        }

        $csv = '';
        foreach ($allRows as $row) {
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
