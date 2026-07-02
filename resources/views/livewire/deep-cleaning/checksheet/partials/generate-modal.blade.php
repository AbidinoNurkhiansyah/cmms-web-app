<!-- Generate Modal -->
<x-modal wire:model="generateModal" title="Generate Checksheet Templates">
    <div class="space-y-4">
        <x-select 
            label="Line & Machine" 
            :options="$this->allMachiningCenters" 
            wire:model="generateMachine" 
            placeholder="-- Choose Machine --" />
        
        <x-select 
            label="Year" 
            :options="$this->generateYearOptions" 
            wire:model="generateYear" 
            placeholder="-- Choose Year --" />
        
        <div>
            <label class="label"><span class="label-text font-semibold">Type Checksheet</span></label>
            <div class="flex flex-col sm:flex-row gap-2 sm:gap-4">
                <x-checkbox label="GATA-GATA" wire:model="generateTypes" value="GATA-GATA" />
                <x-checkbox label="CLAMP ARBOR" wire:model="generateTypes" value="CLAMP ARBOR" />
                <x-checkbox label="RUN OUT" wire:model="generateTypes" value="RUN OUT" />
            </div>
        </div>
    </div>
    <x-slot:actions>
        <x-button label="Cancel" @click="$wire.generateModal = false" />
        <x-button label="Generate" class="btn-primary" wire:click="generateChecksheet" spinner="generateChecksheet" />
    </x-slot:actions>
</x-modal>
