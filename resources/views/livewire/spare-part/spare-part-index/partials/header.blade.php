<x-header title="Spare Part Center" subtitle="List Spare Part" separator>
    <x-slot:middle class="!justify-end">
        <x-input placeholder="Name, ID, Maker..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
    </x-slot:middle>
</x-header>
