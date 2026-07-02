<x-card>
    <x-table
        :headers="[
            ['key' => 'id', 'label' => 'No'],
            ['key' => 'date', 'label' => 'Req Date'],
            ['key' => 'LineName', 'label' => 'Line'],
            ['key' => 'MachineName', 'label' => 'Machine'],
            ['key' => 'description', 'label' => 'Description'],
            ['key' => 'actions', 'label' => 'Action', 'sortable' => false, 'class' => 'text-center'],
        ]"
        :rows="$this->records"
        with-pagination
    >
        @scope('cell_id', $r)
            {{ $this->records->firstItem() + $this->records->search(fn($item) => $item->id === $r->id) }}
        @endscope

        @scope('cell_date', $r)
            {{ $r->date ? $r->date->format('Y-m-d') : '-' }}
        @endscope

        @scope('cell_actions', $r)
            <div class="flex gap-1 justify-center">
                <x-button icon="o-pencil-square" class="btn-ghost btn-xs" wire:click="openEdit({{ $r->id }})" />
                <x-button icon="o-trash" class="btn-ghost btn-xs text-error" 
                    wire:click="deleteRecord({{ $r->id }})" 
                    wire:confirm="Delete this record? This cannot be undone." />
            </div>
        @endscope
    </x-table>
</x-card>
