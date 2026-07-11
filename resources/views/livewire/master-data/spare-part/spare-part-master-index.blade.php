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
            'editId',
            'group',
            'part_number',
            'part_name',
            'no_rack',
            'last_stock',
            'maker',
            'machine',
            'status',
            'use_qty',
            'price_idr',
            'part_photo',
            'existing_photo'
        ]);
        $this->status = 'Y'; // default value
    }

    public function with(SparePartService $sparePartService): array
    {
        $headers = [
            ['key' => 'id', 'label' => 'No', 'class' => 'w-16'],
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
    {{-- Header --}}
    @include('livewire.master-data.spare-part.partials.header')

    {{-- Table --}}
    @include('livewire.master-data.spare-part.partials.table')

    {{-- Add/Edit Modal --}}
    @include('livewire.master-data.spare-part.partials.form-modal')

    {{-- Delete Modal --}}
    @include('livewire.master-data.spare-part.partials.delete-modal')
</div>