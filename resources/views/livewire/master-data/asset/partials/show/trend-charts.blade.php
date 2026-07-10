    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- Problem Trend Chart --}}
        <x-card class="border-l-4 border-error">
            <x-slot:title>
                <div class="flex justify-between items-center w-full">
                    <span class="text-lg">Problem Trend (Carty)</span>
                    <select wire:model.live="chartYear" class="select select-sm select-bordered">
                        @foreach($years as $y)
                            <option value="{{ $y }}">Apr {{ $y }} - Mar {{ $y+1 }}</option>
                        @endforeach
                    </select>
                </div>
            </x-slot:title>
            
            <div class="w-full h-64 mt-4" wire:ignore
                 x-data="{
                     chart: null,
                     labels: @entangle('trendData').live,
                     init() {
                         this.renderChart();
                         this.$watch('labels', () => {
                             if(this.chart) this.chart.destroy();
                             this.renderChart();
                         });
                     },
                     renderChart() {
                         this.chart = new Chart(this.$refs.probCanvas, {
                             type: 'bar',
                             data: {
                                 labels: this.labels.labels,
                                 datasets: [{
                                     label: 'Problems',
                                     data: this.labels.problems,
                                     backgroundColor: '#f87171',
                                 }]
                             },
                             options: { responsive: true, maintainAspectRatio: false }
                         });
                     }
                 }">
                <canvas x-ref="probCanvas"></canvas>
            </div>
        </x-card>

        {{-- Time Trend Chart --}}
        <x-card class="border-l-4 border-warning">
            <x-slot:title>
                <div class="flex justify-between items-center w-full">
                    <span class="text-lg">Time Trend (Minutes)</span>
                    <select wire:model.live="timeChartYear" class="select select-sm select-bordered">
                        @foreach($years as $y)
                            <option value="{{ $y }}">Apr {{ $y }} - Mar {{ $y+1 }}</option>
                        @endforeach
                    </select>
                </div>
            </x-slot:title>

            <div class="w-full h-64 mt-4" wire:ignore
                 x-data="{
                     chart: null,
                     timeData: @entangle('timeTrendData').live,
                     init() {
                         this.renderChart();
                         this.$watch('timeData', () => {
                             if(this.chart) this.chart.destroy();
                             this.renderChart();
                         });
                     },
                     renderChart() {
                         this.chart = new Chart(this.$refs.timeCanvas, {
                             type: 'bar',
                             data: {
                                 labels: this.timeData.labels,
                                 datasets: [
                                     {
                                         label: 'Repair Time',
                                         data: this.timeData.repairTime,
                                         backgroundColor: '#60a5fa',
                                     },
                                     {
                                         label: 'Stop Line Time',
                                         data: this.timeData.stopLineTime,
                                         backgroundColor: '#fbbf24',
                                     }
                                 ]
                             },
                             options: { responsive: true, maintainAspectRatio: false }
                         });
                     }
                 }">
                <canvas x-ref="timeCanvas"></canvas>
            </div>
        </x-card>
    </div>
