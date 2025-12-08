<?php

namespace App\Exports;

use App\Models\Appointment;

class AppointmentsExport
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function download()
    {
        $query = Appointment::with(['patient.user', 'staff.user', 'patient']);

        // Apply search filter
        if (! empty($this->filters['search'])) {
            $search = $this->filters['search'];
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
                    })
                    ->orWhereHas('staff', function ($staffQuery) use ($search) {
                        $staffQuery->where('first_name', 'like', '%'.$search.'%')
                            ->orWhere('last_name', 'like', '%'.$search.'%');
                    })
                    ->orWhere('appointment_type', 'like', '%'.$search.'%')
                    ->orWhere('status', 'like', '%'.$search.'%')
                    ->orWhere('reason_for_visit', 'like', '%'.$search.'%');
            });
        }

        // Apply date range filters
        if (! empty($this->filters['from'])) {
            try {
                $query->whereDate('appointment_date_time', '>=', $this->filters['from']);
            } catch (\Exception $e) {
                // Ignore invalid date formats
            }
        }

        if (! empty($this->filters['to'])) {
            try {
                $query->whereDate('appointment_date_time', '<=', $this->filters['to']);
            } catch (\Exception $e) {
                // Ignore invalid date formats
            }
        }

        $appointments = $query->get();

        // Create CSV content
        $csvContent = $this->generateCsv($appointments);

        $filename = 'appointments-'.now()->format('Y-m-d-H-i-s').'.csv';

        return response($csvContent)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="'.$filename.'"');
    }

    private function generateCsv($appointments)
    {
        $headers = [
            'ID',
            'Time',
            'Date',
            'Patient Name',
            'Mobile Number',
            'Doctor',
            'Status',
            'Appointment Type',
            'Duration (minutes)',
            'Reason for Visit',
            'Created At',
        ];

        $rows = [$headers];

        foreach ($appointments as $appointment) {
            $rows[] = [
                $appointment->id,
                $appointment->appointment_date_time ? \Carbon\Carbon::parse($appointment->appointment_date_time)->format('H:i') : '',
                $appointment->appointment_date_time ? \Carbon\Carbon::parse($appointment->appointment_date_time)->format('Y-m-d') : '',
                $appointment->patient ? ($appointment->patient->user ? $appointment->patient->user->name : ($appointment->patient->name.' '.$appointment->patient->surname)) : 'Unknown Patient',
                $appointment->patient ? $appointment->patient->mobile_phone : '',
                $appointment->staff ? ($appointment->staff->user ? $appointment->staff->user->name : ($appointment->staff->first_name.' '.$appointment->staff->last_name)) : 'Unknown Staff',
                ucfirst($appointment->status->value),
                ucfirst(str_replace('_', ' ', $appointment->appointment_type?->value ?? 'consultation')),
                $appointment->duration_minutes ?? 30,
                $appointment->reason_for_visit ?? '',
                $appointment->created_at ? $appointment->created_at->format('Y-m-d H:i:s') : '',
            ];
        }

        // Convert to CSV string
        $csv = '';
        foreach ($rows as $row) {
            $csv .= implode(',', array_map(function ($field) {
                // Escape fields containing commas, quotes, or newlines
                if (str_contains($field, ',') || str_contains($field, '"') || str_contains($field, "\n")) {
                    return '"'.str_replace('"', '""', $field).'"';
                }

                return $field;
            }, $row))."\n";
        }

        return $csv;
    }
}
