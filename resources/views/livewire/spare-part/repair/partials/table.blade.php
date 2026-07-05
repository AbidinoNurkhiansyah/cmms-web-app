<div class="card bg-base-100 shadow-sm border border-base-200">
    <div class="card-body p-0 overflow-x-auto">
        <table class="table table-zebra table-sm">
            <thead class="bg-base-200 text-base-content/80 text-xs uppercase tracking-wider">
                <tr>
                    <th class="px-4 py-3">Date</th>
                    <th class="px-4 py-3">Part No</th>
                    <th class="px-4 py-3 text-center">Qty</th>
                    <th class="px-4 py-3">Item Repair</th>
                    <th class="px-4 py-3">PIC</th>
                    <th class="px-4 py-3 text-right">Price (Rp)</th>
                    <th class="px-4 py-3 text-center w-24">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($this->records as $record)
                    <tr wire:key="repair-{{ $record->id }}" class="hover:bg-base-200/50 transition-colors cursor-pointer"
                        wire:click="goToDetail({{ $record->id }})">
                        <td class="px-4 py-3 text-sm">
                            {{ $record->date ? $record->date->format('d M Y') : '-' }}
                        </td>
                        <td class="px-4 py-3 text-sm font-medium">
                            {{ $record->sparePart->part_number ?? '-' }}
                        </td>
                        <td class="px-4 py-3 text-sm text-center">
                            {{ $record->qty }}
                        </td>
                        <td class="px-4 py-3 text-sm max-w-[200px] truncate" title="{{ $record->item_repair }}">
                            {{ $record->item_repair ?? '-' }}
                        </td>
                        <td class="px-4 py-3 text-xs">
                            <div class="flex flex-wrap gap-1">
                                @if($record->pic1) <span class="badge badge-sm badge-ghost">{{ $record->pic1->name }}</span>
                                @endif
                                @if($record->pic2) <span class="badge badge-sm badge-ghost">{{ $record->pic2->name }}</span>
                                @endif
                                @if($record->pic3) <span class="badge badge-sm badge-ghost">{{ $record->pic3->name }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3 text-sm text-right font-medium text-success">
                            {{ $record->sparePart ? number_format($record->sparePart->price_idr, 0, ',', '.') : '-' }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-1">
                                <x-button icon="o-pencil-square" wire:click.stop="editRepair({{ $record->id }})"
                                    class="btn-sm btn-circle btn-ghost" tooltip="Edit" spinner />
                                <x-button icon="o-trash" wire:click.stop="deleteRepair({{ $record->id }})"
                                    wire:confirm="Hapus data repair ini?" class="btn-sm btn-circle btn-ghost text-error"
                                    tooltip="Delete" />
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-8 text-base-content/60">
                            <x-icon name="o-inbox" class="w-12 h-12 mx-auto mb-3 opacity-20" />
                            <p>Belum ada data Repair.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($this->records->hasPages())
        <div class="card-footer border-t border-base-200 p-4">
            {{ $this->records->links() }}
        </div>
    @endif
</div>