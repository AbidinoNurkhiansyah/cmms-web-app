<?php

use Livewire\Volt\Component;
use Illuminate\Support\Facades\DB;

new class extends Component {

    public function with(): array
    {
        $year = now()->year;

        // Activity counts – tables will exist after their modules are migrated.
        // Use try/catch so dashboard works before all modules are built.
        $totalTPM      = $this->safeCount('cmms_deep_cleanings',         'Date',  $year);
        $totalCardty   = $this->safeCount('carty',                        'Date',  $year);
        $totalOH       = $this->safeCount('cmms_oh_web',                 'date',  $year);
        $totalWO       = $this->safeCount('cmms_work_order_request',      'date',  $year);

        // 6-month chart labels
        $months = collect(range(5, 0))->map(fn($i) => now()->subMonths($i));
        $labels = $months->map(fn($m) => $m->format('M'))->toArray();

        $tpmData    = $this->safeMonthly('cmms_deep_cleanings',      'Date',  $months);
        $cartyData  = $this->safeMonthly('carty',                    'Date',  $months);
        $ohData     = $this->safeMonthly('cmms_oh_web',              'date',  $months);
        $woData     = $this->safeMonthly('cmms_work_order_request',  'date',  $months);

        return compact(
            'totalTPM', 'totalCardty', 'totalOH', 'totalWO',
            'labels', 'tpmData', 'cartyData', 'ohData', 'woData'
        );
    }

    private function safeCount(string $table, string $dateCol, int $year): int
    {
        try {
            return (int) DB::table($table)->whereYear($dateCol, $year)->count();
        } catch (\Throwable) {
            return 0;
        }
    }

    private function safeMonthly(string $table, string $dateCol, $months): array
    {
        try {
            $rows = DB::table($table)
                ->selectRaw("DATE_FORMAT({$dateCol}, '%Y-%m') as ym, COUNT(*) as total")
                ->whereIn(DB::raw("DATE_FORMAT({$dateCol}, '%Y-%m')"),
                    $months->map(fn($m) => $m->format('Y-m'))->toArray())
                ->groupBy('ym')
                ->pluck('total', 'ym');

            return $months->map(fn($m) => (int) ($rows[$m->format('Y-m')] ?? 0))->toArray();
        } catch (\Throwable) {
            return array_fill(0, 6, 0);
        }
    }
};
?>

<div>
    @include('livewire.dashboard.partials.alert')
    @include('livewire.dashboard.partials.safety-banner')
    @include('livewire.dashboard.partials.activity-stats')
    @include('livewire.dashboard.partials.activity-charts')
</div>
