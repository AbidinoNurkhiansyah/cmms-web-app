<x-header title="Master Check Sheet" subtitle="Kelola template standar pengecekan mesin" separator>
    <x-slot:actions>
        <x-button
            label="Revisi Dokumen"
            icon="o-pencil-square"
            class="btn-outline btn-sm"
            wire:click="openRevisiModal"
            spinner />
        <x-button
            label="Tambah Item"
            icon="o-plus"
            class="btn-primary btn-sm"
            wire:click="openAddModal"
            spinner />
    </x-slot:actions>
</x-header>
