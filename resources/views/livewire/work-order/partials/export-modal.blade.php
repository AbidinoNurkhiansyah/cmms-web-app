<x-modal wire:model="exportModal" title="Export Work Orders" subtitle="Filter data before exporting to Excel" separator>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
        <x-datetime label="Start Date" wire:model="export_start_date" icon="o-calendar" />
        <x-datetime label="End Date" wire:model="export_end_date" icon="o-calendar" />
        
        <x-select label="Team / PIC" wire:model="export_team" :options="$this->teamOptions" option-value="id" option-label="name" placeholder="All Teams" />
        
        <x-select label="Order Type" wire:model="export_order_type" :options="[
            ['id' => '', 'name' => 'All Types'],
            ['id' => 'Install', 'name' => 'Install'],
            ['id' => 'Repair', 'name' => 'Repair'],
            ['id' => 'Kaizen', 'name' => 'Kaizen'],
        ]" />

        <x-select label="Status" wire:model="export_status" :options="[
            ['id' => '', 'name' => 'All Status'],
            ['id' => 'Open', 'name' => 'Open'],
            ['id' => 'In Progress', 'name' => 'In Progress'],
            ['id' => 'Done', 'name' => 'Done'],
        ]" />
    </div>
    
    <x-slot:actions>
        <x-button label="Cancel" wire:click="$set('exportModal', false)" />
        <x-button label="Download Excel" icon="o-document-arrow-down" class="btn-success" wire:click="downloadExport" spinner />
    </x-slot:actions>
</x-modal>
