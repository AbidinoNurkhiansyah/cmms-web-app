<div>
    <x-header title="Maintenance KYT (Safety)" separator progress-indicator="search, save, delete, gotoPage, nextPage, previousPage">
        <x-slot:actions>
            <x-button label="Tambah KYT" icon="o-plus" wire:click="create" class="btn-primary hidden md:flex" />
        </x-slot:actions>
    </x-header>

    <!-- Tombol Tambah KYT khusus Mobile (Full Width) -->
    <div class="md:hidden w-full mb-4 -mt-2">
        <x-button label="Tambah KYT" icon="o-plus" wire:click="create" class="btn-primary w-full" />
    </div>
</div>
