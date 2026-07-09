<x-header title="My Info Administration" separator>
    <x-slot:middle class="!justify-end">
        <x-input placeholder="Search title, source, user..." wire:model.live.debounce="search" clearable
            icon="o-magnifying-glass" />
    </x-slot:middle>
    <x-slot:actions>
        <div class="w-full sm:w-auto flex mt-2 sm:mt-0">
            <x-button label="Add Info" icon="o-plus" class="btn-primary w-full sm:w-auto flex-1" wire:click="openAdd" spinner />
        </div>
    </x-slot:actions>
</x-header>