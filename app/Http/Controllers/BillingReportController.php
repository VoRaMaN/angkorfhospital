<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BillingReportController extends Controller
{
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
                                'amount' => $billing->amount,
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
        ]);

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $groupBy = $request->input('group_by');

        $billings = Billing::with(['patient.user'])
            ->whereBetween('billing_date', [$startDate, $endDate])
            ->orderBy('billing_date', 'asc')
            ->get();

        // Create CSV
        $filename = 'billing_report_'.$startDate.'_to_'.$endDate.'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'inline; filename="'.$filename.'"',
        ];

        $callback = function () use ($billings, $groupBy) {
            $file = fopen('php://output', 'w');

            // Add CSV headers based on grouping
            if ($groupBy === 'year') {
                fputcsv($file, ['Year', 'Patient Name', 'Total Bills', 'Total Amount', 'Status']);
            } elseif ($groupBy === 'month') {
                fputcsv($file, ['Year-Month', 'Patient Name', 'Total Bills', 'Total Amount', 'Status']);
            } else {
                fputcsv($file, ['Date', 'Bill No', 'Patient Name', 'Amount', 'Status', 'Payment Method']);
            }

            if ($groupBy === 'day') {
                // Day-by-day detailed view
                foreach ($billings as $billing) {
                    fputcsv($file, [
                        $billing->billing_date->format('d/m/y'),
                        $billing->bill_no,
                        $billing->patient?->user?->name ?? 'Unknown',
                        number_format($billing->amount, 2),
                        $billing->status,
                        $billing->payment_method ?? 'N/A',
                    ]);
                }
            } elseif ($groupBy === 'month') {
                // Group by year-month and patient
                $grouped = $billings->groupBy(function ($billing) {
                    return $billing->billing_date->format('Y-m').'_'.$billing->patient_id;
                });

                foreach ($grouped as $key => $group) {
                    $parts = explode('_', $key);
                    $yearMonth = $parts[0];
                    $patient = $group->first()->patient;

                    fputcsv($file, [
                        $yearMonth,
                        $patient?->user?->name ?? 'Unknown',
                        $group->count(),
                        number_format($group->sum('amount'), 2),
                        'Mixed',
                    ]);
                }
            } else {
                // Group by year and patient
                $grouped = $billings->groupBy(function ($billing) {
                    return $billing->billing_date->format('Y').'_'.$billing->patient_id;
                });

                foreach ($grouped as $key => $group) {
                    $parts = explode('_', $key);
                    $year = $parts[0];
                    $patient = $group->first()->patient;

                    fputcsv($file, [
                        $year,
                        $patient?->user?->name ?? 'Unknown',
                        $group->count(),
                        number_format($group->sum('amount'), 2),
                        'Mixed',
                    ]);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
