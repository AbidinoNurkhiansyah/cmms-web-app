<div wire:poll.300s="loadCharts" class="space-y-6">
    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @php
            $totalOutstanding = count($outstandingAndons);
            $criticalCount = 0;
            $now = now()->timestamp;
            foreach($outstandingAndons as $step) {
                if ($step->time_in && ($now - $step->time_in->timestamp) >= 7200) {
                    $criticalCount++;
                }
            }
        @endphp
        <x-stat title="Outstanding Calls" value="{{ $totalOutstanding }}" icon="o-bell-alert" class="shadow-sm border-0 bg-base-100" color="text-warning" />
        <x-stat title="Critical (> 2 Hrs)" value="{{ $criticalCount }}" icon="o-fire" class="shadow-sm border-0 bg-base-100" color="text-error" />
        <x-stat title="Last Update" value="{{ now()->format('H:i') }}" icon="o-clock" class="shadow-sm border-0 bg-base-100" color="text-success" />
    </div>

    <!-- Outstanding Andon Table -->
    <x-card class="shadow-sm border-0">
        <x-slot:title>
            <div class="flex items-center gap-2 text-lg font-bold">
                <x-icon name="o-exclamation-triangle" class="text-error w-6 h-6" />
                Outstanding Andon (Today)
            </div>
        </x-slot:title>
        
        @if($totalOutstanding > 0)
            <div class="overflow-x-auto">
                <table class="table table-zebra w-full">
                    <thead class="bg-base-200/50 text-base-content/70">
                        <tr>
                            <th>Line</th>
                            <th>Machine</th>
                            <th>Problems</th>
                            <th>Start Time</th>
                            <th>Duration</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($outstandingAndons as $step)
                            @php
                                $start = $step->time_in ? $step->time_in->timestamp : null;
                                $durationClass = 'badge-ghost';
                                $pulse = '';
                                $formattedDuration = '00:00';
                                
                                if ($start && $start <= $now) {
                                    $diff = $now - $start;
                                    $minutes = floor($diff / 60);
                                    
                                    $hours = floor($diff / 3600);
                                    $mins = floor(($diff % 3600) / 60);
                                    $formattedDuration = sprintf('%02d:%02d', $hours, $mins);

                                    if ($minutes >= 120) {
                                        $durationClass = 'badge-error';
                                        $pulse = 'animate-pulse';
                                    } elseif ($minutes >= 60) {
                                        $durationClass = 'badge-warning';
                                    } elseif ($minutes >= 30) {
                                        $durationClass = 'badge-info';
                                    }
                                }

                                $statusClass = match(strtoupper($step->status)) {
                                    'TPM' => 'text-info',
                                    'STOP' => 'text-error',
                                    'CALL' => 'text-warning',
                                    default => 'text-base-content',
                                };
                            @endphp
                            <tr class="hover:bg-base-200/50 transition-colors">
                                <td class="font-semibold">{{ $step->line_name }}</td>
                                <td>{{ $step->machine }}</td>
                                <td class="text-base-content/80">{{ $step->stop_info }}</td>
                                <td>{{ $step->time_in ? $step->time_in->format('H:i') : '-' }}</td>
                                <td>
                                    <div class="badge {{ $durationClass }} {{ $pulse }} font-mono font-bold">
                                        <x-icon name="o-clock" class="w-3 h-3 mr-1" />
                                        {{ $formattedDuration }}
                                    </div>
                                </td>
                                <td>
                                    <span class="font-bold {{ $statusClass }}">
                                        {{ $step->status }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="flex flex-col items-center justify-center py-10 opacity-50">
                <x-icon name="o-check-badge" class="w-16 h-16 text-success mb-2" />
                <p class="text-lg font-medium">All clear! No outstanding Andon calls right now.</p>
            </div>
        @endif
    </x-card>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <x-card class="col-span-1 shadow-sm border-0">
            <x-slot:title>
                <div class="text-primary font-bold text-lg border-b pb-2">Today ({{ now()->format('d M Y') }})</div>
            </x-slot:title>
            <x-chart wire:model="chartToday" wire:key="chart-today" class="w-full min-h-[250px] mt-4" />
        </x-card>

        <x-card class="col-span-1 md:col-span-2 shadow-sm border-0">
            <x-slot:title>
                <div class="text-info font-bold text-lg border-b pb-2">Top 10 Downtime ({{ now()->format('F Y') }})</div>
            </x-slot:title>
            <x-chart wire:model="chartTop10" wire:key="chart-top10" class="w-full min-h-[250px] mt-4" />
        </x-card>
    </div>

    <x-card class="shadow-sm border-0">
        <x-slot:title>
            <div class="text-secondary font-bold text-lg border-b pb-2">Daily Trend ({{ now()->format('F Y') }})</div>
        </x-slot:title>
        <x-chart wire:model="chartDaily" wire:key="chart-daily" class="w-full min-h-[300px] mt-4" />
    </x-card>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <x-card class="shadow-sm border-0">
            <x-slot:title>
                <div class="text-accent font-bold text-lg border-b pb-2">Monthly Trend (6 Months)</div>
            </x-slot:title>
            <x-chart wire:model="chartMonthly" wire:key="chart-monthly" class="w-full min-h-[250px] mt-4" />
        </x-card>

        <x-card class="shadow-sm border-0">
            <x-slot:title>
                <div class="text-neutral font-bold text-lg border-b pb-2">Line Comparison (6 Months)</div>
            </x-slot:title>
            <x-chart wire:model="chartLine" wire:key="chart-line" class="w-full min-h-[250px] mt-4" />
        </x-card>
    </div>
</div>
