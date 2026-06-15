<?php

use App\Services\WorkOrderService;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;

new class extends Component {
    use WithPagination, WithFileUploads, Toast;

    public string $search = '';
    public string $statusFilter = '';

    // Add modal
    public bool   $addModal             = false;
    public string $addDate              = '';
    public string $addLineName          = '';
    public string $addMachineNo         = '';
    public string $addMachineName       = '';
    public string $addProblem           = '';
    public string $addPic               = '';
    public string $addStatus            = 'Open';
    public string $addPriority          = 'Medium';

    // Edit modal
    public bool   $editModal            = false;
    public ?int   $editId               = null;
    public string $editDate             = '';
    public string $editLineName         = '';
    public string $editMachineNo        = '';
    public string $editMachineName      = '';
    public string $editProblem          = '';
    public string $editPic              = '';
    public string $editStatus           = '';
    public string $editPriority         = '';

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
        ];
    }

    public function openAdd(): void
    {
        $this->reset(['addDate', 'addLineName', 'addMachineNo', 'addMachineName', 'addProblem', 'addPic']);
        $this->addStatus = 'Open';
        $this->addPriority = 'Medium';
        $this->addDate = date('Y-m-d');
        $this->addModal = true;
    }

    public function saveAdd(WorkOrderService $woService): void
    {
        $this->validate([
            'addDate'         => 'required|date',
            'addMachineName'  => 'required|string|max:255',
            'addProblem'      => 'required|string',
            'addPriority'     => 'required|string',
        ]);

        $woService->create([
            'date'                => $this->addDate,
            'LineName'            => $this->addLineName,
            'MachineNo'           => $this->addMachineNo,
            'MachineName'         => $this->addMachineName,
            'problem_description' => $this->addProblem,
            'pic'                 => $this->addPic,
            'status'              => $this->addStatus,
            'priority'            => $this->addPriority,
        ]);

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

        $this->editId          = $id;
        $this->editDate        = $wo->date ? $wo->date->format('Y-m-d') : '';
        $this->editLineName    = $wo->LineName ?? '';
        $this->editMachineNo   = $wo->MachineNo ?? '';
        $this->editMachineName = $wo->MachineName ?? '';
        $this->editProblem     = $wo->problem_description ?? '';
        $this->editPic         = $wo->pic ?? '';
        $this->editStatus      = $wo->status ?? 'Open';
        $this->editPriority    = $wo->priority ?? 'Medium';
        
        $this->editModal       = true;
    }

    public function saveEdit(WorkOrderService $woService): void
    {
        $this->validate([
            'editDate'        => 'required|date',
            'editMachineName' => 'required|string|max:255',
            'editProblem'     => 'required|string',
            'editPriority'    => 'required|string',
            'editStatus'      => 'required|string',
        ]);

        $woService->update($this->editId, [
            'date'                => $this->editDate,
            'LineName'            => $this->editLineName,
            'MachineNo'           => $this->editMachineNo,
            'MachineName'         => $this->editMachineName,
            'problem_description' => $this->editProblem,
            'pic'                 => $this->editPic,
            'status'              => $this->editStatus,
            'priority'            => $this->editPriority,
        ]);

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
    <x-header title="Work Orders" separator progress-indicator>
        <x-slot:middle class="!justify-end gap-2">
            <x-select 
                wire:model.live="statusFilter" 
                :options="[['id'=>'','name'=>'All Status'],['id'=>'Open','name'=>'Open'],['id'=>'In Progress','name'=>'In Progress'],['id'=>'Done','name'=>'Done']]" 
                option-value="id" option-label="name" 
            />
            <x-input placeholder="Search problem, machine..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button label="Add Work Order" icon="o-plus" class="btn-primary" wire:click="openAdd" />
        </x-slot:actions>
    </x-header>

    <x-card>
        <x-table
            :headers="[
                ['key' => 'date',           'label' => 'Date'],
                ['key' => 'LineName',       'label' => 'Line'],
                ['key' => 'MachineName',    'label' => 'Machine'],
                ['key' => 'problem_description', 'label' => 'Problem'],
                ['key' => 'priority',       'label' => 'Priority'],
                ['key' => 'status',         'label' => 'Status'],
                ['key' => 'pic',            'label' => 'PIC'],
            ]"
            :rows="$workOrders"
            with-pagination
        >
            @scope('cell_date', $wo)
                {{ $wo->date ? $wo->date->format('Y-m-d') : '-' }}
            @endscope

            @scope('cell_priority', $wo)
                <x-badge label="{{ $wo->priority }}" 
                    class="{{ 
                        match(strtolower($wo->priority)) {
                            'high' => 'badge-error',
                            'medium' => 'badge-warning',
                            'low' => 'badge-info',
                            default => 'badge-ghost'
                        }
                    }}" 
                />
            @endscope
            
            @scope('cell_status', $wo)
                <x-badge label="{{ $wo->status }}" 
                    class="{{ 
                        match(strtolower($wo->status)) {
                            'open' => 'badge-error',
                            'in progress' => 'badge-warning',
                            'done' => 'badge-success',
                            default => 'badge-ghost'
                        }
                    }}" 
                />
            @endscope

            @scope('actions', $wo)
                <div class="flex gap-1">
                    <x-button icon="o-pencil-square" class="btn-ghost btn-xs" wire:click="openEdit({{ $wo->id }})" />
                    <x-button icon="o-trash" class="btn-ghost btn-xs text-error" 
                        wire:click="deleteWO({{ $wo->id }})" 
                        wire:confirm="Delete Work Order? This cannot be undone." />
                </div>
            @endscope
        </x-table>
    </x-card>

    {{-- Add Modal --}}
    <x-modal wire:model="addModal" title="New Work Order" separator>
        <div class="grid grid-cols-2 gap-3">
            <x-input label="Date" type="date" wire:model="addDate" />
            <x-input label="Line Name" wire:model="addLineName" />
            <x-input label="Machine No" wire:model="addMachineNo" />
            <x-input label="Machine Name" wire:model="addMachineName" />
            <x-select label="Priority" wire:model="addPriority"
                :options="[['id'=>'Low','name'=>'Low'],['id'=>'Medium','name'=>'Medium'],['id'=>'High','name'=>'High']]"
                option-value="id" option-label="name" />
            <x-select label="Status" wire:model="addStatus"
                :options="[['id'=>'Open','name'=>'Open'],['id'=>'In Progress','name'=>'In Progress'],['id'=>'Done','name'=>'Done']]"
                option-value="id" option-label="name" />
            <x-textarea label="Problem Description" wire:model="addProblem" class="col-span-2" rows="3" />
            <x-input label="PIC" wire:model="addPic" class="col-span-2" />
        </div>
        <x-slot:actions>
            <x-button label="Cancel" class="btn-ghost" wire:click="$set('addModal',false)" />
            <x-button label="Submit" class="btn-primary" wire:click="saveAdd" spinner="saveAdd" />
        </x-slot:actions>
    </x-modal>

    {{-- Edit Modal --}}
    <x-modal wire:model="editModal" title="Edit Work Order" separator>
        <div class="grid grid-cols-2 gap-3">
            <x-input label="Date" type="date" wire:model="editDate" />
            <x-input label="Line Name" wire:model="editLineName" />
            <x-input label="Machine No" wire:model="editMachineNo" />
            <x-input label="Machine Name" wire:model="editMachineName" />
            <x-select label="Priority" wire:model="editPriority"
                :options="[['id'=>'Low','name'=>'Low'],['id'=>'Medium','name'=>'Medium'],['id'=>'High','name'=>'High']]"
                option-value="id" option-label="name" />
            <x-select label="Status" wire:model="editStatus"
                :options="[['id'=>'Open','name'=>'Open'],['id'=>'In Progress','name'=>'In Progress'],['id'=>'Done','name'=>'Done']]"
                option-value="id" option-label="name" />
            <x-textarea label="Problem Description" wire:model="editProblem" class="col-span-2" rows="3" />
            <x-input label="PIC" wire:model="editPic" class="col-span-2" />
        </div>
        <x-slot:actions>
            <x-button label="Cancel" class="btn-ghost" wire:click="$set('editModal',false)" />
            <x-button label="Save Changes" class="btn-primary" wire:click="saveEdit" spinner="saveEdit" />
        </x-slot:actions>
    </x-modal>
</div>
