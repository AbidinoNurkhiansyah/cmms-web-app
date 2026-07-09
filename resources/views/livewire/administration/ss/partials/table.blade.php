<div class="mb-6 flex flex-col md:flex-row justify-between items-center gap-4">
    <!-- Pencarian -->
    <div class="w-full md:w-1/3">
        <x-input icon="o-magnifying-glass" wire:model.live.debounce.500ms="search" placeholder="Cari nama atau judul..."
            clearable />
    </div>

    <!-- Navigasi Bulan -->
    <div class="flex items-center gap-4">
        <x-button icon="o-chevron-left" wire:click="previousMonth" class="btn-outline btn-sm" spinner />
        <span class="text-lg font-bold min-w-[150px] text-center">
            {{ \Carbon\Carbon::parse($currentMonth . '-01')->translatedFormat('F Y') }}
        </span>
        <x-button icon="o-chevron-right" wire:click="nextMonth" class="btn-outline btn-sm" spinner />
    </div>
</div>

<x-card>
    <x-table :headers="$this->headers" :rows="$this->suggestionSystems" with-pagination>
        @scope('cell_tgl', $ss)
        {{ \Carbon\Carbon::parse($ss->tgl)->format('d-M') }}
        @endscope

        @scope('cell_score', $ss)
        <span class="text-primary font-bold">{{ $ss->score }}</span>
        @endscope

        @scope('cell_actions', $ss)
        <div class="flex flex-nowrap gap-1 justify-center">
            <x-button icon="o-pencil" wire:click="edit({{ $ss->id }})" class="btn-sm btn-ghost text-blue-500" spinner />
            <x-button icon="o-trash" wire:click="confirmDelete({{ $ss->id }})" class="btn-sm btn-ghost text-error"
                spinner />
        </div>
        @endscope
    </x-table>

    @if($this->suggestionSystems->isEmpty())
        <div class="text-center py-8 text-base-content/60">
            <x-icon name="o-inbox" class="w-12 h-12 mx-auto mb-3 opacity-50" />
            <p>Tidak ada data ditemukan untuk bulan
                {{ \Carbon\Carbon::parse($currentMonth . '-01')->translatedFormat('F Y') }}
                @if($search) dengan kata kunci "{{ $search }}" @endif.
            </p>
        </div>
    @endif
</x-card>

<x-modal wire:model="confirmModal" title="Konfirmasi Hapus" class="backdrop-blur-sm">
    <div class="py-4 text-base-content/80 text-center">
        <x-icon name="o-exclamation-triangle" class="w-16 h-16 mx-auto text-warning mb-4" />
        <p class="text-lg">Apakah Anda yakin ingin menghapus data usulan ini?</p>
        <p class="text-sm opacity-70 mt-1">Tindakan ini tidak dapat dibatalkan.</p>
    </div>
    <x-slot:actions>
        <x-button label="Batal" wire:click="$set('confirmModal', false)" class="btn-ghost" />
        <x-button label="Ya, Hapus" wire:click="executeDelete" icon="o-trash" class="btn-error text-white"
            spinner="executeDelete" />
    </x-slot:actions>
</x-modal>