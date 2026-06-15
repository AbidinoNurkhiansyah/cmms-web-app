<?php

use App\Services\AndonService;
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
    public string $date_shift           = '';
    public string $date_in              = '';
    public string $time_in              = '';
    public string $line_name            = '';
    public string $machine              = '';
    public string $shift                = '1';
    public string $status               = 'CALL';
    public string $stop_info            = '';
    public string $name_pic             = '';
    
    // Repair Fields
    public string $finish_time          = '';
    public bool   $mechanic             = false;
    public bool   $electric             = false;
    public string $cause_actual         = '';
    public string $preventive           = '';
    public string $hasil_repair         = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }
    
    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function with(AndonService $andonService): array
    {
        return [
            'records' => $andonService->getPaginated(15, $this->search, $this->statusFilter),
        ];
    }

    public function openAdd(): void
    {
        $this->reset(['formId','date_shift','date_in','time_in','line_name','machine','shift','stop_info','name_pic','finish_time','mechanic','electric','cause_actual','preventive','hasil_repair']);
        $this->status = 'CALL';
        $this->date_shift = date('Y-m-d');
        $this->date_in = date('Y-m-d');
        $this->time_in = date('H:i');
        $this->addModal = true;
    }

    public function saveAdd(AndonService $andonService): void
    {
        $this->validate([
            'date_shift'  => 'required|date',
            'date_in'     => 'required|date',
            'time_in'     => 'required',
            'line_name'   => 'required|string',
            'machine'     => 'required|string',
            'name_pic'    => 'required|string',
        ]);

        $andonService->create([
            'date_shift'   => $this->date_shift,
            'date_in'      => $this->date_in,
            'time_in'      => $this->date_in . ' ' . $this->time_in . ':00',
            'line_name'    => $this->line_name,
            'machine'      => $this->machine,
            'shift'        => $this->shift,
            'status'       => $this->status,
            'stop_info'    => $this->stop_info,
            'name_pic'     => $this->name_pic,
        ]);

        $this->addModal = false;
        $this->success('Andon call created.');
    }

    public function openEdit(int $id, AndonService $andonService): void
    {
        $record = $andonService->getPaginated()->getCollection()->firstWhere('id', $id);
        if(!$record) {
            $this->error('Record not found.');
            return;
        }

        $this->formId       = $id;
        $this->date_shift   = $record->date_shift ? $record->date_shift->format('Y-m-d') : '';
        $this->date_in      = $record->date_in ? $record->date_in->format('Y-m-d') : '';
        $this->time_in      = $record->time_in ? $record->time_in->format('H:i') : '';
        $this->line_name    = $record->line_name ?? '';
        $this->machine      = $record->machine ?? '';
        $this->shift        = $record->shift ?? '1';
        $this->status       = $record->status ?? 'CALL';
        $this->stop_info    = $record->stop_info ?? '';
        $this->name_pic     = $record->name_pic ?? '';
        
        $this->finish_time  = $record->finish_time ? $record->finish_time->format('H:i') : '';
        $this->mechanic     = $record->mechanic ?? false;
        $this->electric     = $record->electric ?? false;
        $this->cause_actual = $record->cause_actual ?? '';
        $this->preventive   = $record->preventive ?? '';
        $this->hasil_repair = $record->hasil_repair ?? '';
        
        $this->editModal    = true;
    }

    public function saveEdit(AndonService $andonService): void
    {
        $this->validate([
            'date_shift'  => 'required|date',
            'date_in'     => 'required|date',
            'time_in'     => 'required',
            'line_name'   => 'required|string',
            'machine'     => 'required|string',
        ]);

        $data = [
            'date_shift'   => $this->date_shift,
            'date_in'      => $this->date_in,
            'time_in'      => $this->date_in . ' ' . $this->time_in . ($this->time_in && strlen($this->time_in) == 5 ? ':00' : ''),
            'line_name'    => $this->line_name,
            'machine'      => $this->machine,
            'shift'        => $this->shift,
            'status'       => $this->status,
            'stop_info'    => $this->stop_info,
            'name_pic'     => $this->name_pic,
            'mechanic'     => $this->mechanic,
            'electric'     => $this->electric,
            'cause_actual' => $this->cause_actual,
            'preventive'   => $this->preventive,
            'hasil_repair' => $this->hasil_repair,
        ];

        if ($this->finish_time) {
            $data['finish_time'] = $this->date_in . ' ' . $this->finish_time . ($this->finish_time && strlen($this->finish_time) == 5 ? ':00' : '');
        }

        $andonService->update($this->formId, $data);

        $this->editModal = false;
        $this->success('Andon record updated.');
    }

    public function deleteAndon(int $id, AndonService $andonService): void
    {
        $andonService->delete($id);
        $this->success('Andon record deleted.');
    }
};
?>

<div>
    <x-header title="Andon & Call" separator progress-indicator>
        <x-slot:middle class="!justify-end gap-2">
            <x-select 
                wire:model.live="statusFilter" 
                :options="[['id'=>'','name'=>'All Status'],['id'=>'CALL','name'=>'CALL'],['id'=>'REPAIR','name'=>'REPAIR'],['id'=>'DONE','name'=>'DONE']]" 
                option-value="id" option-label="name" 
            />
            <x-input placeholder="Search line, machine, info..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button label="Add Call" icon="o-plus" class="btn-primary" wire:click="openAdd" />
        </x-slot:actions>
    </x-header>

    <x-card>
        <x-table
            :headers="[
                ['key' => 'date_in',      'label' => 'Date'],
                ['key' => 'time_in',      'label' => 'Time In'],
                ['key' => 'line_name',    'label' => 'Line'],
                ['key' => 'machine',      'label' => 'Machine'],
                ['key' => 'stop_info',    'label' => 'Stop Info'],
                ['key' => 'status',       'label' => 'Status'],
                ['key' => 'name_pic',     'label' => 'PIC'],
            ]"
            :rows="$records"
            with-pagination
        >
            @scope('cell_date_in', $r)
                {{ $r->date_in ? $r->date_in->format('Y-m-d') : '-' }}
            @endscope

            @scope('cell_time_in', $r)
                {{ $r->time_in ? $r->time_in->format('H:i') : '-' }}
            @endscope
            
            @scope('cell_status', $r)
                <x-badge label="{{ $r->status }}" 
                    class="{{ 
                        match(strtoupper($r->status)) {
                            'CALL' => 'badge-error',
                            'REPAIR' => 'badge-warning',
                            'DONE' => 'badge-success',
                            default => 'badge-ghost'
                        }
                    }}" 
                />
            @endscope

            @scope('actions', $r)
                <div class="flex gap-1">
                    <x-button icon="o-pencil-square" class="btn-ghost btn-xs" wire:click="openEdit({{ $r->id }})" />
                    <x-button icon="o-trash" class="btn-ghost btn-xs text-error" 
                        wire:click="deleteAndon({{ $r->id }})" 
                        wire:confirm="Delete this record? This cannot be undone." />
                </div>
            @endscope
        </x-table>
    </x-card>

    {{-- Add Modal --}}
    <x-modal wire:model="addModal" title="New Andon Call" separator>
        <div class="grid grid-cols-2 gap-3">
            <x-input label="Date Shift" type="date" wire:model="date_shift" />
            <x-select label="Shift" wire:model="shift"
                :options="[['id'=>'1','name'=>'Shift 1'],['id'=>'2','name'=>'Shift 2'],['id'=>'3','name'=>'Shift 3']]"
                option-value="id" option-label="name" />
            <x-input label="Date In" type="date" wire:model="date_in" />
            <x-input label="Time In" type="time" wire:model="time_in" />
            <x-input label="Line Name" wire:model="line_name" />
            <x-input label="Machine Name" wire:model="machine" />
            <x-input label="PIC Name" wire:model="name_pic" />
            <x-input label="Status" wire:model="status" readonly class="opacity-60" />
            <x-textarea label="Stop Info" wire:model="stop_info" class="col-span-2" rows="3" />
        </div>
        <x-slot:actions>
            <x-button label="Cancel" class="btn-ghost" wire:click="$set('addModal',false)" />
            <x-button label="Submit" class="btn-primary" wire:click="saveAdd" spinner="saveAdd" />
        </x-slot:actions>
    </x-modal>

    {{-- Edit Modal --}}
    <x-modal wire:model="editModal" title="Edit / Close Andon Call" separator>
        <div class="grid grid-cols-2 gap-3">
            <div class="col-span-2 text-sm font-bold border-b pb-1 mb-1">Call Details</div>
            <x-input label="Date Shift" type="date" wire:model="date_shift" />
            <x-select label="Shift" wire:model="shift"
                :options="[['id'=>'1','name'=>'Shift 1'],['id'=>'2','name'=>'Shift 2'],['id'=>'3','name'=>'Shift 3']]"
                option-value="id" option-label="name" />
            <x-input label="Date In" type="date" wire:model="date_in" />
            <x-input label="Time In" type="time" wire:model="time_in" />
            <x-input label="Line Name" wire:model="line_name" />
            <x-input label="Machine Name" wire:model="machine" />
            <x-input label="PIC Name" wire:model="name_pic" />
            <x-select label="Status" wire:model="status"
                :options="[['id'=>'CALL','name'=>'CALL'],['id'=>'REPAIR','name'=>'REPAIR'],['id'=>'DONE','name'=>'DONE']]"
                option-value="id" option-label="name" />
            <x-textarea label="Stop Info" wire:model="stop_info" class="col-span-2" rows="2" />
            
            <div class="col-span-2 text-sm font-bold border-b pb-1 mb-1 mt-2">Repair Actions</div>
            <x-input label="Finish Time" type="time" wire:model="finish_time" />
            <div class="flex items-center gap-4 mt-6">
                <x-checkbox label="Mechanic" wire:model="mechanic" />
                <x-checkbox label="Electric" wire:model="electric" />
            </div>
            <x-textarea label="Actual Cause" wire:model="cause_actual" class="col-span-2" rows="2" />
            <x-textarea label="Preventive" wire:model="preventive" class="col-span-2" rows="2" />
            <x-textarea label="Hasil Repair" wire:model="hasil_repair" class="col-span-2" rows="2" />
        </div>
        <x-slot:actions>
            <x-button label="Cancel" class="btn-ghost" wire:click="$set('editModal',false)" />
            <x-button label="Save Changes" class="btn-primary" wire:click="saveEdit" spinner="saveEdit" />
        </x-slot:actions>
    </x-modal>
</div>
