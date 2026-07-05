<x-modal wire:model="isModalOpen" title="Tambah Data Stock Taking" box-class="max-w-2xl">
    <form wire:submit="saveStockTaking">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
            
            <x-input 
                label="Tanggal Stock" 
                type="date" 
                wire:model="date_stock" 
                icon="o-calendar" 
                required 
            />

            <x-choices 
                label="Spare Part" 
                wire:model="spare_part_id" 
                :options="$this->spareParts" 
                option-label="part_number" 
                option-sub-label="part_name" 
                icon="o-cog"
                single
                searchable 
                required 
            />
            
            <x-input 
                label="Last Stock (System)" 
                type="number" 
                wire:model="last_stock" 
                required 
            />
            
            <x-input 
                label="Check Stock (Physical)" 
                type="number" 
                wire:model="check_stock" 
                required 
            />
            
            <x-input 
                label="In Qty" 
                type="number" 
                wire:model="in_qty" 
                required 
            />
            
            <x-input 
                label="Out Qty" 
                type="number" 
                wire:model="out_qty" 
                required 
            />

        </div>

        <div class="mb-4">
            <x-textarea 
                label="Remark (Optional)" 
                wire:model="remark" 
                rows="2" 
                placeholder="Alasan selisih stok dll..." 
            />
        </div>

        <x-slot:actions>
            <x-button label="Batal" @click="$wire.isModalOpen = false" class="btn-ghost" />
            <x-button label="Simpan" type="submit" class="btn-primary" spinner="saveStockTaking" icon="o-check" />
        </x-slot:actions>
    </form>
</x-modal>
