<?php

use Livewire\Volt\Component;

new class extends Component {
    public string $assetNo;

    public function mount(string $assetNo)
    {
        $this->assetNo = $assetNo;
    }
};
?>

<div>
    <x-header title="Checksheet Detail: {{ $assetNo }}" separator>
        <x-slot:actions>
            <x-button label="Back to Asset Selection" link="/maintenance/checksheet" class="btn-ghost" icon="o-arrow-left" />
        </x-slot:actions>
    </x-header>

    <x-card class="mt-6">
        <div class="flex flex-col items-center justify-center p-10 text-center">
            <x-icon name="o-wrench-screwdriver" class="w-24 h-24 text-gray-300 mb-4" />
            <h2 class="text-2xl font-bold mb-2">Checksheet Detail Page</h2>
            <p class="text-gray-500 mb-6">
                This page is a placeholder for the detailed checksheet of asset <strong>{{ $assetNo }}</strong>. <br>
                The form and item checklists will be implemented here.
            </p>
        </div>
    </x-card>
</div>
