<x-modal wire:model="deleteModal" title="Confirm Deletion" separator>
    <div>Are you sure you want to delete this record? This action cannot be undone.</div>
    <x-slot:actions>
        <x-button label="Cancel" wire:click="$set('deleteModal', false)" />
        <x-button label="Delete" class="btn-error text-white" wire:click="deleteRecord" spinner />
    </x-slot:actions>
</x-modal>
