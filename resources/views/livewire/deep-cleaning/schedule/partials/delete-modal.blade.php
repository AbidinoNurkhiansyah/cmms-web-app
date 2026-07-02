    {{-- Delete Modal --}}
    <x-modal wire:model="deleteModal" title="Hapus Jadwal" separator>
        <div class="py-4">Apakah Anda yakin ingin menghapus jadwal ini?</div>
        <x-slot:actions>
            <x-button label="Batal" @click="$wire.deleteModal = false" />
            <x-button label="Ya, Hapus" class="btn-error text-white" wire:click="deleteSchedule" />
        </x-slot:actions>
    </x-modal>
