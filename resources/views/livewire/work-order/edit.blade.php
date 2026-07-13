<?php

use Livewire\Volt\Component;
use App\Services\WorkOrderService;
use App\Livewire\Forms\WorkOrderEditForm;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;
use App\Livewire\Traits\WithAssetSelection;
use App\Livewire\Traits\WithPersonnel;
use App\Livewire\Traits\WithSpareparts;

new class extends Component {
    use WithFileUploads, Toast, WithAssetSelection, WithPersonnel, WithSpareparts;

    public int $wo_id;
    public WorkOrderEditForm $editForm;

    // Tabs
    public string $editTab = 'general';

    // Sparepart Add Form
    public $sp_id = null;
    public $sp_qty = 1;
    public $sp_itemcheck = '';
    public $workOrderSpareparts = []; // fetched from DB

    public bool $deleteSpModal = false;
    public ?int $deleteSpId = null;
    public bool $addSpModal = false;
    public ?int $editSpId = null;

    public function mount(int $id, WorkOrderService $woService): void
    {
        $this->wo_id = $id;
        $wo = $woService->getPaginated()->getCollection()->firstWhere('id', $id);

        if (!$wo) {
            // Fallback if paginated query doesn't include it
            $wo = \App\Models\WorkOrder::find($id);
        }

        if (!$wo) {
            abort(404, 'Work Order not found.');
        }

        $this->editForm->initForm($wo);

        $this->LineName = $this->editForm->line_name;
        $this->MachineNo = $this->editForm->machine_no;
        $this->MachineName = $this->editForm->machine_name;
        $this->mountWithAssetSelection();
        
        $this->personnelTeamFilter = $this->editForm->pic;
        $this->mountWithPersonnel();

        $this->mountWithSpareparts();
        $this->loadSpareparts();
    }

    public function loadSpareparts(): void
    {
        $this->workOrderSpareparts = \App\Models\WorkOrderSparepart::with('sparepart')
            ->where('work_order_id', $this->editForm->wo_id)
            ->get();
    }

    public function addSp(): void
    {
        $this->editSpId = null;
        $this->sp_id = null;
        $this->sp_qty = 1;
        $this->sp_itemcheck = '';
        $this->addSpModal = true;
    }

    public function saveSp(): void
    {
        $this->validate([
            'sp_id' => 'required',
            'sp_qty' => 'required|numeric|min:1',
        ]);

        if ($this->editSpId) {
            $sp = \App\Models\WorkOrderSparepart::find($this->editSpId);
            if ($sp) {
                $sp->update([
                    'sparepart_id' => $this->sp_id,
                    'qty' => $this->sp_qty,
                    'remarks' => $this->sp_itemcheck,
                ]);
            }
            $message = 'Sparepart updated successfully';
        } else {
            \App\Models\WorkOrderSparepart::create([
                'work_order_id' => $this->editForm->wo_id,
                'sparepart_id' => $this->sp_id,
                'qty' => $this->sp_qty,
                'remarks' => $this->sp_itemcheck,
            ]);
            $message = 'Sparepart added successfully';
        }

        $this->editSpId = null;
        $this->sp_id = null;
        $this->sp_qty = 1;
        $this->sp_itemcheck = '';

        $this->loadSpareparts();
        $this->success($message);
        $this->addSpModal = false;
    }

    public function editSp(int $id): void
    {
        $sp = \App\Models\WorkOrderSparepart::find($id);
        if ($sp) {
            $this->editSpId = $sp->id;
            $this->sp_id = $sp->sparepart_id;
            $this->sp_qty = $sp->qty;
            $this->sp_itemcheck = $sp->remarks;
            $this->addSpModal = true;
        }
    }

    public function deleteSp(int $id): void
    {
        $this->deleteSpId = $id;
        $this->deleteSpModal = true;
    }

    public function confirmDeleteSp(): void
    {
        if ($this->deleteSpId) {
            \App\Models\WorkOrderSparepart::find($this->deleteSpId)?->delete();
            $this->loadSpareparts();
            $this->success('Sparepart removed');
            $this->deleteSpModal = false;
            $this->deleteSpId = null;
        }
    }

    public function saveEdit(WorkOrderService $woService): void
    {
        $this->editForm->line_name = $this->LineName;
        $this->editForm->machine_no = $this->MachineNo;
        $this->editForm->machine_name = $this->MachineName;

        $this->editForm->update($woService);
        $this->success('Work Order updated successfully.');
    }

    public function getDepartmentsProperty(): array
    {
        return collect(['Prod 1', 'Prod 2', 'Prod 3', 'Prod 4', 'Prod 5', 'Jishuken', 'IT', 'HR', 'GA', 'EHS', 'PPIC', 'PE', 'ME', 'QC'])
            ->map(fn($v) => ['id' => $v, 'name' => $v])
            ->toArray();
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

    public function updated($property, $value)
    {
        if ($property === 'editForm.pic') {
            $this->personnelTeamFilter = $value;
            $this->searchUser(); // refresh user list
            
            // Optional: reset selected pics when team changes so they don't have invalid users
            $this->editForm->pic1 = '';
            $this->editForm->pic2 = '';
            $this->editForm->pic3 = '';
        }
    }
};
?>

<div>
    <x-header subtitle="Edit & Confirmation" separator>
        <x-slot:title>
            <div class="flex items-center gap-3">
                <x-button icon="o-arrow-left" class="btn-circle btn-ghost btn-sm" link="{{ route('work-orders.index') }}" wire:navigate />
                <span>Edit / Process Work Order</span>
            </div>
        </x-slot:title>
    </x-header>

    <div class="tabs tabs-boxed mb-4 w-fit bg-base-200">
        <a class="tab {{ $editTab === 'general' ? 'tab-active font-bold' : '' }}"
            wire:click="$set('editTab', 'general')">General Info</a>
        <a class="tab {{ $editTab === 'spareparts' ? 'tab-active font-bold' : '' }}"
            wire:click="$set('editTab', 'spareparts')">Spareparts Used</a>
    </div>

    <div>
        @if($editTab === 'general')
            @include('livewire.work-order.partials.general')
        @endif

        @if($editTab === 'spareparts')
            @include('livewire.work-order.partials.spareparts')
        @endif
    </div>


    {{-- Delete Confirmation Modal --}}
    <x-modal wire:model="deleteSpModal" title="Confirm Deletion" subtitle="Are you sure you want to remove this sparepart from the work order?">
        <x-slot:actions>
            <x-button label="Cancel" @click="$wire.deleteSpModal = false" />
            <x-button label="Remove" class="btn-error text-white" wire:click="confirmDeleteSp" spinner="confirmDeleteSp" />
        </x-slot:actions>
    </x-modal>
</div>