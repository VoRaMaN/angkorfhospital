<?php

namespace App\Exports;

use App\Models\ActivityLog;

class ActivityLogExport
{
    /** @var array<string, mixed> */
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function download(): \Illuminate\Http\Response
    {
        $query = ActivityLog::query()->with('user:id,name,email')->orderByDesc('created_at');

        if (! empty($this->filters['user_id'])) {
            $query->where('user_id', (int) $this->filters['user_id']);
        }

        if (! empty($this->filters['action'])) {
            $query->where('action', $this->filters['action']);
        }

        if (! empty($this->filters['subject_type'])) {
            $query->where('subject_type', $this->filters['subject_type']);
        }

        if (! empty($this->filters['date_from'])) {
            $query->whereDate('created_at', '>=', $this->filters['date_from']);
        }

        if (! empty($this->filters['date_to'])) {
            $query->whereDate('created_at', '<=', $this->filters['date_to']);
        }

        $logs = $query->get();

        $csvContent = $this->generateCsv($logs);

        $filename = 'activity-log-'.now()->format('Y-m-d-H-i-s').'.csv';

        return response($csvContent)
            ->header('Content-Type', 'text/plain; charset=UTF-8')
            ->header('Content-Disposition', 'inline; filename="'.$filename.'"');
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Collection<int, ActivityLog>  $logs
     */
    private function generateCsv($logs): string
    {
        $headers = [
            'ID',
            'User',
            'Action',
            'Description',
            'Model',
            'Model ID',
            'IP Address',
            'User Agent',
            'Date & Time',
        ];

        $rows = [$headers];

        foreach ($logs as $log) {
            $rows[] = [
                $log->id,
                $log->user?->name ?? 'System',
                $log->action,
                $log->description,
                $log->subject_type ? class_basename($log->subject_type) : '',
                $log->subject_id ?? '',
                $log->ip_address ?? '',
                $log->user_agent ?? '',
                $log->created_at?->format('Y-m-d H:i:s') ?? '',
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
}
