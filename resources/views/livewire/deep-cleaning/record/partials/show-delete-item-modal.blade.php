<x-modal wire:model="deleteItemModal" title="Confirm Delete" separator>
    <p>Are you sure you want to delete this finding? This action cannot be undone.</p>
    <x-slot:actions>
        <x-button label="Cancel" class="btn-ghost" wire:click="$set('deleteItemModal',false)" />
        <x-button label="Delete" class="btn-error" wire:click="deleteItem" spinner="deleteItem" />
    </x-slot:actions>
</x-modal>
