{{-- Activity Charts (6 months) --}}
<div class="flex items-center justify-between mb-2 mt-2">
    <h5 class="font-bold opacity-70 uppercase tracking-wider text-sm">Activity Trends (6 Months)</h5>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">

    <div class="card bg-base-100 shadow-sm border border-base-200">
        <div class="card-body p-5">
            <div class="flex items-center justify-between mb-4">
                <h6 class="font-bold text-sm">Deep Cleaning</h6>
                <div class="w-2 h-2 rounded-full bg-[#E30701]"></div>
            </div>
            <canvas id="tpmChart" class="w-full" style="max-height:200px"></canvas>
        </div>
    </div>

    <div class="card bg-base-100 shadow-sm border border-base-200">
        <div class="card-body p-5">
            <div class="flex items-center justify-between mb-4">
                <h6 class="font-bold text-sm">Cardty</h6>
                <div class="w-2 h-2 rounded-full bg-[#E30701]"></div>
            </div>
            <canvas id="cartyChart" class="w-full" style="max-height:200px"></canvas>
        </div>
    </div>

    <div class="card bg-base-100 shadow-sm border border-base-200">
        <div class="card-body p-5">
            <div class="flex items-center justify-between mb-4">
                <h6 class="font-bold text-sm">Overhaul</h6>
                <div class="w-2 h-2 rounded-full bg-[#E30701]"></div>
            </div>
            <canvas id="ohChart" class="w-full" style="max-height:200px"></canvas>
        </div>
    </div>

    <div class="card bg-base-100 shadow-sm border border-base-200">
        <div class="card-body p-5">
            <div class="flex items-center justify-between mb-4">
                <h6 class="font-bold text-sm">Work Orders</h6>
                <div class="w-2 h-2 rounded-full bg-[#E30701]"></div>
            </div>
            <canvas id="woChart" class="w-full" style="max-height:200px"></canvas>
        </div>
    </div>

</div>

@script
<script>
    const labels = @json($labels);
    const tpmData = @json($tpmData);
    const cartyData = @json($cartyData);
    const ohData = @json($ohData);
    const woData = @json($woData);

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
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0, color: '#9ca3af' },
                        border: { display: false },
                        grid: { color: '#37415140' } // very subtle grid line
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#9ca3af' },
                        border: { display: false }
                    }
                }
            }
        });
    }

    makeChart('tpmChart', tpmData, '#E30701');
    makeChart('cartyChart', cartyData, '#E30701');
    makeChart('ohChart', ohData, '#E30701');
    makeChart('woChart', woData, '#E30701');
</script>
@endscript