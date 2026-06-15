<?php

use App\Services\OneHourOverService;
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
    public string $date                 = '';
    public string $group_name           = '';
    public string $line                 = '';
    public string $machine              = '';
    public string $problem              = '';
    public string $status               = 'Open';
    
    // File Uploads
    public $file_rsa                    = null;
    public $file_rca                    = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }
    
    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function with(OneHourOverService $service): array
    {
        return [
            'records' => $service->getPaginated(15, $this->search, $this->statusFilter),
        ];
    }

    public function openAdd(): void
    {
        $this->reset(['formId','date','group_name','line','machine','problem','file_rsa','file_rca']);
        $this->status = 'Open';
        $this->date = date('Y-m-d');
        $this->addModal = true;
    }

    public function saveAdd(OneHourOverService $service): void
    {
        $this->validate([
            'date'       => 'required|date',
            'line'       => 'required|string',
            'machine'    => 'required|string',
            'problem'    => 'required|string',
            'file_rsa'   => 'nullable|file|max:10240', // 10MB max
            'file_rca'   => 'nullable|file|max:10240',
        ]);

        $service->create([
            'date'       => $this->date,
            'group_name' => $this->group_name,
            'line'       => $this->line,
            'machine'    => $this->machine,
            'problem'    => $this->problem,
            'status'     => $this->status,
        ], $this->file_rsa, $this->file_rca);

        $this->addModal = false;
        $this->success('One Hour Over Record Created.');
    }

    public function openEdit(int $id, OneHourOverService $service): void
    {
        $record = $service->getPaginated()->getCollection()->firstWhere('id', $id);
        if(!$record) {
            $this->error('Record not found.');
            return;
        }

        $this->formId       = $id;
        $this->date         = $record->date ? $record->date->format('Y-m-d') : '';
        $this->group_name   = $record->group_name ?? '';
        $this->line         = $record->line ?? '';
        $this->machine      = $record->machine ?? '';
        $this->problem      = $record->problem ?? '';
        $this->status       = $record->status ?? 'Open';
        
        $this->file_rsa     = null;
        $this->file_rca     = null;
        
        $this->editModal    = true;
    }

    public function saveEdit(OneHourOverService $service): void
    {
        $this->validate([
            'date'       => 'required|date',
            'line'       => 'required|string',
            'machine'    => 'required|string',
            'problem'    => 'required|string',
            'file_rsa'   => 'nullable|file|max:10240',
            'file_rca'   => 'nullable|file|max:10240',
        ]);

        $service->update($this->formId, [
            'date'       => $this->date,
            'group_name' => $this->group_name,
            'line'       => $this->line,
            'machine'    => $this->machine,
            'problem'    => $this->problem,
            'status'     => $this->status,
        ], $this->file_rsa, $this->file_rca);

        $this->editModal = false;
        $this->success('One Hour Over Record Updated.');
    }

    public function deleteRecord(int $id, OneHourOverService $service): void
    {
        $service->delete($id);
        $this->success('Record deleted.');
    }
};
?>

<div>
    <x-header title="One Hour Over" separator progress-indicator>
        <x-slot:middle class="!justify-end gap-2">
            <x-select 
                wire:model.live="statusFilter" 
                :options="[['id'=>'','name'=>'All Status'],['id'=>'Open','name'=>'Open'],['id'=>'Closed','name'=>'Closed']]" 
                option-value="id" option-label="name" 
            />
            <x-input placeholder="Search problem, line, machine..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button label="Add Record" icon="o-plus" class="btn-primary" wire:click="openAdd" />
        </x-slot:actions>
    </x-header>

    <x-card>
        <x-table
            :headers="[
                ['key' => 'date',         'label' => 'Date'],
                ['key' => 'group_name',   'label' => 'Group'],
                ['key' => 'line',         'label' => 'Line'],
                ['key' => 'machine',      'label' => 'Machine'],
                ['key' => 'problem',      'label' => 'Problem'],
                ['key' => 'status',       'label' => 'Status'],
            ]"
            :rows="$records"
            with-pagination
        >
            @scope('cell_date', $r)
                {{ $r->date ? $r->date->format('Y-m-d') : '-' }}
            @endscope

            @scope('cell_status', $r)
                <x-badge label="{{ $r->status }}" 
                    class="{{ 
                        match(strtolower($r->status)) {
                            'open' => 'badge-error',
                            'closed' => 'badge-success',
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
    <x-modal wire:model="addModal" title="New One Hour Over Record" separator>
        <div class="grid grid-cols-2 gap-3">
            <x-input label="Date" type="date" wire:model="date" />
            <x-input label="Group Name" wire:model="group_name" />
            <x-input label="Line" wire:model="line" />
            <x-input label="Machine" wire:model="machine" />
            <x-textarea label="Problem" wire:model="problem" class="col-span-2" rows="3" />
            <x-select label="Status" wire:model="status" class="col-span-2"
                :options="[['id'=>'Open','name'=>'Open'],['id'=>'Closed','name'=>'Closed']]"
                option-value="id" option-label="name" />
                
            <div class="col-span-2 grid grid-cols-2 gap-3 mt-2 border-t pt-2">
                <div>
                    <label class="label text-sm font-semibold">File RSA</label>
                    <input type="file" wire:model="file_rsa" class="file-input file-input-bordered file-input-sm w-full" />
                </div>
                <div>
                    <label class="label text-sm font-semibold">File RCA</label>
                    <input type="file" wire:model="file_rca" class="file-input file-input-bordered file-input-sm w-full" />
                </div>
            </div>
        </div>
        <x-slot:actions>
            <x-button label="Cancel" class="btn-ghost" wire:click="$set('addModal',false)" />
            <x-button label="Submit" class="btn-primary" wire:click="saveAdd" spinner="saveAdd" />
        </x-slot:actions>
    </x-modal>

    {{-- Edit Modal --}}
    <x-modal wire:model="editModal" title="Edit Record" separator>
        <div class="grid grid-cols-2 gap-3">
            <x-input label="Date" type="date" wire:model="date" />
            <x-input label="Group Name" wire:model="group_name" />
            <x-input label="Line" wire:model="line" />
            <x-input label="Machine" wire:model="machine" />
            <x-textarea label="Problem" wire:model="problem" class="col-span-2" rows="3" />
            <x-select label="Status" wire:model="status" class="col-span-2"
                :options="[['id'=>'Open','name'=>'Open'],['id'=>'Closed','name'=>'Closed']]"
                option-value="id" option-label="name" />
                
            <div class="col-span-2 grid grid-cols-2 gap-3 mt-2 border-t pt-2">
                <div>
                    <label class="label text-sm font-semibold">Replace File RSA</label>
                    <input type="file" wire:model="file_rsa" class="file-input file-input-bordered file-input-sm w-full" />
                </div>
                <div>
                    <label class="label text-sm font-semibold">Replace File RCA</label>
                    <input type="file" wire:model="file_rca" class="file-input file-input-bordered file-input-sm w-full" />
                </div>
            </div>
        </div>
        <x-slot:actions>
            <x-button label="Cancel" class="btn-ghost" wire:click="$set('editModal',false)" />
            <x-button label="Save Changes" class="btn-primary" wire:click="saveEdit" spinner="saveEdit" />
        </x-slot:actions>
    </x-modal>
</div>
