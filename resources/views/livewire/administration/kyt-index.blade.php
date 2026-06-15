<?php

use App\Services\SkyService;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;

new class extends Component {
    use WithPagination, WithFileUploads, Toast;

    public string $search = '';

    // Modals
    public bool   $addModal             = false;
    public bool   $editModal            = false;

    // Form Fields
    public ?int   $formId               = null;
    public string $date                 = '';
    public string $userId               = '';
    public string $lokasi               = '';
    public string $bahaya               = '';
    public string $countermeasure       = '';
    public string $resiko               = '';
    
    // File Uploads
    public $img                         = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function with(SkyService $service): array
    {
        return [
            'records' => $service->getPaginated(15, $this->search),
        ];
    }

    public function openAdd(): void
    {
        $this->reset(['formId','date','userId','lokasi','bahaya','countermeasure','resiko','img']);
        $this->date = date('Y-m-d');
        $this->addModal = true;
    }

    public function saveAdd(SkyService $service): void
    {
        $this->validate([
            'date'           => 'required|date',
            'userId'         => 'required|string',
            'lokasi'         => 'required|string',
            'bahaya'         => 'required|string',
            'countermeasure' => 'required|string',
            'resiko'         => 'required|string',
            'img'            => 'nullable|image|max:5120', // 5MB max
        ]);

        $service->create([
            'date'           => $this->date,
            'userId'         => $this->userId,
            'lokasi'         => $this->lokasi,
            'bahaya'         => $this->bahaya,
            'countermeasure' => $this->countermeasure,
            'resiko'         => $this->resiko,
        ], $this->img);

        $this->addModal = false;
        $this->success('KYT Record Created.');
    }

    public function openEdit(int $no, SkyService $service): void
    {
        $record = $service->getPaginated()->getCollection()->firstWhere('no', $no);
        if(!$record) {
            $this->error('Record not found.');
            return;
        }

        $this->formId         = $no;
        $this->date           = $record->date ? $record->date->format('Y-m-d') : '';
        $this->userId         = $record->userId ?? '';
        $this->lokasi         = $record->lokasi ?? '';
        $this->bahaya         = $record->bahaya ?? '';
        $this->countermeasure = $record->countermeasure ?? '';
        $this->resiko         = $record->resiko ?? '';
        $this->img            = null;
        
        $this->editModal      = true;
    }

    public function saveEdit(SkyService $service): void
    {
        $this->validate([
            'date'           => 'required|date',
            'userId'         => 'required|string',
            'lokasi'         => 'required|string',
            'bahaya'         => 'required|string',
            'countermeasure' => 'required|string',
            'resiko'         => 'required|string',
            'img'            => 'nullable|image|max:5120',
        ]);

        $service->update($this->formId, [
            'date'           => $this->date,
            'userId'         => $this->userId,
            'lokasi'         => $this->lokasi,
            'bahaya'         => $this->bahaya,
            'countermeasure' => $this->countermeasure,
            'resiko'         => $this->resiko,
        ], $this->img);

        $this->editModal = false;
        $this->success('KYT Record Updated.');
    }

    public function deleteRecord(int $no, SkyService $service): void
    {
        $service->delete($no);
        $this->success('Record deleted.');
    }
};
?>

<div>
    <x-header title="KYT / Safety Administration" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Search lokasi, bahaya..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button label="Add Report" icon="o-plus" class="btn-primary" wire:click="openAdd" />
        </x-slot:actions>
    </x-header>

    <x-card>
        <x-table
            :headers="[
                ['key' => 'date',   'label' => 'Date'],
                ['key' => 'userId', 'label' => 'User ID'],
                ['key' => 'lokasi', 'label' => 'Location'],
                ['key' => 'bahaya', 'label' => 'Hazard (Bahaya)'],
                ['key' => 'resiko', 'label' => 'Risk (Resiko)'],
                ['key' => 'img',    'label' => 'Photo'],
            ]"
            :rows="$records"
            with-pagination
        >
            @scope('cell_date', $r)
                {{ $r->date ? $r->date->format('Y-m-d') : '-' }}
            @endscope

            @scope('cell_img', $r)
                @if($r->img)
                    <div class="avatar">
                        <div class="w-12 rounded">
                            <img src="{{ Storage::url($r->img) }}" alt="KYT Image" />
                        </div>
                    </div>
                @else
                    <span class="text-xs text-gray-500">No Image</span>
                @endif
            @endscope

            @scope('actions', $r)
                <div class="flex gap-1">
                    <x-button icon="o-pencil-square" class="btn-ghost btn-xs" wire:click="openEdit({{ $r->no }})" />
                    <x-button icon="o-trash" class="btn-ghost btn-xs text-error" 
                        wire:click="deleteRecord({{ $r->no }})" 
                        wire:confirm="Delete this record? This cannot be undone." />
                </div>
            @endscope
        </x-table>
    </x-card>

    {{-- Add Modal --}}
    <x-modal wire:model="addModal" title="New KYT Report" separator>
        <div class="grid grid-cols-2 gap-3">
            <x-input label="Date" type="date" wire:model="date" />
            <x-input label="User ID (PIC)" wire:model="userId" />
            <x-input label="Location" wire:model="lokasi" class="col-span-2" />
            
            <x-textarea label="Hazard (Bahaya)" wire:model="bahaya" class="col-span-2" rows="2" />
            <x-textarea label="Risk (Resiko)" wire:model="resiko" class="col-span-2" rows="2" />
            <x-textarea label="Countermeasure" wire:model="countermeasure" class="col-span-2" rows="2" />
                
            <div class="col-span-2 mt-2 border-t pt-2">
                <label class="label text-sm font-semibold">Photo</label>
                <input type="file" wire:model="img" accept="image/*" class="file-input file-input-bordered file-input-sm w-full" />
                @if ($img)
                    <div class="mt-2 text-sm text-gray-500">Photo selected for upload.</div>
                @endif
            </div>
        </div>
        <x-slot:actions>
            <x-button label="Cancel" class="btn-ghost" wire:click="$set('addModal',false)" />
            <x-button label="Submit" class="btn-primary" wire:click="saveAdd" spinner="saveAdd" />
        </x-slot:actions>
    </x-modal>

    {{-- Edit Modal --}}
    <x-modal wire:model="editModal" title="Edit KYT Report" separator>
        <div class="grid grid-cols-2 gap-3">
            <x-input label="Date" type="date" wire:model="date" />
            <x-input label="User ID (PIC)" wire:model="userId" />
            <x-input label="Location" wire:model="lokasi" class="col-span-2" />
            
            <x-textarea label="Hazard (Bahaya)" wire:model="bahaya" class="col-span-2" rows="2" />
            <x-textarea label="Risk (Resiko)" wire:model="resiko" class="col-span-2" rows="2" />
            <x-textarea label="Countermeasure" wire:model="countermeasure" class="col-span-2" rows="2" />
                
            <div class="col-span-2 mt-2 border-t pt-2">
                <label class="label text-sm font-semibold">Replace Photo</label>
                <input type="file" wire:model="img" accept="image/*" class="file-input file-input-bordered file-input-sm w-full" />
            </div>
        </div>
        <x-slot:actions>
            <x-button label="Cancel" class="btn-ghost" wire:click="$set('editModal',false)" />
            <x-button label="Save Changes" class="btn-primary" wire:click="saveEdit" spinner="saveEdit" />
        </x-slot:actions>
    </x-modal>
</div>
