{{-- Form Modal --}}
<x-modal wire:model="formModal" title="{{ $editId ? 'Edit Skill' : 'Add New Skill' }}" separator>
    <div class="grid grid-cols-1 gap-4">
        
        <x-select 
            label="Category" 
            wire:model="category" 
            :options="$categoryOptions" 
            placeholder="Select Category" 
        />

        <x-input 
            label="Skill Name" 
            wire:model="skillName" 
            placeholder="e.g. PLC Programming, Safety ISO, dll" 
        />

        <div class="grid grid-cols-2 gap-4">
            <x-input 
                label="Actual Level (0-4)" 
                wire:model.live="actualLevel" 
                type="number" 
                min="0" max="4" 
            />
            <x-input 
                label="Target Level (1-4)" 
                wire:model.live="targetLevel" 
                type="number" 
                min="1" max="4" 
            />
        </div>

    </div>

    <x-slot:actions>
        <x-button label="Cancel" @click="$wire.formModal = false" class="btn-ghost" />
        <x-button label="{{ $editId ? 'Update' : 'Save' }}" wire:click="save" class="btn-primary" spinner="save" />
    </x-slot:actions>
</x-modal>
