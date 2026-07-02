<!-- Filters -->
<x-card class="mb-6">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 items-end">
        <div class="col-span-1">
            <x-select 
                label="Type" 
                icon="o-tag" 
                :options="$this->availableTypes" 
                wire:model.live="selectedType" 
                placeholder="-- Choose Type --" />
        </div>
        <div class="col-span-1">
            <x-select 
                label="Year" 
                icon="o-calendar" 
                :options="$this->availableYears" 
                wire:model.live="selectedYear" 
                placeholder="-- Choose Year --" 
                :disabled="!$selectedType" />
        </div>
        <div class="col-span-2 flex flex-col md:flex-row gap-4 md:items-end">
            <div class="flex-1 w-full">
                <x-select 
                    label="Machine" 
                    icon="o-cog" 
                    :options="$this->availableMachines" 
                    wire:model.live="selectedMachine" 
                    placeholder="-- Choose Machine --" 
                    :disabled="!$selectedYear || count($this->availableMachines) == 0" />
            </div>
            
            <div class="flex gap-2 w-full md:w-auto mt-2 md:mt-0">
                <x-button label="Input Data" class="btn-success text-white flex-1 md:flex-none" @click="$wire.inputModal = true" :disabled="!$selectedType || !$selectedYear || !$selectedMachine" />
                
                @if($canEdit && $selectedType && $selectedYear && $selectedMachine)
                    <x-button label="Edit Data" class="btn-warning flex-1 md:flex-none" @click="$wire.inputModal = true" />
                @endif
            </div>
        </div>
    </div>
</x-card>
