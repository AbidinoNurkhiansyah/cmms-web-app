<div x-show="tab === 'work-orders'" class="pb-10">

    <div class="flex items-center gap-3 mb-4">
        <div class="w-1 h-6 bg-info rounded-full"></div>
        <span class="text-sm font-bold uppercase tracking-widest text-info">Work Orders — Status Closed</span>
    </div>

    <x-card class="shadow-sm rounded-2xl border-l-4 border-l-info">
        <div class="overflow-x-auto">
            <table class="table table-sm w-full">
                <thead>
                    <tr class="bg-base-200/50">
                        <th>Date</th><th>Requester</th><th>Line</th><th>Problem</th><th>Completed</th><th>PIC</th><th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($workOrders as $wo)
                    <tr class="hover:bg-base-200/30 transition-colors">
                        <td class="text-gray-500 whitespace-nowrap">{{ $wo->date ? $wo->date->format('d M y') : '-' }}</td>
                        <td class="font-medium">{{ $wo->requester }}</td>
                        <td>{{ $wo->LineName }}</td>
                        <td class="max-w-xs truncate" title="{{ $wo->problem_description }}">{{ $wo->problem_description }}</td>
                        <td class="whitespace-nowrap">{{ $wo->actual_date ? $wo->actual_date->format('d M y') : '-' }}</td>
                        <td>{{ $wo->pic }}</td>
                        <td><x-badge value="Closed" class="badge-success text-white badge-sm" /></td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-gray-400 py-8 italic">Tidak ada Work Order yang selesai.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

</div>
