<?php

namespace App\Livewire\SparePart\Repair\Traits;

use App\Models\SparePartRepair;
use App\Services\SparePartRepairService;
use Livewire\Attributes\Computed;

trait WithRepairModals
{
    public bool $isModalOpen = false;
    public bool $isEditMode = false;
    public ?int $editingId = null;

    // Form fields
    public $date;
    public $spare_part_id;
    public $qty = 0;
    public $item_repair;
    public $rack;
    public $pic1_id;
    public $pic2_id;
    public $pic3_id;
    public $part_usage;
    public $review;
    
    // File uploads
    public $file_before_upload;
    public $file_after_upload;
    public $existing_file_before;
    public $existing_file_after;

    #[Computed]
    public function rackOptions()
    {
        return collect(SparePartRepair::whereNotNull('rack')
            ->where('rack', '!=', '')
            ->distinct()
            ->pluck('rack')
            ->map(fn($r) => ['id' => $r, 'name' => $r])
            ->values());
    }

    #[Computed]
    public function partUsageOptions()
    {
        $query = SparePartRepair::whereNotNull('part_usage')->where('part_usage', '!=', '')->distinct();
        
        if ($this->rack) {
            $query->where('rack', $this->rack);
        }
        
        return collect($query->pluck('part_usage')
            ->map(fn($p) => ['id' => $p, 'name' => $p])
            ->values());
    }

    public function mountWithRepairModals()
    {
        $this->date = date('Y-m-d');
    }

    public function rules()
    {
        return [
            'date' => 'required|date',
            'spare_part_id' => 'required|exists:spare_parts,id',
            'qty' => 'required|integer|min:1',
            'item_repair' => 'required|string|max:255',
            'rack' => 'nullable|string|max:255',
            'pic1_id' => 'nullable|exists:users,id',
            'pic2_id' => 'nullable|exists:users,id',
            'pic3_id' => 'nullable|exists:users,id',
            'part_usage' => 'nullable|string|max:255',
            'review' => 'nullable|string|max:255',
            'file_before_upload' => 'nullable|image|max:2048', // max 2MB
            'file_after_upload' => 'nullable|image|max:2048',
        ];
    }

    public function createRepair()
    {
        $this->resetValidation();
        $this->reset([
            'spare_part_id', 'qty', 'item_repair', 'rack',
            'pic1_id', 'pic2_id', 'pic3_id', 'part_usage', 'review',
            'file_before_upload', 'file_after_upload',
            'existing_file_before', 'existing_file_after'
        ]);
        $this->date = date('Y-m-d');
        $this->isEditMode = false;
        $this->editingId = null;
        $this->isModalOpen = true;
    }

    public function editRepair(int $id)
    {
        $this->resetValidation();
        $repair = SparePartRepair::findOrFail($id);
        
        $this->editingId = $repair->id;
        $this->date = $repair->date ? $repair->date->format('Y-m-d') : null;
        $this->spare_part_id = $repair->spare_part_id;
        $this->qty = $repair->qty;
        $this->item_repair = $repair->item_repair;
        $this->rack = $repair->rack;
        $this->pic1_id = $repair->pic1_id;
        $this->pic2_id = $repair->pic2_id;
        $this->pic3_id = $repair->pic3_id;
        $this->part_usage = $repair->part_usage;
        $this->review = $repair->review;
        
        $this->existing_file_before = $repair->file_before;
        $this->existing_file_after = $repair->file_after;
        $this->file_before_upload = null;
        $this->file_after_upload = null;

        $this->isEditMode = true;
        $this->isModalOpen = true;
    }

    public function saveRepair()
    {
        $validatedData = $this->validate();

        $service = app(SparePartRepairService::class);

        if ($this->isEditMode) {
            $service->update($this->editingId, $validatedData);
            $this->success('Data repair berhasil diubah.');
        } else {
            $service->store($validatedData);
            $this->success('Data repair berhasil ditambahkan.');
        }

        $this->isModalOpen = false;
        // Trigger table refresh via event or direct method call is handled automatically since modal closes
    }

    public function deleteRepair(int $id)
    {
        app(SparePartRepairService::class)->delete($id);
        $this->success('Data repair berhasil dihapus.');
    }
}
