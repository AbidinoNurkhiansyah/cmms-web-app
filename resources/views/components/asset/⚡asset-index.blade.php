<?php

use App\Services\AssetService;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public string $search = '';

    public function with(AssetService $assetService): array
    {
        return [
            'assets' => $assetService->getPaginatedAssets(10, $this->search),
        ];
    }
};
?>

<div>
    <!-- HEADER -->
    <x-header title="Assets" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Search..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button label="Add Asset" icon="o-plus" class="btn-primary" />
        </x-slot:actions>
    </x-header>

    <!-- TABLE -->
    <x-card>
        <x-table :headers="[
            ['key' => 'id', 'label' => '#'],
            ['key' => 'asset_no', 'label' => 'Asset No'],
            ['key' => 'machine_name', 'label' => 'Machine Name'],
            ['key' => 'line_name', 'label' => 'Line'],
            ['key' => 'classification', 'label' => 'Class'],
        ]" :rows="$assets" with-pagination />
    </x-card>
</div>