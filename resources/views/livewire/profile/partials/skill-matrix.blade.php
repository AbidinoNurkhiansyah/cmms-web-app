<div class="grid grid-cols-1 md:grid-cols-2 gap-4" x-data="{
    officeSkills: {{ Js::from($officeSkills ?? []) }},
    genbaSkills: {{ Js::from($genbaSkills ?? []) }},
    init() {
        const createRadarChart = (canvas, data, color) => {
            const ctx = canvas.getContext('2d');
            const labels = data.map(item => item.skill_name);
            const actualData = data.map(item => item.actual_level);

            new Chart(ctx, {
                type: 'radar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Actual',
                        data: actualData,
                        backgroundColor: color.bg,
                        borderColor: color.border,
                        borderWidth: 2
                    }]
                },
                options: {
                    scales: {
                        r: {
                            beginAtZero: true,
                            suggestedMax: 4,
                            ticks: { stepSize: 1, precision: 0 },
                            angleLine: { display: true },
                            grid: { color: '#e5e7eb' },
                            pointLabels: { font: { size: 10, weight: 'bold' } }
                        }
                    },
                    plugins: {
                        legend: { display: false }
                    }
                }
            });
        };

        if (this.officeSkills.length > 0) {
            createRadarChart(
                this.$refs.officeCanvas, 
                this.officeSkills, 
                { bg: 'rgba(235, 105, 54, 0.2)', border: 'rgba(235, 105, 54, 1)' }
            );
        }

        if (this.genbaSkills.length > 0) {
            createRadarChart(
                this.$refs.genbaCanvas, 
                this.genbaSkills, 
                { bg: 'rgba(54, 162, 235, 0.2)', border: 'rgba(54, 162, 235, 1)' }
            );
        }
    }
}">
    {{-- Office Skill --}}
    <x-card title="Matrix Skill - Office" shadow class="flex flex-col items-center bg-base-100">
        <div class="w-full max-w-[280px]">
            <canvas x-ref="officeCanvas"></canvas>
        </div>
    </x-card>

    {{-- Genba Skill --}}
    <x-card title="Matrix Skill - Genba" shadow class="flex flex-col items-center bg-base-100">
        <div class="w-full max-w-[280px]">
            <canvas x-ref="genbaCanvas"></canvas>
        </div>
    </x-card>
</div>