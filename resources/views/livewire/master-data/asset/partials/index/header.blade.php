<x-header title="Asset Management" separator progress-indicator="search">
    <x-slot:middle class="!justify-end">
        <x-input placeholder="Search asset..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
    </x-slot:middle>
    <x-slot:actions>
        <x-button label="Add Asset" icon="o-plus" class="btn-primary" wire:click="openAdd" spinner />
    </x-slot:actions>
</x-header>