<x-header title="Master Spare Parts" subtitle="Manage master data for spare parts" separator
    progress-indicator="search">
    <x-slot:middle class="!justify-end">
        <x-input placeholder="Search..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
    </x-slot:middle>
    <x-slot:actions>
        <x-button label="Add New" icon="o-plus" class="btn-primary" wire:click="openAdd" spinner />
    </x-slot:actions>
</x-header>