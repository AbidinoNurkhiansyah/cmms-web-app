<div x-show="tab === 'maintenance'" class="space-y-2 pb-10">

    {{-- MTC Team A --}}
    <div class="flex items-center gap-3 mb-4">
        <div class="w-1 h-6 bg-primary rounded-full"></div>
        <span class="text-sm font-bold uppercase tracking-widest text-primary">MTC Team A — Downtime &gt; 30 Min</span>
    </div>
    <x-card class="shadow-sm rounded-2xl border-l-4 border-l-primary">
        <div class="overflow-x-auto">
            <table class="table table-sm w-full">
                <thead>
                    <tr class="bg-base-200/50">
                        <th>Date</th><th>Line</th><th>Machine</th><th>Downtime</th><th>Problem</th><th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mtcA_records as $r)
                    <tr class="hover:bg-base-200/30 transition-colors">
                        <td class="text-gray-500 whitespace-nowrap">{{ $r->Date ? $r->Date->format('d M') : '-' }}</td>
                        <td>{{ $r->LineName }}</td>
                        <td class="font-medium">{{ $r->MachineName }}</td>
                        <td><span class="badge badge-error badge-sm text-white font-bold">{{ $r->DownTime }}m</span></td>
                        <td>{{ $r->Problem }}</td>
                        <td>{{ $r->Status }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-gray-400 py-8 italic">Tidak ada downtime signifikan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    {{-- MTC Team B --}}
    <div class="flex items-center gap-3 mt-8 mb-4">
        <div class="w-1 h-6 bg-secondary rounded-full"></div>
        <span class="text-sm font-bold uppercase tracking-widest text-secondary">MTC Team B — Downtime &gt; 30 Min</span>
    </div>
    <x-card class="shadow-sm rounded-2xl border-l-4 border-l-secondary">
        <div class="overflow-x-auto">
            <table class="table table-sm w-full">
                <thead>
                    <tr class="bg-base-200/50">
                        <th>Date</th><th>Line</th><th>Machine</th><th>Downtime</th><th>Problem</th><th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mtcB_records as $r)
                    <tr class="hover:bg-base-200/30 transition-colors">
                        <td class="text-gray-500 whitespace-nowrap">{{ $r->Date ? $r->Date->format('d M') : '-' }}</td>
                        <td>{{ $r->LineName }}</td>
                        <td class="font-medium">{{ $r->MachineName }}</td>
                        <td><span class="badge badge-error badge-sm text-white font-bold">{{ $r->DownTime }}m</span></td>
                        <td>{{ $r->Problem }}</td>
                        <td>{{ $r->Status }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-gray-400 py-8 italic">Tidak ada downtime signifikan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

</div>
