<?php

use App\Services\OneHourOverService;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;
use App\Livewire\Traits\WithAssetSelection;

new class extends Component {
    use WithPagination, WithFileUploads, Toast, WithAssetSelection;

    public string $search = '';
    public string $statusFilter = '';

    // Modals
    public bool   $addModal             = false;
    public bool   $editModal            = false;

    // Form Fields
    public ?int   $formId               = null;
    public string $date                 = '';
    public string $group_name           = '';
    // $LineName and $MachineName are provided by WithAssetSelection Trait
    public string $problem              = '';
    public string $status               = 'Open';
    
    // File Uploads
    public $file_rsa                    = null;
    public $file_rca                    = null;

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

    public function with(OneHourOverService $service): array
    {
        return [
            'records' => $service->getPaginated(15, $this->search, $this->statusFilter),
        ];
    }

    public function openAdd(): void
    {
        $this->reset(['formId','date','group_name','LineName','MachineName','problem','file_rsa','file_rca']);
        $this->status = 'Open';
        $this->date = date('Y-m-d');
        $this->asset_id = null;
        $this->addModal = true;
    }

    public function saveAdd(OneHourOverService $service): void
    {
        $this->validate([
            'date'        => 'required|date',
            'LineName'    => 'required|string',
            'MachineName' => 'required|string',
            'problem'     => 'required|string',
            'file_rsa'    => 'nullable|file|max:10240', // 10MB max
            'file_rca'    => 'nullable|file|max:10240',
        ]);

        $service->create([
            'date'       => $this->date,
            'group_name' => $this->group_name,
            'line'       => $this->LineName,
            'machine'    => $this->MachineName,
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
        $this->LineName     = $record->line ?? '';
        $this->MachineName  = $record->machine ?? '';
        $this->problem      = $record->problem ?? '';
        $this->status       = $record->status ?? 'Open';
        
        $this->updatedLineName($this->LineName); // Ensure machines are loaded
        
        $this->file_rsa     = null;
        $this->file_rca     = null;
        
        $this->editModal    = true;
    }

    public function saveEdit(OneHourOverService $service): void
    {
        $this->validate([
            'date'        => 'required|date',
            'LineName'    => 'required|string',
            'MachineName' => 'required|string',
            'problem'     => 'required|string',
            'file_rsa'    => 'nullable|file|max:10240',
            'file_rca'    => 'nullable|file|max:10240',
        ]);

        $service->update($this->formId, [
            'date'       => $this->date,
            'group_name' => $this->group_name,
            'line'       => $this->LineName,
            'machine'    => $this->MachineName,
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
        <x-slot:actions>
            <div class="flex flex-col sm:flex-row w-full sm:w-auto gap-2">
                <!-- Status Filter -->
                <div class="flex-none sm:w-40">
                    <x-select 
                        wire:model.live="statusFilter" 
                        :options="[['id'=>'','name'=>'All Status'],['id'=>'Open','name'=>'Open'],['id'=>'Closed','name'=>'Closed']]" 
                        option-value="id" option-label="name" 
                    />
                </div>

                <!-- Search -->
                <div class="flex-1 sm:w-60">
                    <x-input placeholder="Search problem, line, machine..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
                </div>

                <!-- Add -->
                <div class="flex-none">
                    <x-button icon="o-plus" class="btn-primary" wire:click="openAdd">
                        <span class="hidden sm:inline">Add Record</span>
                    </x-button>
                </div>
            </div>
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
    @include('livewire.maintenance.one-hour-over.partials.add-modal')

    {{-- Edit Modal --}}
    @include('livewire.maintenance.one-hour-over.partials.edit-modal')
</div>
