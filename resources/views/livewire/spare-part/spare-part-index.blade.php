<?php

use App\Services\SparePartService;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public string $search = '';

    // Machine Modal
    public bool $machineModal = false;
    public string $machineDetail = '';

    // Edit Repair Modal
    public bool $editModal = false;
    public ?int $editId = null;
    public int $editQty = 0;
    public string $editRack = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function openMachineModal(string $machine): void
    {
        $this->machineDetail = $machine;
        $this->machineModal = true;
    }

    public function openEditRepair(int $id, int $qty, string $rack): void
    {
        $this->editId = $id;
        $this->editQty = $qty;
        $this->editRack = $rack;
        $this->editModal = true;
    }

    public function saveEditRepair(SparePartService $sparePartService): void
    {
        $this->validate([
            'editQty' => 'required|integer|min:0',
            'editRack' => 'nullable|string|max:50',
        ]);

        $sparePartService->updateSparePart($this->editId, [
            'repair_stock' => $this->editQty,
            'repair_rack' => $this->editRack,
        ]);

        $this->editModal = false;
        // Optionally add a toast success message if you use Mary\Traits\Toast
        // $this->success('Repair data updated.');
    }

    public function with(SparePartService $sparePartService): array
    {
        return [
            'spareParts' => $sparePartService->getPaginatedSpareParts(12, $this->search),
        ];
    }
};
?>

<div>
    <x-header title="Spare Part Center" subtitle="List Spare Part" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Name, ID, Maker..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
    </x-header>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        @forelse($spareParts as $part)
            <x-card class="shadow-sm flex flex-col h-full" body-class="p-4 flex flex-col flex-1">
                <x-slot:figure>
                    @if($part->part_photo)
                        <img src="{{ asset('storage/' . $part->part_photo) }}" alt="{{ $part->part_name }}" class="h-40 w-full object-cover" />
                    @else
                        <div class="h-40 w-full bg-base-200 flex items-center justify-center text-base-content/50">
                            <x-icon name="o-photo" class="w-10 h-10" />
                        </div>
                    @endif
                </x-slot:figure>
                
                <div class="text-center font-bold mb-3 line-clamp-2 min-h-[3rem]">
                    {{ $part->part_name }}
                </div>
                
                <div class="text-sm space-y-2 mt-auto">
                    <div class="flex justify-between border-b border-base-200 pb-1 items-center">
                        <span class="font-semibold text-error">Rack:</span>
                        <a href="{{ route('spare-parts.print-label', $part->id) }}" target="_blank" class="text-primary hover:underline font-bold" title="Print Label">
                            {{ $part->no_rack ?: '-' }}
                        </a>
                    </div>
                    <div class="flex justify-between border-b border-base-200 pb-1">
                        <span class="font-semibold">Maker:</span>
                        <span class="text-right truncate ml-2" title="{{ $part->maker }}">{{ $part->maker ?: '-' }}</span>
                    </div>
                    <div class="flex justify-between border-b border-base-200 pb-1">
                        <span class="font-semibold">Machine:</span>
                        <span wire:click="openMachineModal('{{ addslashes($part->machine) }}')" class="text-primary cursor-pointer hover:underline text-right truncate ml-2" title="{{ $part->machine }}">{{ $part->machine ?: '-' }}</span>
                    </div>
                    <div class="flex justify-between border-b border-base-200 pb-1">
                        <span class="font-semibold text-success">Stock:</span>
                        <span>{{ $part->last_stock ?? 0 }}</span>
                    </div>
                    
                    @if($part->status === 'N')
                        <div class="text-center text-error italic font-bold mt-2">
                            Discontinued
                        </div>
                    @endif

                    @if($part->repair_stock > 0)
                        <div class="mt-3 flex justify-between items-center bg-warning/20 p-2 rounded">
                            <span class="text-warning-content font-semibold">Repair: {{ $part->repair_stock }}</span>
                            <x-button size="btn-xs" class="btn-primary" wire:click="openEditRepair({{ $part->id }}, {{ $part->repair_stock }}, '{{ addslashes($part->repair_rack) }}')">
                                {{ $part->repair_rack ?: 'Edit' }}
                            </x-button>
                        </div>
                    @endif
                </div>
            </x-card>
        @empty
            <div class="col-span-full">
                <x-alert icon="o-exclamation-triangle" class="alert-warning">
                    No spare parts found.
                </x-alert>
            </div>
        @endforelse
    </div>
    
    <div class="mt-6">
        {{ $spareParts->links() }}
    </div>

    {{-- Modal Machine --}}
    <x-modal wire:model="machineModal" title="Machine Information">
        <x-alert icon="o-information-circle" class="alert-info mb-4">
            Note: Detailed machine mapping (t_sp_machine) is not available in the current database. Showing raw machine field data.
        </x-alert>
        
        <div class="p-4 bg-base-200 rounded-lg text-lg font-semibold text-center break-words">
            {{ $this->machineDetail ?: 'No machine specified.' }}
        </div>

        <x-slot:actions>
            <x-button label="Close" wire:click="$set('machineModal', false)" />
        </x-slot:actions>
    </x-modal>

    {{-- Modal Edit Repair --}}
    <x-modal wire:model="editModal" title="Edit Repair Data">
        <div class="grid gap-4">
            <x-input label="Rack Repair" wire:model="editRack" />
            <x-input label="QTY Repair" type="number" wire:model="editQty" />
        </div>
        
        <x-slot:actions>
            <x-button label="Cancel" class="btn-ghost" wire:click="$set('editModal', false)" />
            <x-button label="Submit" class="btn-success text-white" wire:click="saveEditRepair" spinner="saveEditRepair" />
        </x-slot:actions>
    </x-modal>
</div>
