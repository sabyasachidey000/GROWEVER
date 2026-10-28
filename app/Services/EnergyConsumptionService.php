<?php

namespace App\Services;

use App\Models\Machine;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EnergyConsumptionService
{
    private array $numericColumnsCache = [];
    private ?array $columnListingCache = null;

    public function hasMachineMapping(): bool
    {
        return Schema::hasTable('energy_readings') && Schema::hasColumn('energy_readings', 'machine_id');
    }

    public function machineSummary(Machine $machine, $startDate = null, $endDate = null): array
    {
        $start = $this->startDate($startDate);
        $end = $this->endDate($endDate);

        if (!$this->hasMachineMapping()) {
            return $this->emptySummary($machine, $start, $end, 'Energy readings are not mapped to machines yet.');
        }

        $readings = DB::table('energy_readings')
            ->where('machine_id', $machine->id)
            ->whereBetween('timestamp', [$start, $end]);

        $first = (clone $readings)->whereNotNull('wh_import')->orderBy('timestamp')->first();
        $last = (clone $readings)->whereNotNull('wh_import')->orderByDesc('timestamp')->first();
        $latest = (clone $readings)->orderByDesc('timestamp')->first();

        $consumptionWh = $this->calculateConsumptionWh($first?->wh_import, $last?->wh_import);

        return [
            'machine_id' => $machine->id,
            'machine_code' => $machine->machine_code,
            'machine_name' => $machine->name,
            'start_date' => $start->toDateString(),
            'end_date' => $end->toDateString(),
            'reading_count' => (clone $readings)->count(),
            'first_reading_at' => $first?->timestamp,
            'last_reading_at' => $last?->timestamp,
            'start_wh_import' => $first?->wh_import,
            'end_wh_import' => $last?->wh_import,
            'consumption_wh' => $consumptionWh,
            'consumption_kwh' => round($consumptionWh / 1000, 3),
            'latest_voltage_l1' => $this->readingValue($latest, 'voltage_l1'),
            'latest_voltage_l2' => $this->readingValue($latest, 'voltage_l2'),
            'latest_voltage_l3' => $this->readingValue($latest, 'voltage_l3'),
            'latest_watt_sum' => $latest?->watt_sum,
            'latest_current_l1' => $this->readingValue($latest, 'current_l1'),
            'latest_current_l2' => $this->readingValue($latest, 'current_l2'),
            'latest_current_l3' => $this->readingValue($latest, 'current_l3'),
            'latest_current_sum' => $latest?->current_sum,
            'latest_pf_l1' => $this->readingValue($latest, 'pf_l1'),
            'latest_pf_l2' => $this->readingValue($latest, 'pf_l2'),
            'latest_pf_l3' => $this->readingValue($latest, 'pf_l3'),
            'latest_pf_avg' => $this->readingValue($latest, 'pf_avg'),
            'latest_voltage_avg' => $latest?->voltage_avg,
            'latest_frequency' => $latest?->frequency,
            'note' => null,
        ];
    }

    public function machineSummaries($startDate = null, $endDate = null): Collection
    {
        $start = $this->startDate($startDate);
        $end = $this->endDate($endDate);

        return Machine::orderBy('machine_code')
            ->get()
            ->map(fn(Machine $machine) => $this->machineSummary($machine, $start, $end));
    }

    public function recentReadings(Machine $machine, int $limit = 10): Collection
    {
        if (!$this->hasMachineMapping()) {
            return collect();
        }

        return DB::table('energy_readings')
            ->where('machine_id', $machine->id)
            ->orderByDesc('timestamp')
            ->limit($limit)
            ->get();
    }

    public function chartData(Machine $machine, int $limit = 30): array
    {
        if (!$this->hasMachineMapping()) {
            return $this->emptyChartData();
        }

        $readings = DB::table('energy_readings')
            ->where('machine_id', $machine->id)
            ->orderByDesc('timestamp')
            ->limit($limit)
            ->get()
            ->reverse()
            ->values();

        if ($readings->isEmpty()) {
            return $this->emptyChartData();
        }

        return [
            'labels' => $readings->map(fn($reading) => Carbon::parse($reading->timestamp)->format('H:i'))->values(),
            'voltage' => [
                'l1' => $this->series($readings, 'voltage_l1'),
                'l2' => $this->series($readings, 'voltage_l2'),
                'l3' => $this->series($readings, 'voltage_l3'),
            ],
            'watt' => [
                'l1' => $this->series($readings, 'watt_l1'),
                'l2' => $this->series($readings, 'watt_l2'),
                'l3' => $this->series($readings, 'watt_l3'),
                'sum' => $this->series($readings, 'watt_sum'),
            ],
            'current' => [
                'l1' => $this->series($readings, 'current_l1'),
                'l2' => $this->series($readings, 'current_l2'),
                'l3' => $this->series($readings, 'current_l3'),
                'sum' => $this->series($readings, 'current_sum'),
            ],
            'power_factor' => [
                'avg' => $this->series($readings, 'pf_avg'),
                'l1' => $this->series($readings, 'pf_l1'),
                'l2' => $this->series($readings, 'pf_l2'),
                'l3' => $this->series($readings, 'pf_l3'),
            ],
            'frequency' => [
                'value' => $this->series($readings, 'frequency'),
            ],
            'energy' => [
                'wh_import' => $this->series($readings, 'wh_import'),
                'vah' => $this->series($readings, 'vah'),
            ],
        ];
    }

    public function latestMetricCards(Machine $machine): array
    {
        if (!$this->hasMachineMapping()) {
            return [];
        }

        $latest = DB::table('energy_readings')
            ->where('machine_id', $machine->id)
            ->orderByDesc('timestamp')
            ->first();

        if (!$latest) {
            return [];
        }

        return [
            ['label' => 'Voltage L1', 'value' => $this->formatNumber($latest->voltage_l1), 'unit' => 'V'],
            ['label' => 'Voltage L2', 'value' => $this->formatNumber($latest->voltage_l2), 'unit' => 'V'],
            ['label' => 'Voltage L3', 'value' => $this->formatNumber($latest->voltage_l3), 'unit' => 'V'],
            ['label' => 'Frequency', 'value' => $this->formatNumber($latest->frequency), 'unit' => 'Hz'],
            ['label' => 'Current Sum', 'value' => $this->formatNumber($latest->current_sum), 'unit' => 'A'],
            ['label' => 'Power', 'value' => $this->formatNumber($latest->watt_sum), 'unit' => 'W'],
        ];
    }

    public function fieldAnalysis(Machine $machine, $startDate = null, $endDate = null): Collection
    {
        $start = $this->startDate($startDate);
        $end = $this->endDate($endDate);

        if (!$this->hasMachineMapping()) {
            return collect();
        }

        $latest = DB::table('energy_readings')
            ->where('machine_id', $machine->id)
            ->whereBetween('timestamp', [$start, $end])
            ->orderByDesc('timestamp')
            ->first();

        if (!$latest) {
            return collect();
        }

        $columns = collect($this->energyColumns())->reject(fn($column) => in_array($column, [
            'id',
            'machine_id',
            'created_at',
            'updated_at',
        ], true));

        $numericColumns = $this->numericColumns();

        $aggregateColumns = $columns
            ->filter(fn($column) => in_array($column, $numericColumns, true))
            ->flatMap(fn($column) => [
                DB::raw("MIN({$column}) as {$column}_min"),
                DB::raw("MAX({$column}) as {$column}_max"),
                DB::raw("AVG({$column}) as {$column}_avg"),
            ])
            ->values()
            ->all();

        $aggregates = empty($aggregateColumns)
            ? null
            : DB::table('energy_readings')
                ->where('machine_id', $machine->id)
                ->whereBetween('timestamp', [$start, $end])
                ->select($aggregateColumns)
                ->first();

        return $columns->map(function ($column) use ($latest, $aggregates, $numericColumns) {
            $isNumeric = in_array($column, $numericColumns, true);

            return [
                'field' => $column,
                'label' => str_replace('_', ' ', ucfirst($column)),
                'latest' => $this->formatFieldValue($latest->{$column} ?? null),
                'min' => $isNumeric ? $this->formatFieldValue($aggregates?->{$column . '_min'} ?? null) : '-',
                'max' => $isNumeric ? $this->formatFieldValue($aggregates?->{$column . '_max'} ?? null) : '-',
                'avg' => $isNumeric ? $this->formatFieldValue($aggregates?->{$column . '_avg'} ?? null) : '-',
            ];
        });
    }

    public function alerts(Machine $machine): array
    {
        if (!$this->hasMachineMapping()) {
            return ['Energy readings are not mapped to machines yet.'];
        }

        $latest = DB::table('energy_readings')
            ->where('machine_id', $machine->id)
            ->orderByDesc('timestamp')
            ->first();

        if (!$latest) {
            return ['No mapped energy readings found.'];
        }

        $alerts = [];

        foreach (['voltage_l1', 'voltage_l2', 'voltage_l3'] as $field) {
            if ($latest->{$field} !== null && ((float) $latest->{$field} < 200 || (float) $latest->{$field} > 260)) {
                $alerts[] = str_replace('_', ' ', ucfirst($field)) . ' is outside 200-260 V.';
            }
        }

        if ($latest->frequency !== null && ((float) $latest->frequency < 48 || (float) $latest->frequency > 52)) {
            $alerts[] = 'Frequency is outside 48-52 Hz.';
        }

        return $alerts ?: ['No active alerts.'];
    }

    public function machineWiseReport($startDate, $endDate): Collection
    {
        return $this->machineSummaries($startDate, $endDate)->map(fn(array $summary) => [
            'machine_code' => $summary['machine_code'],
            'machine_name' => $summary['machine_name'],
            'reading_count' => $summary['reading_count'],
            'start_reading' => $summary['first_reading_at'] ? Carbon::parse($summary['first_reading_at'])->format('Y-m-d H:i') : '-',
            'end_reading' => $summary['last_reading_at'] ? Carbon::parse($summary['last_reading_at'])->format('Y-m-d H:i') : '-',
            'start_wh_import' => $this->formatNumber($summary['start_wh_import']),
            'end_wh_import' => $this->formatNumber($summary['end_wh_import']),
            'consumption_wh' => $this->formatNumber($summary['consumption_wh']),
            'consumption_kwh' => $this->formatNumber($summary['consumption_kwh']),
            'latest_voltage_l1' => $this->formatNumber($summary['latest_voltage_l1']),
            'latest_voltage_l2' => $this->formatNumber($summary['latest_voltage_l2']),
            'latest_voltage_l3' => $this->formatNumber($summary['latest_voltage_l3']),
            'latest_current_l1' => $this->formatNumber($summary['latest_current_l1']),
            'latest_current_l2' => $this->formatNumber($summary['latest_current_l2']),
            'latest_current_l3' => $this->formatNumber($summary['latest_current_l3']),
            'latest_current_total' => $this->formatNumber($summary['latest_current_sum']),
            'latest_pf_l1' => $this->formatNumber($summary['latest_pf_l1']),
            'latest_pf_l2' => $this->formatNumber($summary['latest_pf_l2']),
            'latest_pf_l3' => $this->formatNumber($summary['latest_pf_l3']),
            'latest_power_factor_avg' => $this->formatNumber($summary['latest_pf_avg']),
            'latest_frequency' => $this->formatNumber($summary['latest_frequency']),
            'latest_power_w' => $this->formatNumber($summary['latest_watt_sum']),
            'note' => $summary['note'] ?? '-',
        ]);
    }

    private function emptySummary(Machine $machine, Carbon $start, Carbon $end, ?string $note = null): array
    {
        return [
            'machine_id' => $machine->id,
            'machine_code' => $machine->machine_code,
            'machine_name' => $machine->name,
            'start_date' => $start->toDateString(),
            'end_date' => $end->toDateString(),
            'reading_count' => 0,
            'first_reading_at' => null,
            'last_reading_at' => null,
            'start_wh_import' => null,
            'end_wh_import' => null,
            'consumption_wh' => 0,
            'consumption_kwh' => 0,
            'latest_voltage_l1' => null,
            'latest_voltage_l2' => null,
            'latest_voltage_l3' => null,
            'latest_watt_sum' => null,
            'latest_current_l1' => null,
            'latest_current_l2' => null,
            'latest_current_l3' => null,
            'latest_current_sum' => null,
            'latest_pf_l1' => null,
            'latest_pf_l2' => null,
            'latest_pf_l3' => null,
            'latest_pf_avg' => null,
            'latest_voltage_avg' => null,
            'latest_frequency' => null,
            'note' => $note,
        ];
    }

    private function calculateConsumptionWh($first, $last): float
    {
        if ($first === null || $last === null) {
            return 0;
        }

        return max(0, (float) $last - (float) $first);
    }

    private function startDate($date = null): Carbon
    {
        return Carbon::parse($date ?? now()->subDays(30))->startOfDay();
    }

    private function endDate($date = null): Carbon
    {
        return Carbon::parse($date ?? now())->endOfDay();
    }

    private function formatNumber($value): string
    {
        return $value === null ? '-' : number_format((float) $value, 3);
    }

    private function readingValue($reading, string $field)
    {
        if (!$reading || !property_exists($reading, $field)) {
            return null;
        }

        return $reading->{$field};
    }

    private function energyColumns(): array
    {
        if ($this->columnListingCache === null) {
            $this->columnListingCache = Schema::hasTable('energy_readings')
                ? Schema::getColumnListing('energy_readings')
                : [];
        }

        return $this->columnListingCache;
    }

    private function numericColumns(): array
    {
        if (!empty($this->numericColumnsCache)) {
            return $this->numericColumnsCache;
        }

        $rows = DB::table('information_schema.columns')
            ->where('table_schema', 'public')
            ->where('table_name', 'energy_readings')
            ->whereIn('data_type', [
                'bigint',
                'double precision',
                'integer',
                'numeric',
                'real',
                'smallint',
            ])
            ->pluck('column_name')
            ->all();

        $this->numericColumnsCache = $rows;

        return $this->numericColumnsCache;
    }

    private function series(Collection $readings, string $field): Collection
    {
        return $readings->map(fn($reading) => $reading->{$field} === null ? null : round((float) $reading->{$field}, 3))->values();
    }

    private function emptyChartData(): array
    {
        return [
            'labels' => [],
            'voltage' => ['l1' => [], 'l2' => [], 'l3' => []],
            'watt' => ['l1' => [], 'l2' => [], 'l3' => [], 'sum' => []],
            'current' => ['l1' => [], 'l2' => [], 'l3' => [], 'sum' => []],
            'power_factor' => ['avg' => [], 'l1' => [], 'l2' => [], 'l3' => []],
            'frequency' => ['value' => []],
            'energy' => ['wh_import' => [], 'vah' => []],
        ];
    }

    private function formatFieldValue($value): string
    {
        if ($value === null) {
            return '-';
        }

        if (is_numeric($value)) {
            return number_format((float) $value, 3);
        }

        return (string) $value;
    }
}
