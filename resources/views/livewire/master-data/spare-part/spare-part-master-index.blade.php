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
    public ?string $rank = null;
    public int $last_stock = 0;
    public string $maker = '';
    public array $machine = [];
    public string $status = 'Y';
    public int $use_qty = 0;
    public int $price_idr = 0;
    public $part_photo; // For new uploads
    public ?string $existing_photo = null;
    
    // Dropdown Data
    public array $assets = [];

    // For Delete
    public ?int $deleteId = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function openAdd(): void
    {
        $this->resetForm();
        $this->searchMachines(''); // Initial load
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
            
            // Prepend MAC- to prevent JS/Livewire numeric casting issues
            $machinePivot = \App\Models\MachineSparePart::where('spare_part_id', $part->id)->pluck('asset_no')->toArray();
            $this->machine = array_map(fn($m) => 'MAC-' . $m, $machinePivot);
            
            $this->status = $part->status ?? 'Y';
            $this->rank = $part->rank;
            if (in_array(strtoupper($this->status), ['ACTIVE', 'AKTIF', '1'])) $this->status = 'Y';
            if (in_array(strtoupper($this->status), ['DISCONTINUED', 'INACTIVE', 'TIDAK AKTIF', '0'])) $this->status = 'N';
            
            $this->use_qty = $part->use_qty ?? 0;
            $this->price_idr = $part->price_idr ?? 0;
            $this->existing_photo = $part->part_photo;

            // Load selected machines + search pool
            $this->searchMachines('');
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
            'rank' => 'nullable|in:A,B,C,D',
            'no_rack' => 'nullable|string|max:50',
            'maker' => 'nullable|string|max:100',
            'machine' => 'nullable|array',
            'machine.*' => 'string',
            'last_stock' => 'required|integer|min:0',
            'use_qty' => 'required|integer|min:0',
            'price_idr' => 'required|numeric|min:0',
            'status' => 'required|in:Y,N',
            'part_photo' => 'nullable|image|max:2048', // max 2MB
        ]);

        // Strip MAC- prefix before saving
        $cleanMachines = array_map(function($m) {
            return str_replace('MAC-', '', $m);
        }, $this->machine ?? []);
        
        $data = [
            'part_name' => $this->part_name,
            'part_number' => $this->part_number,
            'group' => $this->group,
            'rank' => $this->rank,
            'no_rack' => $this->no_rack,
            'maker' => $this->maker,
            'machine' => $cleanMachines,
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
            'rank',
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

    public function searchMachines(string $value = ''): void
    {
        $query = \App\Models\Asset::query();
        
        if (!empty($value)) {
            $query->where(function($q) use ($value) {
                $q->where('machine_name', 'like', "%{$value}%")
                  ->orWhere('asset_no', 'like', "%{$value}%");
            });
        }
        
        $results = $query->orderBy('machine_name')->take(50)->get();
        
        if (!empty($this->machine) && is_array($this->machine)) {
            $cleanMachines = array_map(fn($m) => str_replace('MAC-', '', $m), $this->machine);
            $selected = \App\Models\Asset::whereIn('asset_no', $cleanMachines)->get();
            $results = $results->merge($selected)->unique('asset_no');
        }
        
        $this->assets = $results->map(function($asset) {
            return [
                'id' => 'MAC-' . $asset->asset_no,
                'name' => $asset->machine_name
            ];
        })->toArray();
    }

    public function with(SparePartService $sparePartService): array
    {
        $headers = [
            ['key' => 'id', 'label' => 'No', 'class' => 'w-16'],
            ['key' => 'part_name', 'label' => 'Part Name'],
            ['key' => 'part_number', 'label' => 'Part Number'],
            ['key' => 'maker', 'label' => 'Maker'],
            ['key' => 'rank', 'label' => 'Rank', 'class' => 'text-center'],
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