<x-card class="mt-4">
    <x-table :headers="$this->headers" :rows="$this->records" with-pagination striped>
        @scope('cell_date_input', $record)
        {{ $record->date_input ? $record->date_input->format('d M H:i') : '-' }}
        @endscope

        @scope('cell_shift', $record)
        {{ $record->shift == '1' ? 'Shift 1' : ($record->shift == '2' ? 'Shift 2' : $record->shift) }}
        @endscope



        @scope('cell_actions', $record)
        <div class="flex gap-2 justify-center">
            <x-button icon="o-pencil-square" wire:click="edit({{ $record->id }})" class="btn-sm btn-ghost"
                tooltip="Edit" />
            <x-button icon="o-trash" wire:click="confirmDelete({{ $record->id }})" class="btn-sm btn-ghost text-error"
                tooltip="Hapus" />
        </div>
        @endscope
    </x-table>
</x-card>

<!-- Modal Konfirmasi Hapus -->
<x-modal wire:model="confirmModal" title="Konfirmasi Hapus" subtitle="Data yang dihapus tidak dapat dikembalikan.">
    <x-slot:actions>
        <x-button label="Batal" wire:click="$set('confirmModal', false)" class="btn-ghost" />
        <x-button label="Ya, Hapus" wire:click="executeDelete" class="btn-error text-white" spinner />
    </x-slot:actions>
</x-modal>