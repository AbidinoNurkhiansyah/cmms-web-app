<?php

use App\Services\WorkOrderService;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;
use App\Livewire\Forms\WorkOrderAddForm;
use App\Livewire\Forms\WorkOrderEditForm;
use App\Livewire\Traits\WithAssetSelection;

new class extends Component {
    use WithPagination, WithFileUploads, Toast, WithAssetSelection;

    public string $search = '';
    public string $statusFilter = '';

    public bool $addModal = false;
    public WorkOrderAddForm $addForm;

    public bool $editModal = false;
    public WorkOrderEditForm $editForm;

    public function mount(): void
    {
        $this->mountWithAssetSelection();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }
    
    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function with(WorkOrderService $woService): array
    {
        return [
            'workOrders' => $woService->getPaginated(15, $this->search, $this->statusFilter),
            'progressSummary' => $woService->getProgressSummary(),
        ];
    }

    public function openAdd(): void
    {
        $this->addForm->initForm();
        $this->LineName = '';
        $this->MachineNo = '';
        $this->MachineName = '';
        $this->asset_id = null;
        $this->machines = collect();
        
        $this->addModal = true;
    }

    public function saveAdd(WorkOrderService $woService): void
    {
        $this->addForm->line_name = $this->LineName;
        $this->addForm->machine_name = $this->MachineName;
        $this->addForm->machine_no = $this->MachineNo;

        $this->addForm->store($woService);
        $this->addModal = false;
        $this->success('Work Order created successfully.');
    }

    public function openEdit(int $id, WorkOrderService $woService): void
    {
        $wo = $woService->getPaginated()->getCollection()->firstWhere('id', $id);
        if(!$wo) {
            $this->error('Work Order not found.');
            return;
        }

        $this->editForm->initForm($wo);
        $this->editModal = true;
    }

    public function saveEdit(WorkOrderService $woService): void
    {
        $this->editForm->update($woService);
        $this->editModal = false;
        $this->success('Work Order updated successfully.');
    }

    public function deleteWO(int $id, WorkOrderService $woService): void
    {
        $woService->delete($id);
        $this->success('Work Order deleted.');
    }
};
?>

<div>
    @include('livewire.work-order.partials.stats')

    @include('livewire.work-order.partials.table')

    @include('livewire.work-order.partials.add-modal')
    @include('livewire.work-order.partials.edit-modal')
</div>

