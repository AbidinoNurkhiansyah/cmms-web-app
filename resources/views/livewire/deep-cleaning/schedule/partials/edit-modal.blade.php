    {{-- Edit Modal --}}
    <x-modal wire:model="editModal" title="Edit Jadwal TPM">
        <div class="grid grid-cols-1 gap-4">
            <x-input type="date" label="Tanggal" wire:model="editPlanDate" />

            <x-choices label="Line" wire:model.live="LineName" :options="$lineNames" option-value="name"
                option-label="name" single />

            @if($LineName)
                <x-choices label="Machine" wire:model.live="asset_id" :options="$machines" option-value="id"
                    option-label="machine_name" single />

                <x-input label="Asset No" wire:model="MachineNo" readonly />
            @endif
        </div>

        <x-slot:actions>
            <x-button label="Cancel" @click="$wire.editModal = false" />
            <x-button label="Save" class="btn-primary" wire:click="saveEdit" />
        </x-slot:actions>
    </x-modal>
