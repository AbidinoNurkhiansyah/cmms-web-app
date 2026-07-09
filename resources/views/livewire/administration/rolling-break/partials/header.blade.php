<x-header title="Rolling Break (Late)" subtitle="List Data Rolling Break" separator>
    <x-slot:middle class="!justify-end gap-2">


        <x-input 
            icon="o-magnifying-glass" 
            wire:model.live.debounce.300ms="search" 
            placeholder="Cari nama, catatan..." 
            class="w-full lg:w-64"
            clearable 
        />
    </x-slot:middle>
    <x-slot:actions>
        <x-button 
            icon="o-plus" 
            class="btn-primary hidden lg:flex" 
            wire:click="create" 
        >
            Tambah Data
        </x-button>
    </x-slot:actions>
</x-header>

<!-- Mobile Button -->
<div class="block lg:hidden w-full mb-4">
    <x-button 
        icon="o-plus" 
        class="btn-primary w-full" 
        wire:click="create" 
    >
        Tambah Data
    </x-button>
</div>
