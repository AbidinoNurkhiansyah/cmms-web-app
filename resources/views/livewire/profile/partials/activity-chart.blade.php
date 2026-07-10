<x-card title="Total Aktivitas Pekerjaan" shadow class="bg-base-100">
    <x-slot:menu>
        <div class="badge badge-primary badge-outline font-semibold">Period: {{ $activityStats['period'] }}</div>
    </x-slot:menu>

    <div class="w-full flex justify-center" x-data="{
        stats: {{ Js::from($activityStats) }},
        init() {
            const ctx = this.$refs.canvas.getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: this.stats.labels,
                    datasets: [{
                        label: 'Total Aktivitas',
                        data: this.stats.data,
                        backgroundColor: ['#dc3545', '#0d6efd', '#0dcaf0', '#ffc107', '#198754', '#6f42c1'],
                        borderColor: '#ffffff',
                        borderWidth: 1,
                        borderRadius: 4,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return ` ${context.raw} Reports`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                precision: 0
                            }
                        }
                    }
                }
            });
        }
    }">
        <div class="w-full" style="max-width: 600px;">
            <canvas x-ref="canvas"></canvas>
        </div>
    </div>
</x-card>
