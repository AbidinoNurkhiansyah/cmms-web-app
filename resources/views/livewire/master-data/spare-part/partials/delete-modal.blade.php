<x-modal wire:model="deleteModal" title="Confirm Deletion">
    <div>Are you sure you want to delete this spare part? This action cannot be undone.</div>
    <x-slot:actions>
        <x-button label="Cancel" wire:click="$set('deleteModal', false)" />
        <x-button label="Delete" class="btn-error text-white" wire:click="delete" spinner="delete" />
    </x-slot:actions>
</x-modal>
