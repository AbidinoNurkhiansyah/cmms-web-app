<x-modal wire:model="ssModal" title="{{ $ssId ? 'Edit SS' : 'Tambah SS' }}">
    <x-form wire:submit="save">
        <x-input label="Tanggal" type="date" wire:model="tgl" />
        
        <x-choices 
            label="Nama Karyawan" 
            wire:model="user_id" 
            :options="$this->usersOption" 
            option-label="name" 
            option-value="id"
            single 
            searchable 
        />
        
        <x-input label="Judul SS" wire:model="ss_title" placeholder="Masukkan judul usulan..." />
        
        <x-input label="Score" type="number" wire:model="score" placeholder="Masukkan nilai..." />

        <x-slot:actions>
            <x-button label="Batal" wire:click="$set('ssModal', false)" class="btn-ghost" />
            <x-button label="Simpan" type="submit" class="btn-primary" spinner="save" />
        </x-slot:actions>
    </x-form>
</x-modal>
