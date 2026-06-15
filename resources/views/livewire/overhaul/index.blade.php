<?php

use App\Services\OverhaulService;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;

new class extends Component {
    use WithPagination, WithFileUploads, Toast;

    public string $search = '';
    public string $statusFilter = '';

    // Modals
    public bool   $addModal             = false;
    public bool   $editModal            = false;

    // Form Fields
    public ?int   $formId               = null;
    public string $date_request         = '';
    public string $date_plan            = '';
    public string $date_start           = '';
    public string $date_finish          = '';
    public string $LineName             = '';
    public string $MachineNo            = '';
    public string $MachineName          = '';
    public string $description          = '';
    public string $pic                  = '';
    public string $status               = 'Open';
    public string $result               = '';
    public $photo_before                = null;
    public $photo_after                 = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }
    
    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function with(OverhaulService $service): array
    {
        return [
            'records' => $service->getPaginated(15, $this->search, $this->statusFilter),
        ];
    }

    public function openAdd(): void
    {
        $this->reset(['formId','date_request','date_plan','date_start','date_finish','LineName','MachineNo','MachineName','description','pic','result','photo_before','photo_after']);
        $this->status = 'Open';
        $this->date_request = date('Y-m-d');
        $this->addModal = true;
    }

    public function saveAdd(OverhaulService $service): void
    {
        $this->validate([
            'date_request' => 'required|date',
            'LineName'     => 'required|string',
            'MachineName'  => 'required|string',
            'photo_before' => 'nullable|image|max:4096',
            'photo_after'  => 'nullable|image|max:4096',
        ]);

        $service->create([
            'date_request' => $this->date_request,
            'date_plan'    => $this->date_plan,
            'date_start'   => $this->date_start,
            'date_finish'  => $this->date_finish,
            'LineName'     => $this->LineName,
            'MachineNo'    => $this->MachineNo,
            'MachineName'  => $this->MachineName,
            'description'  => $this->description,
            'pic'          => $this->pic,
            'status'       => $this->status,
            'result'       => $this->result,
        ], $this->photo_before, $this->photo_after);

        $this->addModal = false;
        $this->success('Overhaul Request created.');
    }

    public function openEdit(int $id, OverhaulService $service): void
    {
        $record = $service->getPaginated()->getCollection()->firstWhere('id', $id);
        if(!$record) {
            $this->error('Record not found.');
            return;
        }

        $this->formId       = $id;
        $this->date_request = $record->date_request ? $record->date_request->format('Y-m-d') : '';
        $this->date_plan    = $record->date_plan ? $record->date_plan->format('Y-m-d') : '';
        $this->date_start   = $record->date_start ? $record->date_start->format('Y-m-d') : '';
        $this->date_finish  = $record->date_finish ? $record->date_finish->format('Y-m-d') : '';
        $this->LineName     = $record->LineName ?? '';
        $this->MachineNo    = $record->MachineNo ?? '';
        $this->MachineName  = $record->MachineName ?? '';
        $this->description  = $record->description ?? '';
        $this->pic          = $record->pic ?? '';
        $this->status       = $record->status ?? 'Open';
        $this->result       = $record->result ?? '';
        
        $this->photo_before = null;
        $this->photo_after  = null;
        
        $this->editModal    = true;
    }

    public function saveEdit(OverhaulService $service): void
    {
        $this->validate([
            'date_request' => 'required|date',
            'LineName'     => 'required|string',
            'MachineName'  => 'required|string',
            'photo_before' => 'nullable|image|max:4096',
            'photo_after'  => 'nullable|image|max:4096',
        ]);

        $service->update($this->formId, [
            'date_request' => $this->date_request,
            'date_plan'    => $this->date_plan,
            'date_start'   => $this->date_start,
            'date_finish'  => $this->date_finish,
            'LineName'     => $this->LineName,
            'MachineNo'    => $this->MachineNo,
            'MachineName'  => $this->MachineName,
            'description'  => $this->description,
            'pic'          => $this->pic,
            'status'       => $this->status,
            'result'       => $this->result,
        ], $this->photo_before, $this->photo_after);

        $this->editModal = false;
        $this->success('Overhaul Updated.');
    }

    public function deleteRecord(int $id, OverhaulService $service): void
    {
        $service->delete($id);
        $this->success('Overhaul deleted.');
    }
};
?>

<div>
    <x-header title="Machine Overhaul" separator progress-indicator>
        <x-slot:middle class="!justify-end gap-2">
            <x-select 
                wire:model.live="statusFilter" 
                :options="[['id'=>'','name'=>'All Status'],['id'=>'Open','name'=>'Open'],['id'=>'Planned','name'=>'Planned'],['id'=>'In Progress','name'=>'In Progress'],['id'=>'Done','name'=>'Done']]" 
                option-value="id" option-label="name" 
            />
            <x-input placeholder="Search description, machine..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button label="Add Overhaul Request" icon="o-plus" class="btn-primary" wire:click="openAdd" />
        </x-slot:actions>
    </x-header>

    <x-card>
        <x-table
            :headers="[
                ['key' => 'date_request', 'label' => 'Req Date'],
                ['key' => 'LineName',     'label' => 'Line'],
                ['key' => 'MachineName',  'label' => 'Machine'],
                ['key' => 'description',  'label' => 'Description'],
                ['key' => 'status',       'label' => 'Status'],
                ['key' => 'pic',          'label' => 'PIC'],
            ]"
            :rows="$records"
            with-pagination
        >
            @scope('cell_date_request', $r)
                {{ $r->date_request ? $r->date_request->format('Y-m-d') : '-' }}
            @endscope

            @scope('cell_status', $r)
                <x-badge label="{{ $r->status }}" 
                    class="{{ 
                        match(strtolower($r->status)) {
                            'open' => 'badge-error',
                            'planned' => 'badge-info',
                            'in progress' => 'badge-warning',
                            'done' => 'badge-success',
                            default => 'badge-ghost'
                        }
                    }}" 
                />
            @endscope

            @scope('actions', $r)
                <div class="flex gap-1">
                    <x-button icon="o-pencil-square" class="btn-ghost btn-xs" wire:click="openEdit({{ $r->id }})" />
                    <x-button icon="o-trash" class="btn-ghost btn-xs text-error" 
                        wire:click="deleteRecord({{ $r->id }})" 
                        wire:confirm="Delete this record? This cannot be undone." />
                </div>
            @endscope
        </x-table>
    </x-card>

    {{-- Add Modal --}}
    <x-modal wire:model="addModal" title="New Overhaul Request" separator>
        <div class="grid grid-cols-2 gap-3">
            <x-input label="Req Date" type="date" wire:model="date_request" />
            <x-input label="Plan Date" type="date" wire:model="date_plan" />
            <x-input label="Line Name" wire:model="LineName" />
            <x-input label="Machine Name" wire:model="MachineName" />
            <x-input label="Machine No" wire:model="MachineNo" class="col-span-2" />
            <x-textarea label="Description" wire:model="description" class="col-span-2" rows="2" />
            <x-input label="PIC" wire:model="pic" />
            <x-select label="Status" wire:model="status"
                :options="[['id'=>'Open','name'=>'Open'],['id'=>'Planned','name'=>'Planned'],['id'=>'In Progress','name'=>'In Progress'],['id'=>'Done','name'=>'Done']]"
                option-value="id" option-label="name" />
        </div>
        <x-slot:actions>
            <x-button label="Cancel" class="btn-ghost" wire:click="$set('addModal',false)" />
            <x-button label="Submit" class="btn-primary" wire:click="saveAdd" spinner="saveAdd" />
        </x-slot:actions>
    </x-modal>

    {{-- Edit Modal --}}
    <x-modal wire:model="editModal" title="Edit Overhaul Record" separator>
        <div class="grid grid-cols-2 gap-3">
            <x-input label="Req Date" type="date" wire:model="date_request" />
            <x-input label="Plan Date" type="date" wire:model="date_plan" />
            <x-input label="Start Date" type="date" wire:model="date_start" />
            <x-input label="Finish Date" type="date" wire:model="date_finish" />
            
            <x-input label="Line Name" wire:model="LineName" />
            <x-input label="Machine Name" wire:model="MachineName" />
            <x-input label="Machine No" wire:model="MachineNo" />
            <x-input label="PIC" wire:model="pic" />
            
            <x-textarea label="Description" wire:model="description" class="col-span-2" rows="2" />
            <x-textarea label="Result" wire:model="result" class="col-span-2" rows="2" />
            
            <x-select label="Status" wire:model="status" class="col-span-2"
                :options="[['id'=>'Open','name'=>'Open'],['id'=>'Planned','name'=>'Planned'],['id'=>'In Progress','name'=>'In Progress'],['id'=>'Done','name'=>'Done']]"
                option-value="id" option-label="name" />
                
            <div class="col-span-2 grid grid-cols-2 gap-3">
                <div>
                    <label class="label text-sm font-semibold">Photo Before</label>
                    <input type="file" wire:model="photo_before" class="file-input file-input-bordered file-input-sm w-full" accept="image/*" />
                </div>
                <div>
                    <label class="label text-sm font-semibold">Photo After</label>
                    <input type="file" wire:model="photo_after" class="file-input file-input-bordered file-input-sm w-full" accept="image/*" />
                </div>
            </div>
        </div>
        <x-slot:actions>
            <x-button label="Cancel" class="btn-ghost" wire:click="$set('editModal',false)" />
            <x-button label="Save Changes" class="btn-primary" wire:click="saveEdit" spinner="saveEdit" />
        </x-slot:actions>
    </x-modal>
</div>
