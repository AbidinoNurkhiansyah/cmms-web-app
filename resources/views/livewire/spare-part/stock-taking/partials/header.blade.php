<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div class="flex items-center gap-2">
        <x-button icon="o-plus" class="btn-primary btn-sm" wire:click="createStockTaking" spinner>
            New Record
        </x-button>
    </div>
    
    <div class="flex w-full sm:w-auto items-center gap-2">
        <x-input 
            icon="o-magnifying-glass" 
            placeholder="Search Date (YYYY-MM-DD)..." 
            wire:model.live.debounce.300ms="search" 
            class="input-sm w-full sm:w-64"
            clearable
        />
        <x-button 
            icon="o-x-mark" 
            wire:click="resetFilters" 
            class="btn-sm btn-ghost" 
            tooltip="Reset Filter"
            spinner
        />
    </div>
</div>
