<x-modal wire:model="editModal" title="Edit Job Description" box-class="w-11/12 max-w-4xl" separator>
    <div class="grid grid-cols-1 gap-3">
        <x-input label="Team / Rank" wire:model="editTeam" placeholder="e.g. LEADER, MEMBER" />
        <x-textarea label="Description" wire:model="editDescription" placeholder="Task description..." rows="3" />
        <x-tags label="Units" wire:model="editUnits" hint="Type a unit and press enter. e.g. TPM, ELECTRICAL" />
    </div>
    <x-slot:actions>
        <x-button label="Cancel" class="btn-ghost" wire:click="$set('editModal', false)" />
        <x-button label="Save Changes" class="btn-primary" wire:click="saveEdit" spinner="saveEdit" />
    </x-slot:actions>
</x-modal>
