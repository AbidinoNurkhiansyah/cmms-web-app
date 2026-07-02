<!-- Chart Visualization -->
@if($selectedType && $selectedYear && $selectedMachine && $this->chartData())
    @if($selectedType === 'RUN OUT')
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Run Out Kelurusan Chart -->
            <x-card title="Act Kelurusan (mikron)">
                <x-chart wire:model="myChartKelurusan" wire:key="chart-kelurusan-{{ $chartKey }}" class="w-full min-h-[300px]" style="aspect-ratio: 2/1;" />
            </x-card>

            <!-- Run Out Putaran Chart -->
            <x-card title="Act Putaran (mikron)">
                <x-chart wire:model="myChartPutaran" wire:key="chart-putaran-{{ $chartKey }}" class="w-full min-h-[300px]" style="aspect-ratio: 2/1;" />
            </x-card>
        </div>
    @else
        <x-card title="Actual vs Standard" class="mb-6">
            <x-chart wire:model="myChart" wire:key="chart-main-{{ $chartKey }}" class="w-full min-h-[300px]" style="aspect-ratio: 3/1;" />
        </x-card>
    @endif
@elseif($selectedType || $selectedYear || $selectedMachine)
    <div class="text-center py-10 text-gray-500">
        Please complete the filters to view the checksheet chart.
    </div>
@else
    <div class="text-center py-10 text-gray-500">
        Select a Checksheet Type, Year, and Machine to begin.
    </div>
@endif
