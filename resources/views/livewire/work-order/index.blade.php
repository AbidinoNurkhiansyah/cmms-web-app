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

    public bool $exportModal = false;
    public string $export_start_date = '';
    public string $export_end_date = '';
    public string $export_team = '';
    public string $export_order_type = '';
    public string $export_status = '';

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

    public function deleteWO(int $id, WorkOrderService $woService): void
    {
        $woService->delete($id);
        $this->success('Work Order deleted.');
    }

    public function openExport(): void
    {
        $this->export_start_date = '';
        $this->export_end_date = '';
        $this->export_team = '';
        $this->export_order_type = '';
        $this->export_status = '';
        $this->exportModal = true;
    }

    public function downloadExport()
    {
        $filters = [
            'start_date' => $this->export_start_date,
            'end_date' => $this->export_end_date,
            'team' => $this->export_team,
            'order_type' => $this->export_order_type,
            'status' => $this->export_status,
        ];
        
        $this->exportModal = false;
        
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\WorkOrderExport($filters), 
            'Work_Orders_' . date('Y-m-d_His') . '.xlsx'
        );
    }

    public function getTeamOptionsProperty(): array
    {
        $legacyTeams = ['TPM-OH-SM', 'MAINTENANCE', 'TPM-OH', 'MAINTENANCE A', 'MAINTENANCE B', 'TPM', 'OH', 'ADMIN', 'SUB MATERIAL', 'Repair'];
        $dbTeams = \App\Models\User::select('team')->distinct()->pluck('team')->filter()->toArray();
        return collect(array_unique(array_merge($legacyTeams, $dbTeams)))
            ->sort()
            ->map(fn($v) => ['id' => $v, 'name' => $v])
            ->values()
            ->toArray();
    }
};
?>

<div>
    @include('livewire.work-order.partials.stats')

    @include('livewire.work-order.partials.table')

    @include('livewire.work-order.partials.add-modal')

    @include('livewire.work-order.partials.export-modal')
</div>

