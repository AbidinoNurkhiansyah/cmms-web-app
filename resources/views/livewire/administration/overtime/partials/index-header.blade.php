<x-header title="Rekap SPL" subtitle="Laporan Over Time Karyawan" separator>
    <x-slot:actions>
        <x-button label="Kelola Data Lembur" icon="o-pencil-square" link="{{ route('administration.overtime.manage') }}" class="btn-outline btn-primary" />
        <x-dropdown label="Pilih Periode" class="btn-primary" right>
            <x-menu-item title="SPL Terbaru" wire:click="setFilter('spl', 0)" />
            <x-menu-item title="SPL Bulan Lalu" wire:click="setFilter('spl', 1)" />
            <x-menu-separator />
            <x-menu-item title="Kalender Bulan Ini" wire:click="setFilter('kalender', 0)" />
            <x-menu-item title="Kalender Bulan Lalu" wire:click="setFilter('kalender', 1)" />
        </x-dropdown>
    </x-slot:actions>
</x-header>

<div class="mb-6 alert alert-info text-center font-bold">
    {{ $this->dates['label'] }}
</div>
