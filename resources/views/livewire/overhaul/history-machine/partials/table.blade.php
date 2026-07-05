<x-card>
    <div class="overflow-x-auto">
        <table class="table table-sm w-full">
            <thead>
                <tr>
                    <th class="w-12 text-center text-xs uppercase bg-base-200">No</th>
                    <th class="text-xs uppercase bg-base-200 whitespace-nowrap">Asset No</th>
                    <th class="text-xs uppercase bg-base-200 whitespace-nowrap">Machine Name</th>
                    <th class="text-xs uppercase bg-base-200 whitespace-nowrap">Tgl Berlaku</th>
                    <th class="text-xs uppercase bg-base-200">Problem</th>
                    <th class="w-28 text-center text-xs uppercase bg-base-200 whitespace-nowrap">Part Change</th>
                    <th class="text-xs uppercase bg-base-200 whitespace-nowrap">PIC</th>
                    <th class="w-32 text-center text-xs uppercase bg-base-200">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($this->records as $index => $record)
                    <tr wire:click="openDetail({{ $record->id }})"
                        class="hover:bg-base-200/50 transition-colors cursor-pointer" spinner>
                        <td class="text-center">{{ $this->records->firstItem() + $index }}</td>
                        <td class="whitespace-nowrap font-medium">{{ $record->asset->asset_no ?? '-' }}</td>
                        <td class="whitespace-nowrap">{{ $record->asset->machine_name ?? '-' }}</td>
                        <td class="whitespace-nowrap">
                            {{ $record->tgl_berlaku ? $record->tgl_berlaku->format('d-M-Y') : '-' }}
                        </td>
                        <td class="text-sm max-w-[200px] truncate" title="{{ $record->problem }}">
                            {{ $record->problem ?? '-' }}
                        </td>
                        <td class="text-center align-middle">
                            @if(is_array($record->part_change) && count($record->part_change) > 0)
                                <x-badge value="{{ count($record->part_change) }} parts" class="badge-neutral" />
                            @else
                                <span class="text-base-content/50">-</span>
                            @endif
                        </td>
                        <td class="whitespace-nowrap">{{ $record->pic->name ?? '-' }}</td>
                        <td>
                            <div class="flex items-center justify-center gap-1">
                                <x-button icon="o-pencil-square" wire:click.stop="openEdit({{ $record->id }})"
                                    class="btn-sm btn-ghost text-info" tooltip="Edit" spinner />
                                <x-button icon="o-trash" wire:click.stop="deleteRecord({{ $record->id }})"
                                    wire:confirm="Are you sure you want to delete this record?"
                                    class="btn-sm btn-ghost text-error" tooltip="Delete" />
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="13" class="text-center py-6 text-base-content/50">
                            <div class="flex flex-col items-center justify-center">
                                <x-icon name="o-inbox" class="w-12 h-12 mb-2 text-base-content/30" />
                                <p>Tidak ada data history machine yang ditemukan.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $this->records->links() }}
    </div>
</x-card>