<x-card>
    <x-table :headers="$headers" :rows="$spareParts" with-pagination>
        @scope('cell_id', $part)
            {{ ($this->getPage() - 1) * 15 + $loop->iteration }}
        @endscope

        @scope('cell_status', $part)
        @if($part->status === 'Y')
            <x-badge value="Active" class="badge-success" />
        @else
            <x-badge value="Discontinued" class="badge-error" />
        @endif
        @endscope

        @scope('cell_actions', $part)
        <div class="flex gap-2 justify-center">
            <x-button icon="o-pencil-square" class="btn-sm btn-ghost text-info" wire:click="openEdit({{ $part->id }})"
                spinner />
            <x-button icon="o-trash" class="btn-sm btn-ghost text-error" wire:click="confirmDelete({{ $part->id }})"
                spinner />
        </div>
        @endscope
    </x-table>
</x-card>