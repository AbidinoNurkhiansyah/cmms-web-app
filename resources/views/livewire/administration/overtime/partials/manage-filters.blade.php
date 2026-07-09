<!-- Filters -->
<x-card class="mb-6 shadow-sm">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <x-input label="Cari Karyawan / Seksi" icon="o-magnifying-glass" wire:model.live.debounce.300ms="search" placeholder="Ketik nama atau seksi..." />
        <x-input type="month" label="Bulan Lembur" icon="o-calendar" wire:model.live="searchMonth" />
    </div>
</x-card>
