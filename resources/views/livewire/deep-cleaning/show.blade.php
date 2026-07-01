<?php

use App\Models\DeepCleaning;
use App\Models\DeepCleaningItem;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;
use Illuminate\Support\Facades\Storage;
use App\Livewire\Traits\WithSpareparts;

new class extends Component {
    use WithFileUploads, Toast, WithSpareparts;

    public DeepCleaning $record;
    public string $selectedTab = 'findings-tab';

    // Item modal state
    public bool $itemModal = false;
    public bool $deleteItemModal = false;
    public bool $viewItemModal = false;
    public ?int $editingItemId = null;
    public ?int $itemToDelete = null;
    public ?int $viewingItemId = null;

    // Item Form
    public string $itemcheck = '';
    public string $status = 'Undone';
    public string $description = ''; // Problem
    public string $action = '';
    public $before_photo;
    public $after_photo;
    
    // Existing photos (for edit view preview)
    public ?string $existing_before = null;
    public ?string $existing_after = null;

    // Autocomplete list
    public array $itemcheckOptions = [];
    public array $descriptionOptions = [];
    public array $actionOptions = [];

    // Sparepart Modal State
    public bool $spModal = false;
    public bool $deleteSpModal = false;
    public ?int $editingSpId = null;
    public ?int $spToDelete = null;

    // Sparepart Form
    public $sp_id = '';
    public ?int $sp_qty = 1;
    public string $sp_itemcheck = '';

    public function mount(int $id)
    {
        $this->record = DeepCleaning::with(['items', 'spareparts'])->findOrFail($id);
        $this->loadItemcheckOptions();
    }

    public function loadItemcheckOptions()
    {
        $items = DeepCleaningItem::whereHas('deepCleaning', function($q) {
                $q->where('MachineName', $this->record->MachineName);
            })
            ->get();
            
        $this->itemcheckOptions = $items->pluck('itemcheck')->filter()->unique()->values()->toArray();
        $this->descriptionOptions = $items->pluck('description')->filter()->unique()->values()->toArray();
        $this->actionOptions = $items->pluck('action')->filter()->unique()->values()->toArray();
    }

    // --- FINDING ITEM LOGIC ---

    public function openAddItem()
    {
        $this->reset(['editingItemId', 'itemcheck', 'status', 'description', 'action', 'before_photo', 'after_photo', 'existing_before', 'existing_after']);
        $this->status = 'Undone';
        $this->itemModal = true;
    }

    public function openViewItem(int $itemId)
    {
        $this->viewingItemId = $itemId;
        $this->viewItemModal = true;
    }

    public function openEditItem(int $itemId)
    {
        $item = $this->record->items->firstWhere('id', $itemId);
        if (!$item) return;

        $this->editingItemId = $itemId;
        $this->itemcheck = $item->itemcheck ?? '';
        $this->status = $item->status ?? 'Undone';
        $this->description = $item->description ?? '';
        $this->action = $item->action ?? '';
        
        $this->existing_before = $item->before_photo;
        $this->existing_after = $item->after_photo;
        
        $this->before_photo = null;
        $this->after_photo = null;

        $this->itemModal = true;
    }

    public function saveItem()
    {
        $this->validate([
            'itemcheck' => 'required|string|max:255',
            'status' => 'required|string',
            'description' => 'required|string',
            'action' => 'required|string',
            'before_photo' => 'nullable|image|max:4096',
            'after_photo' => 'nullable|image|max:4096',
        ]);

        $data = [
            'itemcheck' => $this->itemcheck,
            'status' => $this->status,
            'description' => $this->description,
            'action' => $this->action,
        ];

        if ($this->before_photo) {
            $data['before_photo'] = $this->before_photo->store('deep_cleaning_photos', 'public');
        }

        if ($this->after_photo) {
            $data['after_photo'] = $this->after_photo->store('deep_cleaning_photos', 'public');
        }

        if ($this->editingItemId) {
            $item = DeepCleaningItem::find($this->editingItemId);
            if ($this->before_photo && $item->before_photo) {
                Storage::disk('public')->delete($item->before_photo);
            }
            if ($this->after_photo && $item->after_photo) {
                Storage::disk('public')->delete($item->after_photo);
            }
            $item->update($data);
            $this->success('Finding updated successfully.');
        } else {
            $this->record->items()->create($data);
            $this->success('Finding added successfully.');
            
            if (!in_array($this->itemcheck, $this->itemcheckOptions)) {
                $this->itemcheckOptions[] = $this->itemcheck;
            }
            if (!in_array($this->description, $this->descriptionOptions)) {
                $this->descriptionOptions[] = $this->description;
            }
            if (!in_array($this->action, $this->actionOptions)) {
                $this->actionOptions[] = $this->action;
            }
        }

        $this->record->load('items');
        $this->itemModal = false;
    }

    public function confirmDeleteItem(int $itemId)
    {
        $this->itemToDelete = $itemId;
        $this->deleteItemModal = true;
    }

    public function deleteItem()
    {
        if ($this->itemToDelete) {
            $item = DeepCleaningItem::find($this->itemToDelete);
            if ($item) {
                if ($item->before_photo) Storage::disk('public')->delete($item->before_photo);
                if ($item->after_photo) Storage::disk('public')->delete($item->after_photo);
                $item->delete();
            }
            $this->record->load('items');
            $this->success('Finding deleted.');
        }
        $this->deleteItemModal = false;
        $this->itemToDelete = null;
    }

    // --- SPAREPART LOGIC ---

    public function openAddSp()
    {
        $this->reset(['editingSpId', 'sp_id', 'sp_qty', 'sp_itemcheck']);
        $this->sp_qty = 1;
        $this->spModal = true;
    }

    public function openEditSp(int $spId)
    {
        $sp = $this->record->spareparts->firstWhere('id', $spId);
        if (!$sp) return;

        $this->editingSpId = $spId;
        $this->sp_id = $sp->sparepart_id;
        $this->sp_qty = $sp->qty;
        $this->sp_itemcheck = $sp->itemcheck ?? '';
        
        $this->spModal = true;
    }

    public function saveSp()
    {
        \Log::info("saveSp called", ['sp_id' => $this->sp_id, 'sp_qty' => $this->sp_qty, 'sp_itemcheck' => $this->sp_itemcheck]);
        
        $spIdValue = is_array($this->sp_id) ? ($this->sp_id[0] ?? '') : $this->sp_id;

        $this->validate([
            'sp_qty' => 'required|integer|min:1',
            'sp_itemcheck' => 'nullable|string|max:255',
        ]);

        if (empty($spIdValue)) {
            $this->addError('sp_id', 'The sparepart field is required.');
            return;
        }

        $data = [
            'sparepart_id' => $spIdValue,
            'qty' => $this->sp_qty,
            'itemcheck' => $this->sp_itemcheck,
        ];

        if ($this->editingSpId) {
            $this->record->spareparts()->where('id', $this->editingSpId)->update($data);
            $this->success('Sparepart updated.');
        } else {
            $this->record->spareparts()->create($data);
            $this->success('Sparepart added.');
        }

        $this->record->load('spareparts');
        $this->spModal = false;
    }

    public function confirmDeleteSp(int $spId)
    {
        $this->spToDelete = $spId;
        $this->deleteSpModal = true;
    }

    public function deleteSp()
    {
        if ($this->spToDelete) {
            $this->record->spareparts()->where('id', $this->spToDelete)->delete();
            $this->record->load('spareparts');
            $this->success('Sparepart deleted.');
        }
        $this->deleteSpModal = false;
        $this->spToDelete = null;
    }
};
?>

<div>
    <x-header separator class="!mb-4">
        <x-slot:title>
            <div class="flex items-center gap-3">
                <x-button icon="o-arrow-left" class="btn-circle btn-ghost btn-sm" link="{{ route('deep-cleaning.index') }}" wire:navigate />
                <span>Deep Cleaning Details</span>
            </div>
        </x-slot:title>
    </x-header>

    <div class="grid grid-cols-1 gap-4">
        <!-- General Info -->
        @include('livewire.deep-cleaning.partials.show-general-info')

        <x-tabs wire:model="selectedTab" 
            label-div-class="w-full flex overflow-x-auto border-b border-base-content/10 mb-4"
            active-class="bg-neutral text-neutral-content"
            label-class="flex-1 text-center font-bold py-3 [&:not(.tab-active)]:hover:bg-base-300 transition-all cursor-pointer text-base-content/70">
            <!-- TAB 1: FINDINGS -->
            <x-tab name="findings-tab" label="Findings (Before & After)" icon="o-document-text">
                @include('livewire.deep-cleaning.partials.show-tab-findings')
            </x-tab>

            <!-- TAB 2: SPAREPARTS -->
            <x-tab name="spareparts-tab" label="Spareparts Used" icon="o-wrench">
                @include('livewire.deep-cleaning.partials.show-tab-spareparts')
            </x-tab>
        </x-tabs>
    </div>

    <!-- Add / Edit Item Modal -->
    @include('livewire.deep-cleaning.partials.show-item-modal')

    <!-- View Item Modal -->
    @include('livewire.deep-cleaning.partials.show-view-item-modal')

    <!-- Delete Modal -->
    @include('livewire.deep-cleaning.partials.show-delete-item-modal')

    <!-- Add / Edit Sparepart Modal -->
    @include('livewire.deep-cleaning.partials.show-sp-modal')

    <!-- Delete Sparepart Modal -->
    @include('livewire.deep-cleaning.partials.show-delete-sp-modal')
</div>
