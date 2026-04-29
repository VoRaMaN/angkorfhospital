<?php

namespace App\Exports;

use App\Models\Visit;

class VisitsExport
{
    use \App\Traits\RendersExportHtml;

    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function download()
    {
        $query = Visit::with(['patient', 'staff.user', 'doctor.user']);

        if (! empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->whereHas('patient', function ($patientQuery) use ($search) {
                    $patientQuery->where('name', 'like', '%'.$search.'%')
                        ->orWhere('surname', 'like', '%'.$search.'%');
                })
                    ->orWhereHas('patient.user', function ($patientQuery) use ($search) {
                        $patientQuery->where('name', 'like', '%'.$search.'%');
                    })
                    ->orWhere('status', 'like', '%'.$search.'%')
                    ->orWhere('notes', 'like', '%'.$search.'%');
            });
        }

        if (! empty($this->filters['patient'])) {
            $query->where('patient_id', $this->filters['patient']);
        }

        $hasDateFilter = ! empty($this->filters['date']) || ! empty($this->filters['from']) || ! empty($this->filters['to']);

        if (! empty($this->filters['date'])) {
            try {
                $query->whereDate('visit_date_time', $this->filters['date']);
            } catch (\Exception $e) {
                // Ignore invalid date formats
            }
        }

        if (! empty($this->filters['from'])) {
            try {
                $query->whereDate('visit_date_time', '>=', $this->filters['from']);
            } catch (\Exception $e) {
                // Ignore invalid date formats
            }
        }

        if (! empty($this->filters['to'])) {
            try {
                $query->whereDate('visit_date_time', '<=', $this->filters['to']);
            } catch (\Exception $e) {
                // Ignore invalid date formats
            }
        }

        if (! $hasDateFilter) {
            $query->whereDate('visit_date_time', today());
        }

        $visits = $query->orderByDesc('visit_date_time')->get();

        $headers = ['Visit ID', 'Patient ID', 'Name', 'Mobile', 'Date', 'Time', 'Status', 'Doctor', 'Notes', 'Created At'];
        $rows = $this->buildRows($visits);
        $filename = 'visits-'.now()->format('Y-m-d-H-i-s').'.csv';

        return $this->renderExportHtml('Visits Export', $headers, $rows, $this->buildCsvString($headers, $rows), $filename);
    }

    private function buildRows($visits): array
    {
        $rows = [];
        foreach ($visits as $visit) {
            $patientTitle = $visit->patient ? ($visit->patient->title ?? '') : '';
            $patientName = $visit->patient
                ? (trim(($visit->patient->name ?? '').' '.($visit->patient->surname ?? '')) ?: 'Patient #'.$visit->patient->id)
                : 'Unknown Patient';

            $rows[] = [
                $visit->id,
                $visit->patient ? $visit->patient->id : '',
                trim($patientTitle.' '.$patientName),
                $visit->patient ? ($visit->patient->mobile_phone ?? '') : '',
                $visit->visit_date_time ? \Carbon\Carbon::parse($visit->visit_date_time)->format('d/m/y') : '',
                $visit->visit_date_time ? \Carbon\Carbon::parse($visit->visit_date_time)->format('H:i') : '',
                ucfirst($visit->status instanceof \BackedEnum ? $visit->status->value : ($visit->status ?? '')),
                $visit->doctor ? ($visit->doctor->user?->name ?? $visit->doctor->name ?? '') : '',
                $visit->notes ?? '',
                $visit->created_at ? $visit->created_at->format('d/m/y H:i') : '',
            ];
        }

        return $rows;
    }
}
