<!-- Table -->
<x-card class="shadow-sm">
    <div class="overflow-x-auto">
        <table class="table table-zebra w-full">
            <thead class="bg-base-200">
                <tr>
                    <th>Tanggal</th>
                    <th>Seksi</th>
                    <th>Nama Karyawan</th>
                    <th class="text-right">Jam 1</th>
                    <th class="text-right">Jam 2 (Kalkulasi)</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($overtimes as $overtime)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($overtime->date)->format('d M Y') }}</td>
                        <td>
                            <div class="badge badge-neutral">{{ $overtime->user->team ?? '-' }}</div>
                        </td>
                        <td class="font-bold">{{ $overtime->user->name ?? 'Unknown User' }}</td>
                        <td class="text-right">{{ number_format($overtime->hours_1, 1, ',', '.') }}</td>
                        <td class="text-right font-semibold">{{ number_format($overtime->hours_2, 1, ',', '.') }}</td>
                        <td class="text-center">
                            <x-button icon="o-pencil" wire:click="edit({{ $overtime->id }})" class="btn-sm btn-ghost text-info" tooltip="Edit" />
                            <x-button icon="o-trash" wire:click="confirmDelete({{ $overtime->id }})" class="btn-sm btn-ghost text-error" tooltip="Hapus" />
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-gray-500 py-6">Belum ada data lembur yang dicatat pada periode ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="mt-4">
        {{ $overtimes->links() }}
    </div>
</x-card>
