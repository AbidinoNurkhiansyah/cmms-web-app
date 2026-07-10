<x-modal wire:model="deleteModal" title="Confirm Deletion" separator>
    <div class="py-4">
        Are you sure you want to delete this skill? This action cannot be undone.
    </div>

    <x-slot:actions>
        <x-button label="Cancel" @click="$wire.deleteModal = false" class="btn-ghost" />
        <x-button label="Delete" wire:click="deleteSkill" class="btn-error text-white" spinner />
    </x-slot:actions>
</x-modal>
