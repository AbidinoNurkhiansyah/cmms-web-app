<?php

use App\Services\CartyService;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

new class extends Component {
    use WithPagination, Toast;

    public string $search = '';
    public string $statusFilter = '';

    // Modals
    public bool   $addModal             = false;
    public bool   $editModal            = false;

    // Form Fields
    public ?int   $formId               = null;
    public string $Date                 = '';
    public string $groupline            = '';
    public string $LineName             = '';
    public string $MachineNo            = '';
    public string $MachineName          = '';
    public string $DownTime             = '0';
    public string $Problem              = '';
    public string $Action               = '';
    public string $Status               = 'Open';
    public string $Shift                = '1';
    public string $PIC                  = '';
    public string $pic_repair           = '';
    public string $start_time           = '';
    public string $finish_time          = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }
    
    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function with(CartyService $service): array
    {
        return [
            'records' => $service->getPaginated(15, $this->search, $this->statusFilter),
        ];
    }

    public function openAdd(): void
    {
        $this->reset(['formId','Date','groupline','LineName','MachineNo','MachineName','DownTime','Problem','Action','PIC','pic_repair','start_time','finish_time']);
        $this->Date = date('Y-m-d');
        $this->Status = 'Open';
        $this->Shift = '1';
        $this->DownTime = '0';
        $this->addModal = true;
    }

    public function saveAdd(CartyService $service): void
    {
        $this->validate([
            'Date'        => 'required|date',
            'LineName'    => 'required|string',
            'MachineName' => 'required|string',
        ]);

        $service->create([
            'Date'        => $this->Date,
            'groupline'   => $this->groupline,
            'LineName'    => $this->LineName,
            'MachineNo'   => $this->MachineNo,
            'MachineName' => $this->MachineName,
            'DownTime'    => (int)$this->DownTime,
            'Problem'     => $this->Problem,
            'Action'      => $this->Action,
            'Status'      => $this->Status,
            'Shift'       => (int)$this->Shift,
            'PIC'         => $this->PIC,
            'pic_repair'  => $this->pic_repair,
            'start_time'  => $this->start_time,
            'finish_time' => $this->finish_time,
        ]);

        $this->addModal = false;
        $this->success('Carty Record Created.');
    }

    public function openEdit(int $id, CartyService $service): void
    {
        // For Carty, the paginated method returns items wrapped differently if it uses Repository.
        // Let's fetch it via getById.
        $record = $service->getById($id);
        if(!$record) {
            $this->error('Record not found.');
            return;
        }

        $this->formId       = $id;
        $this->Date         = $record->Date ? $record->Date->format('Y-m-d') : '';
        $this->groupline    = $record->groupline ?? '';
        $this->LineName     = $record->LineName ?? '';
        $this->MachineNo    = $record->MachineNo ?? '';
        $this->MachineName  = $record->MachineName ?? '';
        $this->DownTime     = (string)($record->DownTime ?? 0);
        $this->Problem      = $record->Problem ?? '';
        $this->Action       = $record->Action ?? '';
        $this->Status       = $record->Status ?? 'Open';
        $this->Shift        = (string)($record->Shift ?? 1);
        $this->PIC          = $record->PIC ?? '';
        $this->pic_repair   = $record->pic_repair ?? '';
        $this->start_time   = $record->start_time ?? '';
        $this->finish_time  = $record->finish_time ?? '';
        
        $this->editModal    = true;
    }

    public function saveEdit(CartyService $service): void
    {
        $this->validate([
            'Date'        => 'required|date',
            'LineName'    => 'required|string',
            'MachineName' => 'required|string',
        ]);

        $service->update($this->formId, [
            'Date'        => $this->Date,
            'groupline'   => $this->groupline,
            'LineName'    => $this->LineName,
            'MachineNo'   => $this->MachineNo,
            'MachineName' => $this->MachineName,
            'DownTime'    => (int)$this->DownTime,
            'Problem'     => $this->Problem,
            'Action'      => $this->Action,
            'Status'      => $this->Status,
            'Shift'       => (int)$this->Shift,
            'PIC'         => $this->PIC,
            'pic_repair'  => $this->pic_repair,
            'start_time'  => $this->start_time,
            'finish_time' => $this->finish_time,
        ]);

        $this->editModal = false;
        $this->success('Carty Record Updated.');
    }

    public function deleteRecord(int $id, CartyService $service): void
    {
        $service->delete($id);
        $this->success('Carty Record deleted.');
    }
};
?>

<div>
    <x-header title="Carty / Maintenance" separator progress-indicator>
        <x-slot:middle class="!justify-end gap-2">
            <x-select 
                wire:model.live="statusFilter" 
                :options="[['id'=>'','name'=>'All Status'],['id'=>'Open','name'=>'Open'],['id'=>'Close','name'=>'Close']]" 
                option-value="id" option-label="name" 
            />
            <x-input placeholder="Search problem, line..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button label="Add Carty" icon="o-plus" class="btn-primary" wire:click="openAdd" />
        </x-slot:actions>
    </x-header>

    <x-card>
        <x-table
            :headers="[
                ['key' => 'Date',         'label' => 'Date'],
                ['key' => 'LineName',     'label' => 'Line'],
                ['key' => 'MachineName',  'label' => 'Machine'],
                ['key' => 'Problem',      'label' => 'Problem'],
                ['key' => 'Status',       'label' => 'Status'],
                ['key' => 'DownTime',     'label' => 'DownTime (m)'],
                ['key' => 'PIC',          'label' => 'PIC'],
            ]"
            :rows="$records"
            with-pagination
        >
            @scope('cell_Date', $r)
                {{ $r->Date ? $r->Date->format('Y-m-d') : '-' }}
            @endscope

            @scope('cell_Status', $r)
                <x-badge label="{{ $r->Status }}" 
                    class="{{ 
                        match(strtolower($r->Status)) {
                            'open' => 'badge-error',
                            'close' => 'badge-success',
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
    <x-modal wire:model="addModal" title="New Carty Record" separator>
        <div class="grid grid-cols-2 gap-3">
            <x-input label="Date" type="date" wire:model="Date" />
            <x-select label="Shift" wire:model="Shift"
                :options="[['id'=>'1','name'=>'1'],['id'=>'2','name'=>'2'],['id'=>'3','name'=>'3']]"
                option-value="id" option-label="name" />
            <x-input label="Group Line" wire:model="groupline" />
            <x-input label="Line Name" wire:model="LineName" />
            <x-input label="Machine Name" wire:model="MachineName" />
            <x-input label="Machine No" wire:model="MachineNo" />
            <x-input label="Start Time" type="time" wire:model="start_time" />
            <x-input label="Finish Time" type="time" wire:model="finish_time" />
            <x-input label="Down Time (mins)" type="number" wire:model="DownTime" />
            <x-select label="Status" wire:model="Status"
                :options="[['id'=>'Open','name'=>'Open'],['id'=>'Close','name'=>'Close']]"
                option-value="id" option-label="name" />
            <x-input label="PIC" wire:model="PIC" />
            <x-input label="PIC Repair" wire:model="pic_repair" />
            <x-textarea label="Problem" wire:model="Problem" class="col-span-2" rows="2" />
            <x-textarea label="Action" wire:model="Action" class="col-span-2" rows="2" />
        </div>
        <x-slot:actions>
            <x-button label="Cancel" class="btn-ghost" wire:click="$set('addModal',false)" />
            <x-button label="Submit" class="btn-primary" wire:click="saveAdd" spinner="saveAdd" />
        </x-slot:actions>
    </x-modal>

    {{-- Edit Modal --}}
    <x-modal wire:model="editModal" title="Edit Carty Record" separator>
        <div class="grid grid-cols-2 gap-3">
            <x-input label="Date" type="date" wire:model="Date" />
            <x-select label="Shift" wire:model="Shift"
                :options="[['id'=>'1','name'=>'1'],['id'=>'2','name'=>'2'],['id'=>'3','name'=>'3']]"
                option-value="id" option-label="name" />
            <x-input label="Group Line" wire:model="groupline" />
            <x-input label="Line Name" wire:model="LineName" />
            <x-input label="Machine Name" wire:model="MachineName" />
            <x-input label="Machine No" wire:model="MachineNo" />
            <x-input label="Start Time" type="time" wire:model="start_time" />
            <x-input label="Finish Time" type="time" wire:model="finish_time" />
            <x-input label="Down Time (mins)" type="number" wire:model="DownTime" />
            <x-select label="Status" wire:model="Status"
                :options="[['id'=>'Open','name'=>'Open'],['id'=>'Close','name'=>'Close']]"
                option-value="id" option-label="name" />
            <x-input label="PIC" wire:model="PIC" />
            <x-input label="PIC Repair" wire:model="pic_repair" />
            <x-textarea label="Problem" wire:model="Problem" class="col-span-2" rows="2" />
            <x-textarea label="Action" wire:model="Action" class="col-span-2" rows="2" />
        </div>
        <x-slot:actions>
            <x-button label="Cancel" class="btn-ghost" wire:click="$set('editModal',false)" />
            <x-button label="Save Changes" class="btn-primary" wire:click="saveEdit" spinner="saveEdit" />
        </x-slot:actions>
    </x-modal>
</div>
