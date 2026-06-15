<?php

use App\Services\SparePartService;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public string $search = '';

    public function with(SparePartService $sparePartService): array
    {
        return [
            'spareParts' => $sparePartService->getPaginatedSpareParts(10, $this->search),
        ];
    }
};
?>

<div>
    <!-- HEADER -->
    <x-header title="Spare Parts" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Search..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button label="Add Spare Part" icon="o-plus" class="btn-primary" />
        </x-slot:actions>
    </x-header>

    <!-- TABLE -->
    <x-card>
        <x-table :headers="[
            ['key' => 'id', 'label' => '#'],
            ['key' => 'part_number', 'label' => 'Part Number'],
            ['key' => 'group', 'label' => 'Group'],
            ['key' => 'use_qty', 'label' => 'Use Qty'],
            ['key' => 'price_idr', 'label' => 'Price (IDR)'],
        ]" :rows="$spareParts" with-pagination />
    </x-card>
</div>