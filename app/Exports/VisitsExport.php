<?php

namespace App\Exports;

use App\Models\Visit;

class VisitsExport
{
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

        $visits = $query->orderByDesc('visit_date_time')->get();

        $csvContent = $this->generateCsv($visits);

        $filename = 'visits-'.now()->format('Y-m-d-H-i-s').'.csv';

        return response($csvContent)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="'.$filename.'"');
    }

    private function generateCsv($visits): string
    {
        $headers = [
            'Visit ID',
            'Patient ID',
            'Patient Title',
            'Patient Name',
            'Mobile Number',
            'Date',
            'Time',
            'Status',
            'Staff',
            'Doctor',
            'Notes',
            'Created At',
        ];

        $rows = [$headers];

        foreach ($visits as $visit) {
            $patientTitle = $visit->patient ? ($visit->patient->title ?? '') : '';
            $patientName = $visit->patient
                ? (trim(($visit->patient->name ?? '').' '.($visit->patient->surname ?? '')) ?: 'Patient #'.$visit->patient->id)
                : 'Unknown Patient';

            $rows[] = [
                $visit->id,
                $visit->patient ? $visit->patient->id : '',
                $patientTitle,
                $patientName,
                $this->formatPhone($visit->patient ? ($visit->patient->mobile_phone ?? '') : ''),
                $visit->visit_date_time ? \Carbon\Carbon::parse($visit->visit_date_time)->format('d/m/y') : '',
                $visit->visit_date_time ? \Carbon\Carbon::parse($visit->visit_date_time)->format('H:i') : '',
                ucfirst($visit->status instanceof \BackedEnum ? $visit->status->value : ($visit->status ?? '')),
                $visit->staff ? ($visit->staff->user?->name ?? $visit->staff->name ?? '') : 'Unassigned',
                $visit->doctor ? ($visit->doctor->user?->name ?? $visit->doctor->name ?? '') : '',
                $visit->notes ?? '',
                $visit->created_at ? $visit->created_at->format('d/m/y H:i') : '',
            ];
        }

        $csv = '';
        foreach ($rows as $row) {
            $csv .= implode(',', array_map(function ($field) {
                $field = (string) $field;
                if (str_contains($field, ',') || str_contains($field, '"') || str_contains($field, "\n")) {
                    return '"'.str_replace('"', '""', $field).'"';
                }

                return $field;
            }, $row))."\n";
        }

        return $csv;
    }

    private function formatPhone(?string $phone): string
    {
        if (! $phone) {
            return '';
        }

        return '="'.$phone.'"';
    }
}
