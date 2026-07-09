<x-modal wire:model="kytModal" title="{{ $kytNo ? 'Edit Data KYT' : 'Tambah Data KYT' }}" class="backdrop-blur-sm" box-class="w-11/12 max-w-5xl">
    <x-form wire:submit="save">
        <div class="flex flex-col md:flex-row gap-6">
            <!-- Kolom Kiri: Upload & Preview Gambar -->
            <div class="w-full md:w-5/12 flex flex-col justify-start">
                <x-file label="Foto Dokumentasi (Max 5MB)" wire:model="img" accept="image/*" class="w-full mb-4" />
                
                <div class="w-full flex justify-center">
                    @if ($img)
                        <div class="text-center w-full">
                            <span class="text-xs text-gray-500 block mb-2">Preview Baru:</span>
                            <img src="{{ $img->temporaryUrl() }}" class="w-full rounded shadow object-contain border border-base-200">
                        </div>
                    @elseif ($oldImg)
                        <div class="text-center w-full">
                            <span class="text-xs text-gray-500 block mb-2">Foto Saat Ini:</span>
                            <img src="{{ $this->getImageUrl($oldImg) }}" class="w-full rounded shadow object-contain border border-base-200">
                        </div>
                    @else
                        <div class="w-full aspect-square bg-base-200 flex flex-col items-center justify-center rounded border border-dashed border-base-300 text-gray-400">
                            <x-icon name="o-photo" class="w-16 h-16 opacity-50 mb-2" />
                            <p class="text-sm text-center">Belum ada foto<br><span class="text-xs opacity-70">(Pilih file di atas)</span></p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Kolom Kanan: Form Input -->
            <div class="w-full md:w-7/12 space-y-3">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <x-input label="Tanggal" type="date" wire:model="date" />
                    
                    <x-choices 
                        label="User" 
                        wire:model="userId" 
                        :options="$this->usersOption" 
                        option-label="name" 
                        option-value="jid_no"
                        single 
                        searchable 
                    />
                </div>

                <x-input label="Lokasi" wire:model="lokasi" placeholder="Lokasi pekerjaan..." />

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <x-textarea label="Bahaya" wire:model="bahaya" placeholder="Potensi bahaya..." rows="2" />
                    <x-textarea label="Resiko" wire:model="resiko" placeholder="Resiko yang dapat terjadi..." rows="2" />
                </div>
                
                <x-textarea label="Pencegahan / Countermeasure" wire:model="countermeasure" placeholder="Tindakan pencegahan..." rows="2" />
            </div>
        </div>

        <x-slot:actions>
            <x-button label="Batal" wire:click="$set('kytModal', false)" class="btn-ghost" />
            <x-button label="Simpan" type="submit" class="btn-primary" spinner="save" />
        </x-slot:actions>
    </x-form>
</x-modal>
