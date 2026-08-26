<?php

namespace App\Http\Controllers;

use App\Models\CbcReport;
use App\Models\FetReport;
use App\Models\HormoneReport;
use App\Models\IuiReport;
use App\Models\OpuReport;
use App\Models\SaReport;
use App\Models\SemenAnalysisReport;
use App\Models\SpermFreezingReport;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

class LabResultController extends Controller
{
    private const TYPE_LABELS = [
        'hormone' => 'Hormone',
        'sa' => 'SA',
        'semen_analysis' => 'Semen Analysis',
        'cbc' => 'CBC',
        'iui' => 'IUI',
        'opu' => 'OPU',
        'fet' => 'FET',
        'sperm_freezing' => 'Sperm Freezing',
    ];

    /**
     * Completed lab results, merged across the 8 report tables. Each table
     * has a different shape (some have a real patient() relation, OPU is
     * bi-patient, FET has no relation at all — just denormalized name/HN
     * columns), so results are normalized into one common row shape here
     * rather than attempted as a single SQL UNION across incompatible
     * schemas. Sorting/filtering by date uses created_at (the one field
     * guaranteed present and consistently typed on every table) rather than
     * each type's own "reported_date"-style field, which is free-text on
     * some tables (Hormone, CBC) and absent entirely on others (OPU, FET).
     */
    public function index(Request $request): Response
    {
        $search = trim((string) $request->input('search', ''));
        $type = $request->input('type') ?: null;
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $direction = $request->input('direction') === 'asc' ? 'asc' : 'desc';
        $perPage = 20;
        $page = max(1, (int) $request->input('page', 1));

        $rows = collect();

        $standardSources = [
            'hormone' => [HormoneReport::class, 'reported_date', fn ($id) => route('hormone-reports.pdf', $id)],
            'sa' => [SaReport::class, 'reported_date', fn ($id) => route('sa-reports.show', $id)],
            'semen_analysis' => [SemenAnalysisReport::class, 'reported_date', fn ($id) => route('semen-analysis-reports.show', $id)],
            'cbc' => [CbcReport::class, 'reported_date', fn ($id) => route('cbc-reports.show', $id)],
            'iui' => [IuiReport::class, 'reported_date', fn ($id) => route('iui-reports.show', $id)],
            'sperm_freezing' => [SpermFreezingReport::class, 'reported_date', fn ($id) => route('sperm-freezing-reports.pdf', $id)],
        ];

        foreach ($standardSources as $key => [$modelClass, $dateField, $urlResolver]) {
            if ($type && $type !== $key) {
                continue;
            }
            $rows = $rows->merge($this->fetchStandard($modelClass, $key, $dateField, $urlResolver, $search, $startDate, $endDate));
        }

        if (! $type || $type === 'fet') {
            $rows = $rows->merge($this->fetchFet($search, $startDate, $endDate));
        }

        if (! $type || $type === 'opu') {
            $rows = $rows->merge($this->fetchOpu($search, $startDate, $endDate));
        }

        $rows = $rows->sortBy('created_at', SORT_REGULAR, $direction === 'desc')->values();

        $total = $rows->count();
        $items = $rows->forPage($page, $perPage)->values();
        $paginator = new LengthAwarePaginator($items, $total, $perPage, $page, [
            'path' => $request->url(),
            'query' => $request->query(),
        ]);

        return Inertia::render('LabResults/Index', [
            'results' => $paginator,
            'filters' => [
                'search' => $search,
                'type' => $type,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'direction' => $direction,
            ],
            'typeOptions' => self::TYPE_LABELS,
        ]);
    }

    private function applySearch(Builder $query, string $search): void
    {
        if ($search === '') {
            return;
        }

        $query->where(function (Builder $q) use ($search) {
            $q->whereHas('patient', function (Builder $pq) use ($search) {
                $pq->where('name', 'like', "%{$search}%")
                    ->orWhere('surname', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%");
            })->orWhere('medical_order_id', 'like', "%{$search}%");
        });
    }

    private function fetchStandard(string $modelClass, string $type, string $dateField, callable $urlResolver, string $search, ?string $startDate, ?string $endDate): Collection
    {
        $query = $modelClass::query()->with('patient');

        $this->applySearch($query, $search);

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        return $query->get()->map(function ($report) use ($type, $dateField, $urlResolver) {
            $patient = $report->patient;

            return [
                'key' => $type.'-'.$report->id,
                'type' => $type,
                'type_label' => self::TYPE_LABELS[$type],
                'patient_name' => $patient?->full_name ?: 'Unknown Patient',
                'patient_hn' => $patient?->id,
                'medical_order_id' => $report->medical_order_id,
                'result_date' => $report->{$dateField} ?: null,
                'created_at' => optional($report->created_at)->toIso8601String(),
                'view_url' => $urlResolver($report->id),
            ];
        });
    }

    private function fetchFet(string $search, ?string $startDate, ?string $endDate): Collection
    {
        $query = FetReport::query();

        if ($search !== '') {
            $query->where(function (Builder $q) use ($search) {
                $q->where('female_patient_name', 'like', "%{$search}%")
                    ->orWhere('female_hn', 'like', "%{$search}%")
                    ->orWhere('male_patient_name', 'like', "%{$search}%")
                    ->orWhere('male_hn', 'like', "%{$search}%")
                    ->orWhere('medical_order_id', 'like', "%{$search}%");
            });
        }
        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        return $query->get()->map(fn ($report) => [
            'key' => 'fet-'.$report->id,
            'type' => 'fet',
            'type_label' => self::TYPE_LABELS['fet'],
            'patient_name' => $report->female_patient_name ?: 'Unknown Patient',
            'patient_hn' => $report->female_hn,
            'medical_order_id' => $report->medical_order_id,
            'result_date' => null,
            'created_at' => optional($report->created_at)->toIso8601String(),
            'view_url' => route('fet-reports.show', $report->id),
        ]);
    }

    private function fetchOpu(string $search, ?string $startDate, ?string $endDate): Collection
    {
        $query = OpuReport::query()->with(['femalePatient', 'malePatient']);

        if ($search !== '') {
            $query->where(function (Builder $q) use ($search) {
                $q->whereHas('femalePatient', function (Builder $pq) use ($search) {
                    $pq->where('name', 'like', "%{$search}%")
                        ->orWhere('surname', 'like', "%{$search}%")
                        ->orWhere('id', 'like', "%{$search}%");
                })->orWhereHas('malePatient', function (Builder $pq) use ($search) {
                    $pq->where('name', 'like', "%{$search}%")
                        ->orWhere('surname', 'like', "%{$search}%")
                        ->orWhere('id', 'like', "%{$search}%");
                })->orWhere('medical_order_id', 'like', "%{$search}%");
            });
        }
        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        return $query->get()->map(function ($report) {
            $patient = $report->femalePatient ?? $report->malePatient;

            return [
                'key' => 'opu-'.$report->id,
                'type' => 'opu',
                'type_label' => self::TYPE_LABELS['opu'],
                'patient_name' => $patient?->full_name ?: 'Unknown Patient',
                'patient_hn' => $patient?->id,
                'medical_order_id' => $report->medical_order_id,
                'result_date' => null,
                'created_at' => optional($report->created_at)->toIso8601String(),
                'view_url' => $report->medical_order_id ? route('opu-reports.show', ['medicalOrderId' => $report->medical_order_id]) : null,
            ];
        });
    }
}
