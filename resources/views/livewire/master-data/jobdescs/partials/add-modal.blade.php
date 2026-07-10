<x-modal wire:model="addModal" title="Add Job Description" box-class="w-11/12 max-w-4xl" separator>
    <div class="grid grid-cols-1 gap-3">
        <x-input label="Team / Rank" wire:model="addTeam" placeholder="e.g. LEADER, MEMBER" />
        <x-textarea label="Description" wire:model="addDescription" placeholder="Task description..." rows="3" />
        
        <!-- Assuming we use x-tags for arbitrary array of strings, or x-choices if we load standard units. 
             x-tags allows entering new units easily by pressing enter. -->
        <x-tags label="Units" wire:model="addUnits" hint="Type a unit and press enter. e.g. TPM, ELECTRICAL" />
    </div>
    <x-slot:actions>
        <x-button label="Cancel" class="btn-ghost" wire:click="$set('addModal', false)" />
        <x-button label="Save" class="btn-primary" wire:click="saveAdd" spinner="saveAdd" />
    </x-slot:actions>
</x-modal>
