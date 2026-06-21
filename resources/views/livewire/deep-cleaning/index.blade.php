<?php

use App\Services\DeepCleaningService;
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
    public bool $addModal = false;
    public bool $editModal = false;
    public bool $deleteModal = false;
    // Form Fields
    public ?int $formId = null;
    public ?int $recordToDelete = null;
    public string $Date = '';

    // $LineName and $MachineName are provided by WithAssetSelection Trait
    public array $pics = [''];
    public string $status = 'Scheduled';
    public string $description = '';
    public string $itemcheck = '';
    public string $action = '';
    public string $sparepart_id = '';
    public ?int $sparepart_qty = null;
    public $before_photo = null;
    public $after_photo = null;

    public function mount(): void
    {
        $this->mountWithAssetSelection();
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

    public function addPic(): void
    {
        $this->pics[] = '';
    }

    public function removePic(int $index): void
    {
        unset($this->pics[$index]);
        $this->pics = array_values($this->pics); // Re-index
    }

    public function openAdd(): void
    {
        $this->reset(['formId', 'Date', 'LineName', 'MachineName', 'status', 'description', 'itemcheck', 'action', 'sparepart_id', 'sparepart_qty', 'before_photo', 'after_photo']);
        $this->pics = [''];
        $this->status = 'Scheduled';
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
            'pics.*' => 'nullable|string',
            'before_photo' => 'nullable|image|max:4096',
            'after_photo' => 'nullable|image|max:4096',
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
            'MachineNo' => $this->MachineName, // Wait, machine no isn't fully supported via WithAssetSelection. Will map it roughly if needed.
            'MachineName' => $this->MachineName,
            'pics' => array_values($filteredPics),
            'status' => $this->status,
            'description' => $this->description,
            'itemcheck' => $this->itemcheck,
            'action' => $this->action,
            'sparepart_id' => $this->sparepart_id,
            'sparepart_qty' => $this->sparepart_qty,
        ];

        if ($this->before_photo) {
            $data['before_photo'] = $this->before_photo->store('deep_cleaning_photos', 'public');
        }
        if ($this->after_photo) {
            $data['after_photo'] = $this->after_photo->store('deep_cleaning_photos', 'public');
        }

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
        $this->pics = is_array($record->pics) && count($record->pics) > 0 ? $record->pics : [''];
        $this->status = $record->status ?? 'Scheduled';
        $this->description = $record->description ?? '';
        $this->itemcheck = $record->itemcheck ?? '';
        $this->action = $record->action ?? '';
        $this->sparepart_id = $record->sparepart_id ?? '';
        $this->sparepart_qty = $record->sparepart_qty;

        $this->updatedLineName($this->LineName); // Ensure machines are loaded

        $this->before_photo = null;
        $this->after_photo = null;

        $this->editModal = true;
    }

    public function saveEdit(DeepCleaningService $service): void
    {
        $this->validate([
            'Date' => 'required|date',
            'LineName' => 'required|string',
            'MachineName' => 'required|string',
            'pics.*' => 'nullable|string',
            'before_photo' => 'nullable|image|max:4096',
            'after_photo' => 'nullable|image|max:4096',
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
            'MachineNo' => $this->MachineName,
            'MachineName' => $this->MachineName,
            'pics' => array_values($filteredPics),
            'status' => $this->status,
            'description' => $this->description,
            'itemcheck' => $this->itemcheck,
            'action' => $this->action,
            'sparepart_id' => $this->sparepart_id,
            'sparepart_qty' => $this->sparepart_qty,
        ];

        if ($this->before_photo) {
            $data['before_photo'] = $this->before_photo->store('deep_cleaning_photos', 'public');
        }
        if ($this->after_photo) {
            $data['after_photo'] = $this->after_photo->store('deep_cleaning_photos', 'public');
        }

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
                    <x-select wire:model.live="perPage"
                        :options="[['id' => 10, 'name' => '10'], ['id' => 25, 'name' => '25'], ['id' => 50, 'name' => '50'], ['id' => 100, 'name' => '100']]"
                        option-value="id" option-label="name" />
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
            <x-select wire:model.live="perPage"
                :options="[['id' => 10, 'name' => '10'], ['id' => 25, 'name' => '25'], ['id' => 50, 'name' => '50'], ['id' => 100, 'name' => '100']]"
                option-value="id" option-label="name" class="w-full" />
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
        ['key' => 'description', 'label' => 'Description'],
        ['key' => 'actions', 'label' => 'Action', 'class' => 'text-center w-24'],
    ]"
                :rows="$records" with-pagination @row-click="Livewire.navigate('{{ route('deep-cleaning.index') }}/' + $event.detail.id)"
                class="hover:cursor-pointer">

                @scope('cell_Date', $r)
                {{ $r->Date ? $r->Date->format('Y-m-d') : '-' }}
                @endscope

                @scope('cell_description', $r)
                <span class="truncate block max-w-xs" title="{{ $r->description }}">{{ $r->description ?: '-' }}</span>
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
    @include('livewire.deep-cleaning.partials.add-modal')

    {{-- Edit Modal --}}
    @include('livewire.deep-cleaning.partials.edit-modal')

    {{-- Delete Modal --}}
    @include('livewire.deep-cleaning.partials.delete-modal')
</div>