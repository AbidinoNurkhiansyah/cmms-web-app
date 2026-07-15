<x-header separator>
    <x-slot:title>
        <div class="flex items-center gap-3">
            <x-button link="/maintenance/checksheet" class="btn-circle btn-ghost btn-sm" icon="o-arrow-left" tooltip="Back to Asset Selection" />
            Checksheet Detail: {{ $assetNo }}
        </div>
    </x-slot:title>
    
    <x-slot:subtitle>
        <div class="flex items-center gap-4 mt-2">
            <div class="badge badge-neutral badge-outline gap-2">
                <x-icon name="o-document-text" class="w-4 h-4" />
                Doc No: {{ $docNo }}
            </div>
        </div>
    </x-slot:subtitle>

    <x-slot:actions>
        <x-button link="/checksheet/master" icon="o-cog-6-tooth" class="btn-primary btn-sm" label="Kelola Item" tooltip="Add New / Manage Checksheet Items" />
    </x-slot:actions>
</x-header>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <x-input label="Line Name" value="{{ $lineName }}" readonly />
    <x-input label="Machine Name" value="{{ $machineName }}" readonly />
    <x-input label="PIC" wire:model="picSL" readonly />
</div>
