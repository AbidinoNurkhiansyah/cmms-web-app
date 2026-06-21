<?php

use App\Services\OneHourOverService;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;
use App\Livewire\Traits\WithAssetSelection;

new class extends Component {
    use WithPagination, WithFileUploads, Toast, WithAssetSelection;

    public int $perPage = 10;
    public string $search = '';

    // Modals
    public bool   $addModal             = false;
    public bool   $editModal            = false;
    public bool   $deleteModal          = false;

    // Form Fields
    public ?int   $formId               = null;
    public ?int   $recordToDelete       = null;
    public string $date                 = '';
    public string $group_name           = '';
    // $LineName and $MachineName are provided by WithAssetSelection Trait
    public string $problem              = '';
    
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

    public function with(OneHourOverService $service): array
    {
        return [
            'records' => $service->getPaginated($this->perPage, $this->search),
        ];
    }

    public function openAdd(): void
    {
        $this->reset(['formId','date','group_name','LineName','MachineName','problem','file_rsa','file_rca']);
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
        ], $this->file_rsa, $this->file_rca);

        $this->editModal = false;
        $this->success('One Hour Over Record Updated.');
    }

    public function openDelete(int $id): void
    {
        $this->recordToDelete = $id;
        $this->deleteModal = true;
    }

    public function deleteRecord(OneHourOverService $service): void
    {
        if ($this->recordToDelete) {
            $service->delete($this->recordToDelete);
            $this->success('Record deleted.');
            $this->deleteModal = false;
            $this->recordToDelete = null;
        }
    }
};
?>

<div>
    <x-header title="One Hour Over" separator progress-indicator>
        <x-slot:actions>
            <!-- Desktop Actions -->
            <div class="hidden sm:flex flex-row items-center gap-2">
                <div class="w-60">
                    <x-input placeholder="Search problem, line, machine..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
                </div>
                <div class="w-20">
                    <x-select wire:model.live="perPage" :options="[['id'=>10,'name'=>'10'],['id'=>25,'name'=>'25'],['id'=>50,'name'=>'50'],['id'=>100,'name'=>'100']]" option-value="id" option-label="name" />
                </div>
                <x-button icon="o-plus" class="btn-primary" wire:click="openAdd">
                    <span class="inline">Add Record</span>
                </x-button>
            </div>
        </x-slot:actions>
    </x-header>

    <!-- Mobile Actions -->
    <div class="grid grid-cols-4 gap-2 mb-4 sm:hidden">
        <!-- Search (3/4 on mobile) -->
        <div class="col-span-3">
            <x-input placeholder="Search problem, line, machine..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </div>
        <!-- Rows per page (1/4 on mobile) -->
        <div class="col-span-1">
            <x-select wire:model.live="perPage" :options="[['id'=>10,'name'=>'10'],['id'=>25,'name'=>'25'],['id'=>50,'name'=>'50'],['id'=>100,'name'=>'100']]" option-value="id" option-label="name" class="w-full" />
        </div>
        <!-- Add (Full width on mobile) -->
        <div class="col-span-4 mt-2">
            <x-button icon="o-plus" class="btn-primary w-full" wire:click="openAdd">
                <span class="inline">Add Record</span>
            </x-button>
        </div>
    </div>

    <x-card>
        <div class="overflow-x-auto w-full">
            <x-table
                :headers="[
                    ['key' => 'date',         'label' => 'Date'],
                    ['key' => 'group_name',   'label' => 'Group'],
                    ['key' => 'line',         'label' => 'Line'],
                    ['key' => 'machine',      'label' => 'Machine'],
                    ['key' => 'problem',      'label' => 'Problem'],
                    ['key' => 'files',        'label' => 'Files', 'class' => 'text-center'],
                    ['key' => 'actions',      'label' => 'Action', 'class' => 'text-center w-24'],
                ]"
                :rows="$records"
                with-pagination
            >
            @scope('cell_date', $r)
                {{ $r->date ? $r->date->format('Y-m-d') : '-' }}
            @endscope

            @scope('cell_files', $r)
            <div class="flex gap-1 justify-center">
                @if($r->file_rsa)
                    <x-button label="RSA" icon="o-document" link="{{ asset('storage/' . $r->file_rsa) }}" external
                        class="btn-xs btn-outline btn-info" tooltip="Repair Step Analysis" />
                @endif
                @if($r->file_rca)
                    <x-button label="RCA" icon="o-document" link="{{ asset('storage/' . $r->file_rca) }}" external
                        class="btn-xs btn-outline btn-success" tooltip="Root Cause Analysis" />
                @endif
            </div>
            @endscope

            @scope('cell_actions', $r)
                <div class="flex gap-1 justify-center">
                    <x-button icon="o-pencil-square" class="btn-ghost btn-xs" wire:click="openEdit({{ $r->id }})" />
                    <x-button icon="o-trash" class="btn-ghost btn-xs text-error" wire:click="openDelete({{ $r->id }})" />
                </div>
            @endscope
        </x-table>
        </div>
    </x-card>

    {{-- Add Modal --}}
    @include('livewire.maintenance.one-hour-over.partials.add-modal')

    {{-- Edit Modal --}}
    @include('livewire.maintenance.one-hour-over.partials.edit-modal')

    {{-- Delete Modal --}}
    @include('livewire.maintenance.one-hour-over.partials.delete-modal')
</div>
