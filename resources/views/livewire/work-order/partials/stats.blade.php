{{-- Progress Dashboard / Stat Cards --}}
<div class="mb-6">
    <h2 class="text-xl font-bold mb-4">Progress Summary</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        @forelse($progressSummary as $stat)
            <x-stat 
                title="{{ $stat->pic ?: 'Unassigned Team' }}" 
                value="{{ $stat->open_count + $stat->progress_count + $stat->closed_count }} Total"
                icon="o-chart-bar"
                class="shadow"
            >
                <x-slot:description>
                    <div class="flex gap-3 text-sm mt-2">
                        <span class="text-error font-medium">Open: {{ $stat->open_count }}</span>
                        <span class="text-warning font-medium">Prog: {{ $stat->progress_count }}</span>
                        <span class="text-success font-medium">Done: {{ $stat->closed_count }}</span>
                    </div>
                </x-slot:description>
            </x-stat>
        @empty
            <div class="col-span-full text-center text-gray-500 py-4">No progress data available yet.</div>
        @endforelse
    </div>
</div>
