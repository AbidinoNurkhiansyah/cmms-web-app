<x-header separator>
    <x-slot:title>
        <div class="flex items-center gap-3">
            <x-button link="/maintenance/checksheet" class="btn-circle btn-ghost btn-sm" icon="o-arrow-left" tooltip="Back to Asset Selection" />
            Checksheet Detail: {{ $assetNo }}
        </div>
    </x-slot:title>
</x-header>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <x-input label="Line Name" value="{{ $lineName }}" readonly />
    <x-input label="Machine Name" value="{{ $machineName }}" readonly />
    <x-input label="PIC" wire:model="picSL" readonly />
</div>
