<div x-show="tab === 'part-repair'" class="pb-10">

    <div class="flex items-center gap-3 mb-4">
        <div class="w-1 h-6 bg-accent rounded-full"></div>
        <span class="text-sm font-bold uppercase tracking-widest text-accent">Part Repair</span>
    </div>

    <x-card class="shadow-sm rounded-2xl border-l-4 border-l-accent">
        <div class="overflow-x-auto">
            <table class="table table-sm w-full">
                <thead>
                    <tr class="bg-base-200/50">
                        <th>Date</th><th>Part Number</th><th>Item Repair</th><th>PIC</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($partRepairs as $pr)
                    <tr class="hover:bg-base-200/30 transition-colors">
                        <td class="text-gray-500 whitespace-nowrap">{{ $pr->date ? $pr->date->format('d M y') : '-' }}</td>
                        <td class="font-medium text-accent">{{ $pr->sparePart->part_no ?? '-' }}</td>
                        <td>{{ $pr->item_repair }}</td>
                        <td class="text-sm">
                            @php $pics = array_filter([$pr->pic1->name ?? '', $pr->pic2->name ?? '', $pr->pic3->name ?? '']); @endphp
                            {{ implode(', ', $pics) }}
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center text-gray-400 py-8 italic">Tidak ada Part Repair.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

</div>
