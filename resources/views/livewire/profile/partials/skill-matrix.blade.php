<div class="grid grid-cols-1 md:grid-cols-2 gap-4"
     wire:key="skill-matrix-{{ md5(json_encode($officeSkills) . json_encode($genbaSkills)) }}">

    {{-- Office Skill --}}
    <x-card title="Matrix Skill - Office" shadow class="flex flex-col items-center bg-base-100">
        <div class="w-full max-w-[280px]">
            <canvas id="officeMatrixCanvas"></canvas>
        </div>
    </x-card>

    {{-- Genba Skill --}}
    <x-card title="Matrix Skill - Genba" shadow class="flex flex-col items-center bg-base-100">
        <div class="w-full max-w-[280px]">
            <canvas id="genbaMatrixCanvas"></canvas>
        </div>
    </x-card>
</div>

<script>
(function () {
    const officeSkillsData = @json($officeSkills ?? []);
    const genbaSkillsData  = @json($genbaSkills ?? []);

    function createRadarChart(canvasId, data, color) {
        const canvas = document.getElementById(canvasId);
        if (!canvas) return;

        const existing = Chart.getChart(canvas);
        if (existing) existing.destroy();

        if (!data || data.length === 0) return;

        new Chart(canvas.getContext('2d'), {
            type: 'radar',
            data: {
                labels: data.map(item => item.skill_name),
                datasets: [{
                    label: 'Actual',
                    data: data.map(item => item.actual_level),
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
                        grid: { color: '#e5e7eb' },
                        pointLabels: { font: { size: 10, weight: 'bold' } }
                    }
                },
                plugins: { legend: { display: false } }
            }
        });
    }

    function initCharts() {
        createRadarChart('officeMatrixCanvas', officeSkillsData, {
            bg: 'rgba(235, 105, 54, 0.2)', border: 'rgba(235, 105, 54, 1)'
        });
        createRadarChart('genbaMatrixCanvas', genbaSkillsData, {
            bg: 'rgba(54, 162, 235, 0.2)', border: 'rgba(54, 162, 235, 1)'
        });
    }

    // Run on initial load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCharts);
    } else {
        initCharts();
    }

    // Re-run after every Livewire SPA navigation
    document.addEventListener('livewire:navigated', initCharts);
})();
</script>