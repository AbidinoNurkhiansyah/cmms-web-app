<?php

use App\Services\SparePartService;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Mary\Traits\Toast;

new class extends Component {
    use WithPagination, WithFileUploads, Toast;

    public string $search = '';

    // Add modal
    public bool   $addModal       = false;
    public string $addPartNumber  = '';
    public string $addPartName    = '';
    public string $addNoRack      = '';
    public int    $addLastStock   = 0;
    public string $addMaker       = '';
    public string $addMachine     = '';
    public string $addGroup       = '';
    public string $addStatus      = 'Y';
    public        $addPhoto       = null;

    // Edit modal
    public bool   $editModal      = false;
    public ?int   $editId         = null;
    public string $editPartNumber = '';
    public string $editPartName   = '';
    public string $editNoRack     = '';
    public int    $editLastStock  = 0;
    public string $editMaker      = '';
    public string $editMachine    = '';
    public string $editRepairStock = '';
    public string $editRepairRack  = '';
    public string $editStatus     = 'Y';
    public        $editPhoto      = null;

    // View toggle: grid or table
    public string $viewMode = 'table';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function with(SparePartService $sparePartService): array
    {
        return [
            'spareParts' => $sparePartService->getPaginatedSpareParts(24, $this->search),
        ];
    }

    public function openAdd(): void
    {
        $this->reset(['addPartNumber','addPartName','addNoRack','addLastStock','addMaker','addMachine','addGroup','addPhoto']);
        $this->addStatus = 'Y';
        $this->addModal  = true;
    }

    public function saveAdd(SparePartService $sparePartService): void
    {
        $this->validate([
            'addPartNumber' => 'required|string|max:100|unique:spare_parts,part_number',
            'addPhoto'      => 'nullable|image|max:4096',
        ]);

        $sparePartService->createSparePart([
            'part_number' => strtoupper(trim($this->addPartNumber)),
            'part_name'   => $this->addPartName,
            'no_rack'     => $this->addNoRack,
            'last_stock'  => $this->addLastStock,
            'maker'       => $this->addMaker,
            'machine'     => $this->addMachine,
            'group'       => $this->addGroup,
            'status'      => $this->addStatus,
        ], $this->addPhoto);

        $this->addModal = false;
        $this->success('Spare part created.');
    }

    public function openEdit(int $id, SparePartService $sparePartService): void
    {
        $sp = $sparePartService->getSparePartById($id);
        $this->editId          = $id;
        $this->editPartNumber  = $sp->part_number   ?? '';
        $this->editPartName    = $sp->part_name     ?? '';
        $this->editNoRack      = $sp->no_rack       ?? '';
        $this->editLastStock   = $sp->last_stock    ?? 0;
        $this->editMaker       = $sp->maker         ?? '';
        $this->editMachine     = $sp->machine       ?? '';
        $this->editRepairStock = (string)($sp->repair_stock ?? 0);
        $this->editRepairRack  = $sp->repair_rack   ?? '';
        $this->editStatus      = $sp->status        ?? 'Y';
        $this->editPhoto       = null;
        $this->editModal       = true;
    }

    public function saveEdit(SparePartService $sparePartService): void
    {
        $this->validate([
            'editPhoto' => 'nullable|image|max:4096',
        ]);

        $sparePartService->updateSparePart($this->editId, [
            'part_name'    => $this->editPartName,
            'no_rack'      => $this->editNoRack,
            'last_stock'   => $this->editLastStock,
            'maker'        => $this->editMaker,
            'machine'      => $this->editMachine,
            'repair_stock' => (int)$this->editRepairStock,
            'repair_rack'  => $this->editRepairRack,
            'status'       => $this->editStatus,
        ], $this->editPhoto);

        $this->editModal = false;
        $this->success('Spare part updated.');
    }

    public function deletePart(int $id, SparePartService $sparePartService): void
    {
        $sparePartService->deleteSparePart($id);
        $this->success('Spare part deleted.');
    }
};
?>

<div>
    <x-header title="Spare Part" separator progress-indicator>
        <x-slot:middle class="!justify-end gap-2">
            <x-input placeholder="Search name, ID, rack, maker..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
            <x-button icon="{{ $viewMode === 'table' ? 'o-squares-2x2' : 'o-list-bullet' }}"
                class="btn-ghost btn-sm"
                wire:click="$set('viewMode', '{{ $viewMode === 'table' ? 'grid' : 'table' }}')"
                tooltip="{{ $viewMode === 'table' ? 'Grid view' : 'Table view' }}" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button label="Add Spare Part" icon="o-plus" class="btn-primary" wire:click="openAdd" />
        </x-slot:actions>
    </x-header>

    {{-- TABLE VIEW --}}
    @if($viewMode === 'table')
    <x-card>
        <x-table
            :headers="[
                ['key' => 'part_number', 'label' => 'Part No'],
                ['key' => 'part_name',   'label' => 'Part Name'],
                ['key' => 'no_rack',     'label' => 'Rack'],
                ['key' => 'maker',       'label' => 'Maker'],
                ['key' => 'last_stock',  'label' => 'Stock'],
                ['key' => 'status',      'label' => 'Status'],
            ]"
            :rows="$spareParts"
            with-pagination
        >
            @scope('cell_status', $sp)
                @if($sp->status === 'N')
                    <x-badge label="Discontinued" class="badge-error badge-sm" />
                @else
                    <x-badge label="Active" class="badge-success badge-sm" />
                @endif
            @endscope

            @scope('cell_last_stock', $sp)
                <span class="{{ $sp->last_stock <= 0 ? 'text-error font-bold' : '' }}">{{ $sp->last_stock }}</span>
                @if($sp->repair_stock > 0)
                    <x-badge label="Repair: {{ $sp->repair_stock }}" class="badge-warning badge-sm ml-1" />
                @endif
            @endscope

            @scope('actions', $sp)
                <div class="flex gap-1">
                    <x-button icon="o-pencil-square" class="btn-ghost btn-xs" wire:click="openEdit({{ $sp->id }})" />
                    <x-button icon="o-trash" class="btn-ghost btn-xs text-error"
                        wire:click="deletePart({{ $sp->id }})"
                        wire:confirm="Delete spare part {{ $sp->part_number }}?" />
                </div>
            @endscope
        </x-table>
    </x-card>

    @else
    {{-- GRID / CARD VIEW --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
        @foreach($spareParts as $sp)
        <div class="card bg-base-100 shadow-sm hover:shadow-md transition-shadow">
            <div class="card-body p-3 text-center">
                @if($sp->part_photo)
                    <a href="{{ Storage::url($sp->part_photo) }}" target="_blank">
                        <img src="{{ Storage::url($sp->part_photo) }}" alt="{{ $sp->part_name }}"
                             class="w-full rounded mb-2 object-cover" style="max-height:80px">
                    </a>
                @else
                    <div class="w-full rounded mb-2 bg-base-200 flex items-center justify-center" style="height:60px">
                        <x-icon name="o-photo" class="w-6 h-6 opacity-20" />
                    </div>
                @endif

                <p class="text-xs font-bold line-clamp-2">{{ $sp->part_name ?: $sp->part_number }}</p>

                <div class="text-left text-xs mt-1 space-y-0.5">
                    <p><span class="font-semibold text-error">Rack:</span> {{ $sp->no_rack ?: '—' }}</p>
                    <p><span class="font-semibold">Maker:</span> {{ $sp->maker ?: '—' }}</p>
                    <p><span class="font-semibold text-success">Stock:</span> {{ $sp->last_stock }}</p>
                    @if($sp->status === 'N')
                        <p class="text-error font-bold italic text-center">Discontinued</p>
                    @endif
                    @if($sp->repair_stock > 0)
                        <x-badge label="Repair: {{ $sp->repair_stock }}" class="badge-warning badge-xs" />
                    @endif
                </div>

                <div class="flex gap-1 justify-center mt-2">
                    <x-button icon="o-pencil-square" class="btn-ghost btn-xs" wire:click="openEdit({{ $sp->id }})" />
                    <x-button icon="o-trash" class="btn-ghost btn-xs text-error"
                        wire:click="deletePart({{ $sp->id }})"
                        wire:confirm="Delete {{ $sp->part_number }}?" />
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="mt-4">{{ $spareParts->links() }}</div>
    @endif

    {{-- Add Modal --}}
    <x-modal wire:model="addModal" title="Add Spare Part" separator>
        <div class="grid grid-cols-2 gap-3">
            <x-input label="Part Number" wire:model="addPartNumber" class="col-span-2" />
            <x-input label="Part Name" wire:model="addPartName" class="col-span-2" />
            <x-input label="No Rack" wire:model="addNoRack" />
            <x-input label="Maker" wire:model="addMaker" />
            <x-input label="Last Stock" wire:model="addLastStock" type="number" />
            <x-input label="Machine" wire:model="addMachine" />
            <x-input label="Group" wire:model="addGroup" />
            <x-select label="Status" wire:model="addStatus"
                :options="[['id'=>'Y','name'=>'Active'],['id'=>'N','name'=>'Discontinued']]"
                option-value="id" option-label="name" />
            <div class="col-span-2">
                <label class="label text-sm font-semibold">Part Photo</label>
                <input type="file" wire:model="addPhoto" accept="image/*"
                       class="file-input file-input-bordered file-input-sm w-full" />
            </div>
        </div>
        <x-slot:actions>
            <x-button label="Cancel" class="btn-ghost" wire:click="$set('addModal',false)" />
            <x-button label="Submit" class="btn-primary" wire:click="saveAdd" spinner="saveAdd" />
        </x-slot:actions>
    </x-modal>

    {{-- Edit Modal --}}
    <x-modal wire:model="editModal" title="Edit Spare Part" separator>
        <div class="grid grid-cols-2 gap-3">
            <x-input label="Part Number" wire:model="editPartNumber" readonly class="col-span-2 opacity-60" />
            <x-input label="Part Name" wire:model="editPartName" class="col-span-2" />
            <x-input label="No Rack" wire:model="editNoRack" />
            <x-input label="Maker" wire:model="editMaker" />
            <x-input label="Last Stock" wire:model="editLastStock" type="number" />
            <x-input label="Machine" wire:model="editMachine" />
            <x-input label="Repair Stock" wire:model="editRepairStock" type="number" />
            <x-input label="Repair Rack" wire:model="editRepairRack" />
            <x-select label="Status" wire:model="editStatus"
                :options="[['id'=>'Y','name'=>'Active'],['id'=>'N','name'=>'Discontinued']]"
                option-value="id" option-label="name" />
            <div class="col-span-2">
                <label class="label text-sm font-semibold">Replace Photo (optional)</label>
                <input type="file" wire:model="editPhoto" accept="image/*"
                       class="file-input file-input-bordered file-input-sm w-full" />
            </div>
        </div>
        <x-slot:actions>
            <x-button label="Cancel" class="btn-ghost" wire:click="$set('editModal',false)" />
            <x-button label="Save Changes" class="btn-primary" wire:click="saveEdit" spinner="saveEdit" />
        </x-slot:actions>
    </x-modal>
</div>
