<?php

use App\Services\TpmService;
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
    public string $LineName             = '';
    public string $MachineNo            = '';
    public string $MachineName          = '';
    public string $pic                  = '';
    public string $status               = 'Scheduled';
    public string $description          = '';
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

    public function with(TpmService $tpmService): array
    {
        return [
            'records' => $tpmService->getPaginated(15, $this->search, $this->statusFilter),
        ];
    }

    public function openAdd(): void
    {
        $this->reset(['formId','date','LineName','MachineNo','MachineName','pic','description','result','photo_before','photo_after']);
        $this->status = 'Scheduled';
        $this->date = date('Y-m-d');
        $this->addModal = true;
    }

    public function saveAdd(TpmService $tpmService): void
    {
        $this->validate([
            'date'         => 'required|date',
            'LineName'     => 'required|string',
            'MachineName'  => 'required|string',
            'photo_before' => 'nullable|image|max:4096',
            'photo_after'  => 'nullable|image|max:4096',
        ]);

        $data = [
            'date'        => $this->date,
            'LineName'    => $this->LineName,
            'MachineNo'   => $this->MachineNo,
            'MachineName' => $this->MachineName,
            'pic'         => $this->pic,
            'status'      => $this->status,
            'description' => $this->description,
            'result'      => $this->result,
        ];

        // Handling files should ideally be in service, but kept simple here for the prototype.
        if ($this->photo_before) {
            $data['photo_before'] = $this->photo_before->store('tpm_photos', 'public');
        }
        if ($this->photo_after) {
            $data['photo_after'] = $this->photo_after->store('tpm_photos', 'public');
        }

        $tpmService->create($data);

        $this->addModal = false;
        $this->success('TPM Activity Scheduled.');
    }

    public function openEdit(int $id, TpmService $tpmService): void
    {
        $record = $tpmService->getPaginated()->getCollection()->firstWhere('id', $id);
        if(!$record) {
            $this->error('Record not found.');
            return;
        }

        $this->formId       = $id;
        $this->date         = $record->date ? $record->date->format('Y-m-d') : '';
        $this->LineName     = $record->LineName ?? '';
        $this->MachineNo    = $record->MachineNo ?? '';
        $this->MachineName  = $record->MachineName ?? '';
        $this->pic          = $record->pic ?? '';
        $this->status       = $record->status ?? 'Scheduled';
        $this->description  = $record->description ?? '';
        $this->result       = $record->result ?? '';
        
        $this->photo_before = null;
        $this->photo_after  = null;
        
        $this->editModal    = true;
    }

    public function saveEdit(TpmService $tpmService): void
    {
        $this->validate([
            'date'         => 'required|date',
            'LineName'     => 'required|string',
            'MachineName'  => 'required|string',
            'photo_before' => 'nullable|image|max:4096',
            'photo_after'  => 'nullable|image|max:4096',
        ]);

        $data = [
            'date'        => $this->date,
            'LineName'    => $this->LineName,
            'MachineNo'   => $this->MachineNo,
            'MachineName' => $this->MachineName,
            'pic'         => $this->pic,
            'status'      => $this->status,
            'description' => $this->description,
            'result'      => $this->result,
        ];

        if ($this->photo_before) {
            $data['photo_before'] = $this->photo_before->store('tpm_photos', 'public');
        }
        if ($this->photo_after) {
            $data['photo_after'] = $this->photo_after->store('tpm_photos', 'public');
        }

        $tpmService->update($this->formId, $data);

        $this->editModal = false;
        $this->success('TPM Activity Updated.');
    }

    public function deleteTpm(int $id, TpmService $tpmService): void
    {
        $tpmService->delete($id);
        $this->success('TPM Activity deleted.');
    }
};
?>

<div>
    <x-header title="Total Productive Maintenance (TPM)" separator progress-indicator>
        <x-slot:middle class="!justify-end gap-2">
            <x-select 
                wire:model.live="statusFilter" 
                :options="[['id'=>'','name'=>'All Status'],['id'=>'Scheduled','name'=>'Scheduled'],['id'=>'In Progress','name'=>'In Progress'],['id'=>'Done','name'=>'Done']]" 
                option-value="id" option-label="name" 
            />
            <x-input placeholder="Search machine, line..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button label="Add TPM" icon="o-plus" class="btn-primary" wire:click="openAdd" />
        </x-slot:actions>
    </x-header>

    <x-card>
        <x-table
            :headers="[
                ['key' => 'date',         'label' => 'Date'],
                ['key' => 'LineName',     'label' => 'Line'],
                ['key' => 'MachineName',  'label' => 'Machine'],
                ['key' => 'pic',          'label' => 'PIC'],
                ['key' => 'status',       'label' => 'Status'],
                ['key' => 'result',       'label' => 'Result'],
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
                            'scheduled' => 'badge-info',
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
                        wire:click="deleteTpm({{ $r->id }})" 
                        wire:confirm="Delete this record? This cannot be undone." />
                </div>
            @endscope
        </x-table>
    </x-card>

    {{-- Add Modal --}}
    <x-modal wire:model="addModal" title="New TPM Schedule" separator>
        <div class="grid grid-cols-2 gap-3">
            <x-input label="Date" type="date" wire:model="date" />
            <x-input label="Line Name" wire:model="LineName" />
            <x-input label="Machine No" wire:model="MachineNo" />
            <x-input label="Machine Name" wire:model="MachineName" />
            <x-input label="PIC" wire:model="pic" />
            <x-select label="Status" wire:model="status"
                :options="[['id'=>'Scheduled','name'=>'Scheduled'],['id'=>'In Progress','name'=>'In Progress'],['id'=>'Done','name'=>'Done']]"
                option-value="id" option-label="name" />
            <x-textarea label="Description" wire:model="description" class="col-span-2" rows="2" />
        </div>
        <x-slot:actions>
            <x-button label="Cancel" class="btn-ghost" wire:click="$set('addModal',false)" />
            <x-button label="Submit" class="btn-primary" wire:click="saveAdd" spinner="saveAdd" />
        </x-slot:actions>
    </x-modal>

    {{-- Edit Modal --}}
    <x-modal wire:model="editModal" title="Edit TPM / Result" separator>
        <div class="grid grid-cols-2 gap-3">
            <x-input label="Date" type="date" wire:model="date" />
            <x-input label="Line Name" wire:model="LineName" />
            <x-input label="Machine No" wire:model="MachineNo" />
            <x-input label="Machine Name" wire:model="MachineName" />
            <x-input label="PIC" wire:model="pic" />
            <x-select label="Status" wire:model="status"
                :options="[['id'=>'Scheduled','name'=>'Scheduled'],['id'=>'In Progress','name'=>'In Progress'],['id'=>'Done','name'=>'Done']]"
                option-value="id" option-label="name" />
            <x-textarea label="Description" wire:model="description" class="col-span-2" rows="2" />
            <x-textarea label="Result" wire:model="result" class="col-span-2" rows="2" />
            
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
