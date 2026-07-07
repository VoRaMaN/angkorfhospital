<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use App\Traits\RendersExportHtml;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BillingReportController extends Controller
{
    use RendersExportHtml;

    public function index(Request $request): Response
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $billingData = [];
        $summary = [
            'total_revenue' => 0,
            'total_bills' => 0,
            'average_bill' => 0,
            'paid_count' => 0,
            'unpaid_count' => 0,
        ];

        if ($startDate && $endDate) {
            // Get all billings within the date range
            $billings = Billing::with(['patient.user'])
                ->whereBetween('billing_date', [$startDate, $endDate])
                ->orderBy('billing_date', 'desc')
                ->get();

            // Calculate summary
            $summary['total_bills'] = $billings->count();
            $summary['total_revenue'] = $billings->sum('amount');
            $summary['average_bill'] = $summary['total_bills'] > 0 ? $summary['total_revenue'] / $summary['total_bills'] : 0;
            $summary['paid_count'] = $billings->where('status', 'paid')->count();
            $summary['unpaid_count'] = $billings->whereIn('status', ['pending', 'overdue'])->count();

            // Group by patient
            $billingData = $billings->groupBy('patient_id')
                ->map(function ($patientBillings, $patientId) {
                    $patient = $patientBillings->first()->patient;

                    return [
                        'patient_id' => $patientId,
                        'patient_name' => $patient?->full_name ?: 'Unknown Patient',
                        'total_amount' => $patientBillings->sum('amount'),
                        'bill_count' => $patientBillings->count(),
                        'billings' => $patientBillings->map(function ($billing) {
                            return [
                                'id' => $billing->id,
                                'bill_no' => $billing->bill_no,
                                'billing_date' => $billing->billing_date->format('d/m/y'),
                                'amount' => (float) $billing->amount,
                                'status' => $billing->status,
                                'payment_method' => $billing->payment_method ?? 'N/A',
                            ];
                        })->values(),
                    ];
                })
                ->values();
        }

        return Inertia::render('Reports/BillingReport', [
            'billingData' => $billingData,
            'summary' => $summary,
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
        ]);
    }

    public function export(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'group_by' => 'required|in:year,month,day',
            'status' => 'nullable|string|in:'.implode(',', array_column(\App\Enums\BillingStatusEnum::cases(), 'value')),
        ]);

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $groupBy = $request->input('group_by');
        $status = $request->input('status');

        $billings = Billing::with(['patient.user'])
            ->whereBetween('billing_date', [$startDate, $endDate])
            ->when($status, fn ($query) => $query->where('status', $status))
            ->orderBy('billing_date', 'asc')
            ->get();

        if ($groupBy === 'day') {
            $headers = ['Date', 'Bill No', 'Patient Name', 'Amount', 'Status', 'Payment Method'];
            $rows = $billings->map(fn ($billing) => [
                $billing->billing_date->format('d/m/y'),
                $billing->bill_no,
                $billing->patient?->full_name ?: 'Unknown',
                number_format($billing->amount, 2),
                $billing->status instanceof \BackedEnum ? $billing->status->value : $billing->status,
                $billing->payment_method ?? 'N/A',
            ])->toArray();
        } elseif ($groupBy === 'month') {
            $headers = ['Year-Month', 'Patient Name', 'Total Bills', 'Total Amount'];
            $rows = [];
            $grouped = $billings->groupBy(fn ($billing) => $billing->billing_date->format('Y-m').'_'.$billing->patient_id);
            foreach ($grouped as $key => $group) {
                $yearMonth = explode('_', $key)[0];
                $patient = $group->first()->patient;
                $rows[] = [$yearMonth, $patient?->full_name ?: 'Unknown', $group->count(), number_format($group->sum('amount'), 2)];
            }
        } else {
            $headers = ['Year', 'Patient Name', 'Total Bills', 'Total Amount'];
            $rows = [];
            $grouped = $billings->groupBy(fn ($billing) => $billing->billing_date->format('Y').'_'.$billing->patient_id);
            foreach ($grouped as $key => $group) {
                $year = explode('_', $key)[0];
                $patient = $group->first()->patient;
                $rows[] = [$year, $patient?->full_name ?: 'Unknown', $group->count(), number_format($group->sum('amount'), 2)];
            }
        }

        $filename = 'billing_report_'.($status ? $status.'_' : '').$startDate.'_to_'.$endDate.'.csv';

        return $this->renderExportHtml('Billing Report Export', $headers, $rows, $this->buildCsvString($headers, $rows), $filename);
    }
}
