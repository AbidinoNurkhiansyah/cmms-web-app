{{-- Progress Dashboard / Stat Cards --}}
<div class="mb-2">
    <h2 class="text-lg font-bold mb-2">Progress Summary</h2>
    <div class="flex overflow-x-auto gap-3 lg:gap-4 pb-2 snap-x">
        @forelse($progressSummary as $stat)
            <div class="snap-start shrink-0">
                <x-stat title="{{ $stat->pic ?: 'Unassigned Team' }}"
                    value="{{ $stat->open_count + $stat->progress_count + $stat->closed_count }} Total"
                    class="shadow !px-4 !py-3">

                    <x-slot:description>
                        <div class="flex gap-1.5 text-[12px] 2xl:text-xs mt-2 whitespace-nowrap">
                            <span class="text-error font-medium">Open: {{ $stat->open_count }}</span>
                            <span class="text-warning font-medium">Prog: {{ $stat->progress_count }}</span>
                            <span class="text-success font-medium">Done: {{ $stat->closed_count }}</span>
                        </div>
                    </x-slot:description>
                </x-stat>
            </div>
        @empty
            <div class="w-full text-center text-gray-500 py-4">No progress data available yet.</div>
        @endforelse
    </div>
</div>