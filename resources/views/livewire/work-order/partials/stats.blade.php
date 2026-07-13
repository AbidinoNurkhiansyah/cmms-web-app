{{-- Progress Dashboard / Stat Cards --}}
<div class="mb-4">
    <h2 class="text-xl font-bold mb-4">Progress Summary</h2>
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-3 lg:gap-4">
        @forelse($progressSummary as $stat)
            <x-stat title="{{ $stat->pic ?: 'Unassigned Team' }}"
                value="{{ $stat->open_count + $stat->progress_count + $stat->closed_count }} Total" class="shadow">
                <x-slot:figure>
                    <div class="stat-figure hidden md:block">
                        <x-icon name="o-chart-bar" class="w-11 h-11 text-base-content/20" />
                    </div>
                </x-slot:figure>
                <x-slot:description>
                    <div class="flex gap-1.5 text-[12px] 2xl:text-xs mt-2 whitespace-nowrap">
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