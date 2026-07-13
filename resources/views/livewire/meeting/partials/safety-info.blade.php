<div x-show="tab === 'safety-info'" class="space-y-2 pb-10">

    {{-- Rolling Break --}}
    <div class="flex items-center gap-3 mb-4">
        <div class="w-1 h-6 bg-warning rounded-full"></div>
        <span class="text-sm font-bold uppercase tracking-widest text-warning">Rolling Break (Terlambat)</span>
    </div>
    <x-card class="shadow-sm rounded-2xl border-l-4 border-l-warning">
        <div class="overflow-x-auto">
            <table class="table table-sm w-full">
                <thead>
                    <tr class="bg-base-200/50"><th>Date</th><th>Time</th><th>Nama</th><th>Shift</th><th>Break</th><th>Notes</th></tr>
                </thead>
                <tbody>
                    @forelse($rollingBreaks as $rb)
                    <tr class="hover:bg-base-200/30 transition-colors">
                        <td class="text-gray-500 whitespace-nowrap">{{ $rb->date_input ? $rb->date_input->format('d M') : '-' }}</td>
                        <td class="font-medium">{{ $rb->date_input ? $rb->date_input->format('H:i') : '-' }}</td>
                        <td>{{ $rb->user->name ?? $rb->fullname }}</td>
                        <td>{{ $rb->shift }}</td>
                        <td>{{ $rb->break_time }}</td>
                        <td class="text-gray-500">{{ $rb->notes }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-gray-400 py-8 italic">Tidak ada rolling break terlambat.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    {{-- SKY --}}
    <div class="flex items-center gap-3 mt-8 mb-4">
        <div class="w-1 h-6 bg-success rounded-full"></div>
        <span class="text-sm font-bold uppercase tracking-widest text-success">SKY (Safety)</span>
    </div>
    <x-card class="shadow-sm rounded-2xl border-l-4 border-l-success">
        <div class="overflow-x-auto">
            <table class="table table-sm w-full">
                <thead>
                    <tr class="bg-base-200/50"><th>Nama</th><th>Lokasi</th></tr>
                </thead>
                <tbody>
                    @forelse($skyRecords as $sky)
                    <tr class="hover:bg-base-200/30 transition-colors">
                        <td class="font-medium">{{ $sky->user->name ?? $sky->userId }}</td>
                        <td class="text-gray-500">{{ $sky->lokasi }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="2" class="text-center text-gray-400 py-8 italic">Tidak ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    {{-- My Info --}}
    <div class="flex items-center gap-3 mt-8 mb-4">
        <div class="w-1 h-6 bg-base-content/30 rounded-full"></div>
        <span class="text-sm font-bold uppercase tracking-widest text-base-content/50">My Info</span>
    </div>
    <x-card class="shadow-sm rounded-2xl border-l-4 border-l-base-300">
        <div class="overflow-x-auto">
            <table class="table table-sm w-full">
                <thead>
                    <tr class="bg-base-200/50"><th>Date</th><th>User</th><th>Source</th></tr>
                </thead>
                <tbody>
                    @forelse($myInfos as $info)
                    <tr class="hover:bg-base-200/30 transition-colors">
                        <td class="text-gray-500 whitespace-nowrap">{{ $info->date ? $info->date->format('d M y') : '-' }}</td>
                        <td class="font-medium">{{ $info->user->name ?? $info->user_id }}</td>
                        <td>{{ $info->source }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="text-center text-gray-400 py-8 italic">Tidak ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

</div>
