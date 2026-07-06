<x-header title="Stock Taking" separator>
    <x-slot:actions>
        <div class="flex w-full sm:w-auto items-center gap-2">
            <x-input 
                type="date"
                wire:model.live="search" 
                class="input-sm w-full sm:w-48"
                clearable
            />
        </div>
    </x-slot:actions>
</x-header>
