<!-- Modal Form -->
<form wire:submit.prevent="save">
    <x-modal wire:model="showModal" title="{{ $editMode ? 'Edit Data Lembur' : 'Tambah Data Lembur' }}" separator>
        <div class="space-y-4">
            <x-choices label="Karyawan" wire:model="user_id" :options="$users" option-label="name" option-sub-label="team"
                searchable single />

            <x-input type="date" label="Tanggal Lembur" wire:model="date" />

            <div class="grid grid-cols-2 gap-4">
                <x-input type="text" label="Total Jam 1" wire:model="hours_1" placeholder="Contoh: 36.5" />
                <x-input type="text" label="Total Jam 2 (Kalkulasi)" wire:model="hours_2" placeholder="Contoh: 70.5" />
            </div>
        </div>

        <x-slot:actions>
            <x-button label="Batal" @click="$wire.showModal = false" class="btn-ghost" />
            <x-button type="submit" label="Simpan" class="btn-primary" spinner="save" />
        </x-slot:actions>
    </x-modal>
</form>