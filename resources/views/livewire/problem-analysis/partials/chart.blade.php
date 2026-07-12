<x-card class="mt-4" 
    x-effect="renderChart($wire.chartData)"
    x-data="{
        problemChart: null,
        init() {
            // Wait for Chart.js to load from the CDN
            const checkChart = setInterval(() => {
                if (typeof Chart !== 'undefined' && typeof ChartDataLabels !== 'undefined') {
                    clearInterval(checkChart);
                    Chart.register(ChartDataLabels);
                    this.renderChart($wire.chartData);
                }
            }, 100);
        },
        renderChart(data) {
            // Ensure Chart is loaded
            if (typeof Chart === 'undefined') return;
            
            if (this.problemChart) {
                this.problemChart.destroy();
                this.problemChart = null;
            }
            
            const emptyState = document.getElementById('chartEmptyState');
            
            if (!data || !Array.isArray(data) || data.length === 0) {
                if(emptyState) emptyState.style.display = 'flex';
                return;
            }
            
            if(emptyState) emptyState.style.display = 'none';
            
            const labels = data.map(item => item.category);
            const totals = data.map(item => parseInt(item.total));
            const ctx = document.getElementById('problemChart').getContext('2d');
            
            this.problemChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Total Problems',
                        data: totals,
                        backgroundColor: 'rgba(229, 0, 18, 0.8)', // #E50012 with opacity
                        borderColor: '#E50012',
                        borderWidth: 2,
                        borderRadius: 4,
                        hoverBackgroundColor: '#E50012',
                        hoverBorderColor: '#C2000F' // slightly darker red
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    onClick: (e, elements) => {
                        if (elements.length > 0) {
                            const index = elements[0].index;
                            const category = this.problemChart.data.labels[index];
                            $wire.showDetail(category);
                        }
                    },
                    onHover: (event, activeElements) => {
                        event.native.target.style.cursor = activeElements.length > 0 ? 'pointer' : 'default';
                    },
                    plugins: {
                        legend: { display: true },
                        tooltip: { enabled: true },
                        datalabels: {
                            color: (context) => {
                                return document.documentElement.classList.contains('dark') ? '#ffffff' : '#333333';
                            },
                            font: { weight: 'bold', size: 12 },
                            align: 'end',
                            anchor: 'end',
                            formatter: (value) => value,
                            offset: 4
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 }
                        }
                    }
                }
            });
        }
    }"
>
    <div class="text-center mb-6">
        <h3 class="text-xl font-bold text-base-content/80" id="chart-title">
            @if($selectedYear && $selectedMonth && $selectedBy)
                Worst Condition By {{ $selectedBy }} ({{ $monthNames[$selectedMonth] ?? '' }} {{ $selectedYear }})
            @else
                Please select Year, Category, and Month to view the chart
            @endif
        </h3>
    </div>
    
    <div class="w-full h-[450px] flex justify-center items-center relative" wire:ignore>
        <canvas id="problemChart"></canvas>
        <div id="chartEmptyState" class="absolute inset-0 flex flex-col justify-center items-center bg-base-100/50 backdrop-blur-sm z-10 transition-opacity duration-300">
            <x-icon name="o-chart-bar" class="w-16 h-16 opacity-30 mb-2" />
            <p class="text-base-content/50">Data not available or filters incomplete</p>
        </div>
    </div>
</x-card>
