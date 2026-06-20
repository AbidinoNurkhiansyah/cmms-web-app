<!-- Delete Confirmation Modal -->
<x-modal wire:model="deleteModal" class="backdrop-blur">
    <div class="flex flex-col items-center justify-center text-center gap-4 py-4">
        <x-icon name="o-exclamation-triangle" class="w-16 h-16 text-error" />
        <div>
            <h3 class="font-bold text-lg">Hapus Record Ini?</h3>
            <p class="text-base-content/70 mt-2">Data yang sudah dihapus tidak dapat dikembalikan lagi. Anda yakin?
            </p>
        </div>
    </div>

    <x-slot:actions>
        <x-button label="Batal" wire:click="$set('deleteModal', false)" class="btn-ghost" />
        <x-button label="Ya, Hapus" wire:click="deleteRecord" class="btn-error text-white" spinner="deleteRecord" />
    </x-slot:actions>
</x-modal>
