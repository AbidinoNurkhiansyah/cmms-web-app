<!-- Export Options Modal -->
<x-modal wire:model="exportModal" title="Export Options" separator>
    <div class="space-y-4 py-4">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 w-full items-end">
            <x-select label="Line Name" wire:model.live="exportLineName" :options="$exportLineNames" option-value="id" option-label="name" placeholder="Semua Line" />
            <x-select label="Machine Name" wire:model="exportMachineName" :options="$exportMachines" option-value="machine_name" option-label="machine_name" placeholder="Semua Mesin" :disabled="!$exportLineName" />
            <x-select label="Total Stop Line" wire:model="exportTotalStopLine" :options="[['id' => '', 'name' => 'Semua'], ['id' => '30', 'name' => '>= 30 Menit'], ['id' => '60', 'name' => '>= 60 Menit']]" option-value="id" option-label="name" />
            <x-select label="Status" wire:model="exportStatus" :options="[['id' => '', 'name' => 'Semua Status'], ['id' => 'Permanent', 'name' => 'Permanent'], ['id' => 'Temporary', 'name' => 'Temporary']]" option-value="id" option-label="name" />
            <x-input label="Start Date" type="date" wire:model="exportStartDate" />
            <x-input label="End Date" type="date" wire:model="exportEndDate" />
        </div>

        <x-radio label="Export Format" wire:model="exportFormat" :options="[['id' => 'excel', 'name' => 'Excel (.xlsx)'], ['id' => 'pdf', 'name' => 'PDF Document (.pdf)']]" option-value="id" option-label="name" />
    </div>

    <x-slot:actions>
        <x-button label="Cancel" @click="$wire.exportModal = false" class="btn-ghost" />
        <x-button label="Download" wire:click="processExport" icon="o-arrow-down-tray"
            class="btn-success text-white" spinner="processExport" />
    </x-slot:actions>
</x-modal>
