<!-- Delete Sparepart Modal -->
<x-modal wire:model="deleteSpModal" class="backdrop-blur">
    <div class="flex flex-col items-center text-center py-4">
        <div class="bg-error/10 p-4 rounded-full mb-4">
            <x-icon name="o-exclamation-triangle" class="w-12 h-12 text-error" />
        </div>
        <h2 class="text-xl font-bold mb-2">Delete Sparepart?</h2>
        <p class="text-base-content/70">Are you sure you want to delete this sparepart record? This action cannot be undone.</p>
    </div>

    <x-slot:actions>
        <div class="flex gap-2 w-full justify-center mt-2">
            <x-button label="Cancel" @click="$wire.deleteSpModal = false" class="btn-ghost" />
            <x-button label="Delete" wire:click="deleteSp" class="btn-error" spinner="deleteSp" />
        </div>
    </x-slot:actions>
</x-modal>
