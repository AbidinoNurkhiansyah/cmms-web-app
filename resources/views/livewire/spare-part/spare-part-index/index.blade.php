<?php

use App\Services\SparePartService;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public string $search = '';

    public bool $machineModal = false;
    public $machinePartDetails = [];

    // Print Modal
    public bool $printModal = false;
    public ?int $printId = null;

    public function openPrintModal(int $partId): void
    {
        $this->printId = $partId;
        $this->printModal = true;
    }

    // Edit Repair Modal
    public bool $editModal = false;
    public ?int $editId = null;
    public int $editQty = 0;
    public string $editRack = '';

    public function openMachineModal(int $partId): void
    {
        $this->machinePartDetails = \App\Models\MachineSparePart::where('spare_part_id', $partId)->get();
        $this->machineModal = true;
    }

    public function openEditRepair(int $id, int $qty, string $rack): void
    {
        $this->editId = $id;
        $this->editQty = $qty;
        $this->editRack = $rack;
        $this->editModal = true;
    }

    public function saveEditRepair(SparePartService $sparePartService): void
    {
        $this->validate([
            'editQty' => 'required|integer|min:0',
            'editRack' => 'nullable|string|max:50',
        ]);

        $sparePartService->updateSparePart($this->editId, [
            'repair_stock' => $this->editQty,
            'repair_rack' => $this->editRack,
        ]);

        $this->editModal = false;
        // Optionally add a toast success message if you use Mary\Traits\Toast
        // $this->success('Repair data updated.');
    }

    public function with(SparePartService $sparePartService): array
    {
        return [
            'spareParts' => $sparePartService->getPaginatedSpareParts(12, $this->search),
        ];
    }
};
?>

<div>
    @include('livewire.spare-part.spare-part-index.partials.header')
    @include('livewire.spare-part.spare-part-index.partials.grid')
    @include('livewire.spare-part.spare-part-index.partials.modals')
</div>
