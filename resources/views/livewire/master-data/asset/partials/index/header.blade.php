    <x-header title="Asset Management" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Search asset..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button label="Add Asset" icon="o-plus" class="btn-primary" wire:click="openAdd" />
        </x-slot:actions>
    </x-header>
