<?php

use Livewire\Volt\Component;
use Illuminate\Support\Facades\DB;

new class extends Component {

    public function with(): array
    {
        $year = now()->year;

        // Activity counts – tables will exist after their modules are migrated.
        // Use try/catch so dashboard works before all modules are built.
        $totalTPM      = $this->safeCount('cmms_tpm',                    'Date',  $year);
        $totalCardty   = $this->safeCount('carty',                        'Date',  $year);
        $totalOH       = $this->safeCount('cmms_oh_web',                 'date',  $year);
        $totalWO       = $this->safeCount('cmms_work_order_request',      'date',  $year);

        // 6-month chart labels
        $months = collect(range(5, 0))->map(fn($i) => now()->subMonths($i));
        $labels = $months->map(fn($m) => $m->format('M'))->toArray();

        $tpmData    = $this->safeMonthly('cmms_tpm',                 'Date',  $months);
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
    @if(session('success_login'))
        <div class="alert alert-success mb-6 shadow-sm" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition.duration.500ms>
            <x-icon name="o-check-circle" class="w-6 h-6" />
            <span class="font-medium">{{ session('success_login') }}</span>
            <div>
                <button class="btn btn-sm btn-circle btn-ghost" @click="show = false"><x-icon name="o-x-mark" /></button>
            </div>
        </div>
    @endif

    <x-header title="Dashboard" subtitle="{{ now()->format('l, d F Y') }}" separator />

    @php
        $safetySlides = [
            ['title' => 'Emergency Stop', 'text' => 'Tekan Tombol Emergency Stop saat kondisi darurat.', 'icon' => 'o-exclamation-triangle', 'color' => 'text-error'],
            ['title' => 'Manual Mode', 'text' => 'Pindahkan Selector Switch Ke Manual sebelum perbaikan.', 'icon' => 'o-adjustments-horizontal', 'color' => 'text-warning'],
            ['title' => 'LOTO Procedure', 'text' => 'Gantung Safety Tag Sebelum Memulai Pekerjaan.', 'icon' => 'o-tag', 'color' => 'text-info'],
            ['title' => 'Pneumatic Safety', 'text' => 'Buang Sisa Tekanan Angin hingga tuntas.', 'icon' => 'o-arrow-down-circle', 'color' => 'text-success'],
        ];
    @endphp

    {{-- Safety Commitment Banner --}}
    <div class="mb-8 overflow-hidden rounded-2xl bg-gradient-to-r from-base-200 to-base-100 border border-base-200 shadow-sm relative"
         x-data="{
            current: 0,
            init() { setInterval(() => { this.current = (this.current + 1) % {{ count($safetySlides) }} }, 6000) }
         }">
        <div class="px-6 py-5 flex items-center justify-between">
            <div class="flex-1">
                <div class="text-[10px] font-bold uppercase tracking-widest opacity-50 mb-2">Safety Commitment</div>
                <div class="relative h-12">
                    @foreach($safetySlides as $i => $slide)
                        <div x-show="current === {{ $i }}" 
                             x-transition:enter="transition ease-out duration-700"
                             x-transition:enter-start="opacity-0 translate-y-2" 
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-300 absolute inset-0"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0"
                             class="flex items-center gap-4" style="display: none;">
                            <div class="p-2.5 rounded-xl bg-base-100 shadow-sm border border-base-200">
                                <x-icon name="{{ $slide['icon'] }}" class="w-6 h-6 {{ $slide['color'] }}" />
                            </div>
                            <div>
                                <div class="font-bold text-sm">{{ $slide['title'] }}</div>
                                <div class="text-xs opacity-70 mt-0.5">{{ $slide['text'] }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            
            {{-- Dots Indicator --}}
            <div class="flex gap-1.5 ml-4">
                @foreach($safetySlides as $i => $slide)
                    <button @click="current = {{ $i }}" 
                            class="h-1.5 rounded-full transition-all duration-300"
                            :class="current === {{ $i }} ? 'bg-primary w-4' : 'bg-base-300 hover:bg-base-content/30 w-1.5'"></button>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Activity Summary Stats --}}
    <div class="flex items-center justify-between mb-3">
        <h5 class="font-bold opacity-70 uppercase tracking-wider text-sm">Main Activity Summary</h5>
    </div>
    <div class="stats stats-vertical lg:stats-horizontal shadow-sm border border-base-200 w-full mb-8 bg-base-100">
        
        <div class="stat hover:bg-base-200/50 transition-colors cursor-pointer" onclick="Livewire.navigate('/tpm/list')">
            <div class="stat-figure text-success">
                <div class="p-3 bg-success/10 rounded-full">
                    <x-icon name="o-wrench-screwdriver" class="w-7 h-7" />
                </div>
            </div>
            <div class="stat-title font-medium text-xs uppercase tracking-wide">Deep Cleaning</div>
            <div class="stat-value text-success text-3xl my-1">{{ $totalTPM }}</div>
            <div class="stat-desc">Records this year</div>
        </div>
        
        <div class="stat border-t lg:border-t-0 lg:border-l border-base-200 hover:bg-base-200/50 transition-colors cursor-pointer" onclick="Livewire.navigate('/maintenance/cardty')">
            <div class="stat-figure text-error">
                <div class="p-3 bg-error/10 rounded-full">
                    <x-icon name="o-exclamation-triangle" class="w-7 h-7" />
                </div>
            </div>
            <div class="stat-title font-medium text-xs uppercase tracking-wide">Cardty</div>
            <div class="stat-value text-error text-3xl my-1">{{ $totalCardty }}</div>
            <div class="stat-desc">Issues this year</div>
        </div>
        
        <div class="stat border-t lg:border-t-0 lg:border-l border-base-200 hover:bg-base-200/50 transition-colors cursor-pointer" onclick="Livewire.navigate('/overhaul')">
            <div class="stat-figure text-primary">
                <div class="p-3 bg-primary/10 rounded-full">
                    <x-icon name="o-cog-8-tooth" class="w-7 h-7" />
                </div>
            </div>
            <div class="stat-title font-medium text-xs uppercase tracking-wide">Overhaul</div>
            <div class="stat-value text-primary text-3xl my-1">{{ $totalOH }}</div>
            <div class="stat-desc">OH made this year</div>
        </div>
        
        <div class="stat border-t lg:border-t-0 lg:border-l border-base-200 hover:bg-base-200/50 transition-colors cursor-pointer" onclick="Livewire.navigate('/work-orders')">
            <div class="stat-figure text-warning">
                <div class="p-3 bg-warning/10 rounded-full">
                    <x-icon name="o-clipboard-document-check" class="w-7 h-7" />
                </div>
            </div>
            <div class="stat-title font-medium text-xs uppercase tracking-wide">Work Orders</div>
            <div class="stat-value text-warning text-3xl my-1">{{ $totalWO }}</div>
            <div class="stat-desc">Requests this year</div>
        </div>
        
    </div>

    {{-- Activity Charts (6 months) --}}
    <div class="flex items-center justify-between mb-3 mt-4">
        <h5 class="font-bold opacity-70 uppercase tracking-wider text-sm">Activity Trends (6 Months)</h5>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">

        <div class="card bg-base-100 shadow-sm border border-base-200">
            <div class="card-body p-5">
                <div class="flex items-center justify-between mb-4">
                    <h6 class="font-bold text-sm">Deep Cleaning</h6>
                    <div class="w-2 h-2 rounded-full bg-success"></div>
                </div>
                <canvas id="tpmChart" class="w-full" style="max-height:160px"></canvas>
            </div>
        </div>

        <div class="card bg-base-100 shadow-sm border border-base-200">
            <div class="card-body p-5">
                <div class="flex items-center justify-between mb-4">
                    <h6 class="font-bold text-sm">Cardty</h6>
                    <div class="w-2 h-2 rounded-full bg-error"></div>
                </div>
                <canvas id="cartyChart" class="w-full" style="max-height:160px"></canvas>
            </div>
        </div>

        <div class="card bg-base-100 shadow-sm border border-base-200">
            <div class="card-body p-5">
                <div class="flex items-center justify-between mb-4">
                    <h6 class="font-bold text-sm">Overhaul</h6>
                    <div class="w-2 h-2 rounded-full bg-primary"></div>
                </div>
                <canvas id="ohChart" class="w-full" style="max-height:160px"></canvas>
            </div>
        </div>

        <div class="card bg-base-100 shadow-sm border border-base-200">
            <div class="card-body p-5">
                <div class="flex items-center justify-between mb-4">
                    <h6 class="font-bold text-sm">Work Orders</h6>
                    <div class="w-2 h-2 rounded-full bg-warning"></div>
                </div>
                <canvas id="woChart" class="w-full" style="max-height:160px"></canvas>
            </div>
        </div>

    </div>

    @script
    <script>
        const labels  = @json($labels);
        const tpmData  = @json($tpmData);
        const cartyData = @json($cartyData);
        const ohData   = @json($ohData);
        const woData   = @json($woData);

        function makeChart(id, data, color) {
            const ctx = document.getElementById(id);
            if (!ctx) return;
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: color + '99',
                        borderColor: color,
                        borderWidth: 1,
                        borderRadius: 4,
                    }]
                },
                options: {
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, ticks: { precision: 0 } },
                        x: { grid: { display: false } }
                    }
                }
            });
        }

        makeChart('tpmChart',   tpmData,   '#22c55e');
        makeChart('cartyChart', cartyData, '#ef4444');
        makeChart('ohChart',    ohData,    '#3b82f6');
        makeChart('woChart',    woData,    '#f59e0b');
    </script>
    @endscript
</div>
