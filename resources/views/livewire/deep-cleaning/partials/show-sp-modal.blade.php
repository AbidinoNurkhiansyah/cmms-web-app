<!-- Add / Edit Sparepart Modal -->
<x-modal wire:model="spModal" class="backdrop-blur">
    <x-slot:title>
        {{ $editingSpId ? 'Edit Sparepart' : 'Add Sparepart' }}
    </x-slot:title>
    
    <form wire:submit.prevent="saveSp">
        <div class="grid grid-cols-1 gap-4 mb-4">
            <x-input label="Sparepart Name / ID" wire:model="sp_id" placeholder="Ex: SP-12345 or V-Belt" required />
            
            <div class="grid grid-cols-2 gap-4">
                <x-input label="Qty" type="number" wire:model="sp_qty" min="1" required />
                <x-input label="Used For (Optional Item Check)" wire:model="sp_itemcheck" placeholder="Ex: Motor Fan" />
            </div>
        </div>

        <x-slot:actions>
            <x-button label="Cancel" @click="$wire.spModal = false" />
            <x-button type="submit" label="{{ $editingSpId ? 'Update' : 'Save' }}" class="btn-primary" spinner="saveSp" />
        </x-slot:actions>
    </form>
</x-modal>
