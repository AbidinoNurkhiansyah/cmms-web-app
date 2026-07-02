<x-header title="Overhaul Report" separator progress-indicator>
    <x-slot:middle class="!justify-end gap-2">
        <x-input placeholder="Search description, machine..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
    </x-slot:middle>
    <x-slot:actions>
        <x-button label="Add Overhaul Report" icon="o-plus" class="btn-primary" wire:click="openAdd" />
    </x-slot:actions>
</x-header>
