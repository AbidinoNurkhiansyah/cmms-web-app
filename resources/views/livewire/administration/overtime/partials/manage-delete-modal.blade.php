<!-- Delete Confirmation Modal -->
<x-modal wire:model="deleteModal" title="Konfirmasi Hapus" separator>
    <div class="py-4">
        Apakah Anda yakin ingin menghapus data lembur ini? Data yang dihapus tidak dapat dikembalikan.
    </div>

    <x-slot:actions>
        <x-button label="Batal" @click="$wire.deleteModal = false" class="btn-ghost" />
        <x-button label="Ya, Hapus" wire:click="executeDelete" class="btn-error text-white" spinner="executeDelete" />
    </x-slot:actions>
</x-modal>