<x-modal wire:model="deleteModal" title="Delete Record" separator>
    <div class="py-4">
        Are you sure you want to delete this record? This action cannot be undone.
    </div>
    <x-slot:actions>
        <x-button label="Cancel" class="btn-ghost" wire:click="$set('deleteModal',false)" />
        <x-button label="Delete" class="btn-error" wire:click="deleteRecord" spinner="deleteRecord" />
    </x-slot:actions>
</x-modal>
