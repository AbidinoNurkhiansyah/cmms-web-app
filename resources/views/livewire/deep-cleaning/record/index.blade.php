<?php

use App\Services\DeepCleaningService;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;
use App\Livewire\Traits\WithAssetSelection;
use App\Livewire\Traits\WithPersonnel;

new class extends Component {
    use WithPagination, WithFileUploads, Toast, WithAssetSelection, WithPersonnel;

    public int $perPage = 10;
    public string $search = '';

    // Modals
    public bool $addModal = false;
    public bool $editModal = false;
    public bool $deleteModal = false;
    // Form Fields
    public ?int $formId = null;
    public ?int $recordToDelete = null;
    public string $Date = '';
    public string $description = '';

    // $LineName and $MachineName are provided by WithAssetSelection Trait
    // $pics is provided by WithPersonnel Trait

    public function mount(): void
    {
        $this->mountWithAssetSelection();
        $this->mountWithPersonnel();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function with(DeepCleaningService $service): array
    {
        $records = $service->getPaginated($this->perPage, $this->search, '');

        $records->getCollection()->transform(function ($item, $key) use ($records) {
            $item->row_index = $records->firstItem() + $key;
            return $item;
        });

        return [
            'records' => $records,
        ];
    }

    public function openAdd(): void
    {
        $this->reset(['formId', 'Date', 'LineName', 'MachineName', 'MachineNo', 'description']);
        $this->pics = [''];
        $this->Date = date('Y-m-d');
        $this->asset_id = null;
        $this->addModal = true;
    }

    public function saveAdd(DeepCleaningService $service): void
    {
        $this->validate([
            'Date' => 'required|date',
            'LineName' => 'required|string',
            'MachineName' => 'required|string',
            'description' => 'required|string',
            'pics.*' => 'nullable|string',
        ]);

        // Filter out empty PICs
        $filteredPics = [];
        foreach ($this->pics as $p) {
            if (trim($p) !== '') {
                $filteredPics[] = $p;
            }
        }

        $data = [
            'Date' => $this->Date,
            'LineName' => $this->LineName,
            'MachineNo' => $this->MachineNo,
            'MachineName' => $this->MachineName,
            'description' => $this->description,
            'pics' => array_values($filteredPics),
        ];



        $service->create($data);

        $this->addModal = false;
        $this->success('Deep Cleaning Record Created.');
    }

    public function openEdit(int $id, DeepCleaningService $service): void
    {
        $record = $service->getPaginated($this->perPage)->getCollection()->firstWhere('id', $id);
        if (!$record) {
            $this->error('Record not found.');
            return;
        }

        $this->formId = $id;
        $this->Date = $record->Date ? $record->Date->format('Y-m-d') : '';
        $this->LineName = $record->LineName ?? '';
        $this->MachineName = $record->MachineName ?? '';
        $this->MachineNo = $record->MachineNo ?? '';
        $this->description = $record->description ?? '';
        $this->pics = is_array($record->pics) && count($record->pics) > 0 ? $record->pics : [''];
        $this->updatedLineName($this->LineName); // Ensure machines are loaded

        $this->editModal = true;
    }

    public function saveEdit(DeepCleaningService $service): void
    {
        $this->validate([
            'Date' => 'required|date',
            'LineName' => 'required|string',
            'MachineName' => 'required|string',
            'description' => 'required|string',
            'pics.*' => 'nullable|string',
        ]);

        // Filter out empty PICs
        $filteredPics = [];
        foreach ($this->pics as $p) {
            if (trim($p) !== '') {
                $filteredPics[] = $p;
            }
        }

        $data = [
            'Date' => $this->Date,
            'LineName' => $this->LineName,
            'MachineNo' => $this->MachineNo,
            'MachineName' => $this->MachineName,
            'description' => $this->description,
            'pics' => array_values($filteredPics),
        ];



        $service->update($this->formId, $data);

        $this->editModal = false;
        $this->success('Deep Cleaning Record Updated.');
    }

    public function deleteRecord(DeepCleaningService $service): void
    {
        if ($this->recordToDelete) {
            $service->delete($this->recordToDelete);
            $this->success('Record deleted.');
            $this->deleteModal = false;
            $this->recordToDelete = null;
        }
    }

    public function updatedMachineName($value)
    {
        $this->MachineNo = '';
        if ($value && isset($this->machines)) {
            $machine = $this->machines->firstWhere('machine_name', $value);
            if ($machine) {
                $this->MachineNo = $machine->asset_no ?? '';
            }
        }
    }
};
?>

<div>
    <x-header title="Deep Cleaning / TPM" separator progress-indicator>
        <x-slot:actions>
            <!-- Desktop Actions -->
            <div class="hidden sm:flex flex-row items-center gap-2">
                <div class="w-60">
                    <x-input placeholder="Search machine, line..." wire:model.live.debounce="search" clearable
                        icon="o-magnifying-glass" />
                </div>
                <div class="w-20">
                    <x-select wire:model.live="perPage" :options="[['id' => 10, 'name' => '10'], ['id' => 25, 'name' => '25'], ['id' => 50, 'name' => '50'], ['id' => 100, 'name' => '100']]" option-value="id"
                        option-label="name" />
                </div>

                <x-button icon="o-plus" class="btn-primary" wire:click="openAdd">
                    <span class="inline">Add Record</span>
                </x-button>
            </div>
        </x-slot:actions>
    </x-header>

    <!-- Mobile Actions -->
    <div class="grid grid-cols-4 gap-2 mb-4 sm:hidden">
        <div class="col-span-3">
            <x-input placeholder="Search machine, line..." wire:model.live.debounce="search" clearable
                icon="o-magnifying-glass" />
        </div>
        <div class="col-span-1">
            <x-select wire:model.live="perPage" :options="[['id' => 10, 'name' => '10'], ['id' => 25, 'name' => '25'], ['id' => 50, 'name' => '50'], ['id' => 100, 'name' => '100']]" option-value="id" option-label="name"
                class="w-full" />
        </div>
        <div class="col-span-4 mt-2">
            <x-button icon="o-plus" class="btn-primary w-full" wire:click="openAdd">
                <span class="inline">Add Record</span>
            </x-button>
        </div>
    </div>

    <x-card>
        <div class="overflow-x-auto w-full">
            <x-table :headers="[
        ['key' => 'row_index', 'label' => 'No'],
        ['key' => 'Date', 'label' => 'Date'],
        ['key' => 'LineName', 'label' => 'Line'],
        ['key' => 'MachineName', 'label' => 'Machine'],
        ['key' => 'MachineNo', 'label' => 'Asset No'],
        ['key' => 'description', 'label' => 'Description'],
        ['key' => 'actions', 'label' => 'Action', 'class' => 'text-center w-24'],
    ]" :rows="$records" with-pagination
                @row-click="Livewire.navigate('{{ route('deep-cleaning.index') }}/' + $event.detail.id)"
                class="hover:cursor-pointer">

                @scope('cell_Date', $r)
                {{ $r->Date ? $r->Date->format('Y-m-d') : '-' }}
                @endscope

                @scope('cell_description', $r)
                @if($r->description)
                                <x-badge value="{{ $r->description }}" class="{{ match ($r->description) {
                        'TPM' => 'badge-primary',
                        'Preventive' => 'badge-info',
                        'Repair' => 'badge-warning',
                        default => 'badge-ghost'
                    } }} rounded-full badge-sm" />
                @else
                    -
                @endif
                @endscope

                @scope('cell_actions', $r)
                <div class="flex gap-1 justify-center" @click.stop>
                    <x-button icon="o-pencil-square" class="btn-ghost btn-xs" wire:click="openEdit({{ $r->id }})" />
                    <x-button icon="o-trash" class="btn-ghost btn-xs text-error"
                        @click="$wire.recordToDelete = {{ $r->id }}; $wire.deleteModal = true" />
                </div>
                @endscope
            </x-table>
        </div>
    </x-card>

    {{-- Add Modal --}}
    @include('livewire.deep-cleaning.record.partials.add-modal')

    {{-- Edit Modal --}}
    @include('livewire.deep-cleaning.record.partials.edit-modal')

    {{-- Delete Modal --}}
    @include('livewire.deep-cleaning.record.partials.delete-modal')
</div>