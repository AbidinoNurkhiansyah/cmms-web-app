<div class="flex items-center gap-4 mb-4">
    <x-button icon="o-arrow-left" link="{{ route('administration.overtime') }}" class="btn-circle btn-ghost bg-base-200" tooltip="Kembali ke Rekap" />
    <div class="flex-1">
        <x-header title="Kelola Data Lembur" subtitle="Manajemen Data SPL Karyawan" class="!mb-0">
            <x-slot:actions>
                <x-button label="Tambah Data" icon="o-plus" wire:click="create" class="btn-primary" responsive />
            </x-slot:actions>
        </x-header>
    </div>
</div>
<div class="divider mt-0"></div>
