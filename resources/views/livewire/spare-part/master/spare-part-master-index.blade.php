<?php

use App\Services\SparePartService;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;

new class extends Component {
    use WithPagination, WithFileUploads, Toast;

    public string $search = '';

    // Modals
    public bool $formModal = false;
    public bool $deleteModal = false;

    // Form State
    public ?int $editId = null;
    public string $group = '';
    public string $part_number = '';
    public string $part_name = '';
    public string $no_rack = '';
    public int $last_stock = 0;
    public string $maker = '';
    public string $machine = '';
    public string $status = 'Y';
    public int $use_qty = 0;
    public int $price_idr = 0;
    public $part_photo; // For new uploads
    public ?string $existing_photo = null;

    // For Delete
    public ?int $deleteId = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function openAdd(): void
    {
        $this->resetForm();
        $this->formModal = true;
    }

    public function openEdit(int $id, SparePartService $sparePartService): void
    {
        $this->resetForm();
        $part = $sparePartService->getSparePartById($id);
        
        if ($part) {
            $this->editId = $part->id;
            $this->group = $part->group ?? '';
            $this->part_number = $part->part_number ?? '';
            $this->part_name = $part->part_name ?? '';
            $this->no_rack = $part->no_rack ?? '';
            $this->last_stock = $part->last_stock ?? 0;
            $this->maker = $part->maker ?? '';
            $this->machine = $part->machine ?? '';
            $this->status = $part->status ?? 'Y';
            $this->use_qty = $part->use_qty ?? 0;
            $this->price_idr = $part->price_idr ?? 0;
            $this->existing_photo = $part->part_photo;
            
            $this->formModal = true;
        }
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId = $id;
        $this->deleteModal = true;
    }

    public function save(SparePartService $sparePartService): void
    {
        $this->validate([
            'part_name' => 'required|string|max:255',
            'part_number' => 'nullable|string|max:100',
            'group' => 'nullable|string|max:100',
            'no_rack' => 'nullable|string|max:50',
            'maker' => 'nullable|string|max:100',
            'machine' => 'nullable|string|max:100',
            'last_stock' => 'required|integer|min:0',
            'use_qty' => 'required|integer|min:0',
            'price_idr' => 'required|numeric|min:0',
            'status' => 'required|in:Y,N',
            'part_photo' => 'nullable|image|max:2048', // max 2MB
        ]);

        $data = [
            'part_name' => $this->part_name,
            'part_number' => $this->part_number,
            'group' => $this->group,
            'no_rack' => $this->no_rack,
            'maker' => $this->maker,
            'machine' => $this->machine,
            'last_stock' => $this->last_stock,
            'use_qty' => $this->use_qty,
            'price_idr' => $this->price_idr,
            'status' => $this->status,
        ];

        if ($this->editId) {
            $sparePartService->updateSparePart($this->editId, $data, $this->part_photo);
            $this->success('Spare part updated successfully.');
        } else {
            $sparePartService->createSparePart($data, $this->part_photo);
            $this->success('Spare part created successfully.');
        }

        $this->formModal = false;
        $this->resetForm();
    }

    public function delete(SparePartService $sparePartService): void
    {
        if ($this->deleteId) {
            $sparePartService->deleteSparePart($this->deleteId);
            $this->success('Spare part deleted successfully.');
        }
        $this->deleteModal = false;
        $this->deleteId = null;
    }

    private function resetForm(): void
    {
        $this->reset([
            'editId', 'group', 'part_number', 'part_name', 'no_rack', 
            'last_stock', 'maker', 'machine', 'status', 'use_qty', 
            'price_idr', 'part_photo', 'existing_photo'
        ]);
        $this->status = 'Y'; // default value
    }

    public function with(SparePartService $sparePartService): array
    {
        $headers = [
            ['key' => 'id', 'label' => '#', 'class' => 'w-16'],
            ['key' => 'part_name', 'label' => 'Part Name'],
            ['key' => 'part_number', 'label' => 'Part Number'],
            ['key' => 'maker', 'label' => 'Maker'],
            ['key' => 'no_rack', 'label' => 'Rack'],
            ['key' => 'last_stock', 'label' => 'Stock'],
            ['key' => 'actions', 'label' => 'Actions', 'class' => 'w-24 text-center']
        ];

        return [
            'spareParts' => $sparePartService->getPaginatedSpareParts(15, $this->search),
            'headers' => $headers,
        ];
    }
};
?>

<div>
    <x-header title="Master Spare Parts" subtitle="Manage master data for spare parts" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Search..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button label="Add New" icon="o-plus" class="btn-primary" wire:click="openAdd" />
        </x-slot:actions>
    </x-header>

    <x-card>
        <x-table :headers="$headers" :rows="$spareParts" with-pagination>
            @scope('cell_part_name', $part)
                <div class="flex items-center gap-3">
                    <x-avatar :image="$part->part_photo ? asset('storage/' . $part->part_photo) : ''" class="!w-10 !h-10 rounded-lg" />
                    <div>
                        <div class="font-bold">{{ $part->part_name }}</div>
                        <div class="text-sm opacity-50">{{ $part->group ?: 'No Group' }}</div>
                    </div>
                </div>
            @endscope

            @scope('cell_status', $part)
                @if($part->status === 'Y')
                    <x-badge value="Active" class="badge-success" />
                @else
                    <x-badge value="Discontinued" class="badge-error" />
                @endif
            @endscope

            @scope('cell_actions', $part)
                <div class="flex gap-2 justify-center">
                    <x-button icon="o-pencil-square" class="btn-sm btn-ghost text-info" wire:click="openEdit({{ $part->id }})" />
                    <x-button icon="o-trash" class="btn-sm btn-ghost text-error" wire:click="confirmDelete({{ $part->id }})" />
                </div>
            @endscope
        </x-table>
    </x-card>

    {{-- Add/Edit Modal --}}
    <x-modal wire:model="formModal" title="{{ $editId ? 'Edit Spare Part' : 'Add New Spare Part' }}" separator>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <x-input label="Part Name *" wire:model="part_name" />
            </div>
            
            <x-input label="Part Number" wire:model="part_number" />
            <x-input label="Group" wire:model="group" />
            
            <x-input label="Maker" wire:model="maker" />
            <x-input label="Machine" wire:model="machine" />
            
            <x-input label="Rack No" wire:model="no_rack" />
            <x-input label="Initial Stock *" type="number" wire:model="last_stock" />
            
            <x-input label="Use Qty *" type="number" wire:model="use_qty" />
            <x-input label="Price (IDR) *" type="number" wire:model="price_idr" prefix="Rp" />
            
            <x-select label="Status *" :options="[['id' => 'Y', 'name' => 'Active'], ['id' => 'N', 'name' => 'Discontinued']]" wire:model="status" />
            
            <div class="md:col-span-2 mt-4">
                <x-file wire:model="part_photo" label="Photo" accept="image/*">
                    @if($existing_photo && !$part_photo)
                        <img src="{{ asset('storage/' . $existing_photo) }}" class="h-40 rounded-lg mt-2 object-cover" />
                    @endif
                </x-file>
            </div>
        </div>

        <x-slot:actions>
            <x-button label="Cancel" wire:click="$set('formModal', false)" />
            <x-button label="Save" class="btn-primary" wire:click="save" spinner="save" />
        </x-slot:actions>
    </x-modal>

    {{-- Delete Modal --}}
    <x-modal wire:model="deleteModal" title="Confirm Deletion">
        <div>Are you sure you want to delete this spare part? This action cannot be undone.</div>
        <x-slot:actions>
            <x-button label="Cancel" wire:click="$set('deleteModal', false)" />
            <x-button label="Delete" class="btn-error text-white" wire:click="delete" spinner="delete" />
        </x-slot:actions>
    </x-modal>
</div>
