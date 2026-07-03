<x-card>
    <x-table
        :headers="[
            ['key' => 'id', 'label' => 'No'],
            ['key' => 'date', 'label' => 'Req Date'],
            ['key' => 'LineName', 'label' => 'Line'],
            ['key' => 'MachineName', 'label' => 'Machine'],
            ['key' => 'problem', 'label' => 'Problem'],
            ['key' => 'actions', 'label' => 'Action', 'sortable' => false, 'class' => 'text-center w-24'],
        ]"
        :rows="$this->records"
        with-pagination
        @row-click="Livewire.navigate('{{ route('overhaul.index') }}/' + $event.detail.id)"
        class="hover:cursor-pointer"
    >
        @scope('cell_id', $r)
            {{ $this->records->firstItem() + $this->records->search(fn($item) => $item->id === $r->id) }}
        @endscope

        @scope('cell_date', $r)
            {{ $r->date ? $r->date->format('Y-m-d') : '-' }}
        @endscope

        @scope('cell_actions', $r)
            <div class="flex gap-1 justify-center" @click.stop>
                <x-button icon="o-pencil-square" class="btn-ghost btn-xs" wire:click="openEdit({{ $r->id }})" spinner />
                <x-button icon="o-trash" class="btn-ghost btn-xs text-error" 
                    wire:click="deleteRecord({{ $r->id }})" 
                    wire:confirm="Delete this record? This cannot be undone."
                    spinner />
            </div>
        @endscope
    </x-table>
</x-card>
