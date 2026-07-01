<!-- Add / Edit Sparepart Modal -->
<x-modal wire:model="spModal" class="backdrop-blur" box-class="overflow-visible">
    <x-slot:title>
        {{ $editingSpId ? 'Edit Sparepart' : 'Add Sparepart' }}
    </x-slot:title>
    
        <div class="grid grid-cols-1 gap-4 mb-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-3">
                    <x-choices label="Sparepart Name / ID" wire:model="sp_id" :options="$spareparts" option-label="part_name" option-value="part_name" searchable search-function="searchSparepart" placeholder="Select Spare Part..." single no-progress debounce="50ms" />
                </div>
                <div class="md:col-span-1">
                    <x-input label="Qty" type="number" wire:model="sp_qty" min="1" required />
                </div>
            </div>
            
            <x-input label="Used For (Optional Item Check)" wire:model="sp_itemcheck" placeholder="Ex: Motor Fan" />
        </div>

        <x-slot:actions>
            <x-button label="Cancel" @click="$wire.spModal = false" />
            <x-button label="{{ $editingSpId ? 'Update' : 'Save' }}" class="btn-primary" wire:click="saveSp" spinner="saveSp" />
        </x-slot:actions>
</x-modal>
