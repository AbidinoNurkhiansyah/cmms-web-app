<x-header title="Suggestion System (SS)" separator progress-indicator="search, save, delete, gotoPage, nextPage, previousPage">
    <x-slot:actions>
        <div class="hidden sm:flex items-center gap-2">
            <x-button label="Tambah" icon="o-plus" wire:click="create" class="btn-primary" />
            <x-button label="Monthly" link="{{ route('administration.ss.monthly') }}" class="btn-outline" />
        </div>
    </x-slot:actions>
</x-header>

<!-- Mobile Actions (Full Width) -->
<div class="flex sm:hidden w-full gap-2 mb-6 -mt-2">
    <x-button label="Tambah" icon="o-plus" wire:click="create" class="btn-primary flex-1" />
    <x-button label="Monthly" link="{{ route('administration.ss.monthly') }}" class="btn-outline flex-1" />
</div>
