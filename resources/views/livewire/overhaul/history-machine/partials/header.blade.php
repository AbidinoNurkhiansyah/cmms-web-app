<x-header title="History Machine" subtitle="Data historikal perawatan mesin dan overhaul">
    <x-slot:actions>
        <div class="flex items-center gap-2">
            <!-- Filter Tanggal -->
            <x-input type="date" wire:model.live="filter_tgl_berlaku" class="input-sm" />

            <!-- Filter Asset -->
            <x-choices wire:model.live="filter_asset_id" :options="$this->assets" option-label="machine_name"
                option-value="id" placeholder="Pilih Mesin..." class="min-w-[250px]" icon="o-cpu-chip" single
                searchable />

            <x-input wire:model.live.debounce.300ms="search" icon="o-magnifying-glass" placeholder="Cari data..."
                class="input-sm w-48" clearable />
            <x-button icon="o-arrow-path" wire:click="resetFilters" class="btn-sm btn-ghost" tooltip="Reset Filter"
                spinner />
            <x-button icon="o-plus" class="btn-primary btn-sm" wire:click="openAdd" title="Tambah Data" spinner />
            <x-button icon="o-qr-code" class="btn-info btn-sm text-white" spinner title="Cetak QR Code"
                link="{{ route('overhaul.history-machine.qr-generator') }}" />
        </div>
    </x-slot:actions>
</x-header>