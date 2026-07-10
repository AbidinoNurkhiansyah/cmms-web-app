<x-modal wire:model="deleteModal" title="Confirm Delete" separator>
    <div class="py-4">
        Are you sure you want to delete this user? This action cannot be undone.
    </div>
    <x-slot:actions>
        <x-button label="Cancel" class="btn-ghost" wire:click="$set('deleteModal', false)" />
        <x-button label="Delete" class="btn-error text-white" wire:click="deleteUser" spinner="deleteUser" />
    </x-slot:actions>
</x-modal>