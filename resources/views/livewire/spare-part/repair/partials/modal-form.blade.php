<x-modal wire:model="isModalOpen" title="{{ $isEditMode ? 'Edit Repair Sparepart' : 'New Repair Sparepart' }}" class="backdrop-blur" box-class="w-11/12 max-w-3xl">
    
    <div class="flex flex-col gap-3">
        
        <!-- Row 1: Date, Part Name, Qty -->
        <div class="flex flex-col md:flex-row gap-3">
            <div class="w-full md:w-48">
                <x-input label="Date" type="date" wire:model="date" />
            </div>
            
            <div class="w-full md:flex-1">
                <x-choices 
                    label="Part Name" 
                    wire:model="spare_part_id" 
                    :options="$spareparts" 
                    option-label="part_name" 
                    search-function="searchSparepart"
                    no-result-text="Nothing found"
                    single
                    searchable
                />
            </div>
            
            <div class="w-full md:w-28">
                <x-input label="Qty" type="number" wire:model="qty" min="1" />
            </div>
        </div>
        
        <!-- Row 2: Rack and Part Usage (Edit Mode Only) -->
        @if($isEditMode)
        <div class="flex flex-col md:flex-row gap-3">
            <div class="w-full md:flex-1">
                <x-choices 
                    label="Rack" 
                    wire:model.live="rack" 
                    :options="$this->rackOptions" 
                    option-label="name" 
                    single 
                    searchable 
                />
            </div>
            
            <div class="w-full md:flex-1">
                <x-choices 
                    label="Part Usage" 
                    wire:model="part_usage" 
                    :options="$this->partUsageOptions" 
                    option-label="name" 
                    single 
                    searchable 
                />
            </div>
        </div>
        @endif
        
        <!-- Row 3: Item Repair and Review -->
        <div class="flex flex-col md:flex-row gap-3">
            <div class="w-full md:flex-1">
                <x-textarea label="Item Repair" wire:model="item_repair" rows="1" maxlength="255" />
            </div>
            
            <div class="w-full md:flex-1">
                <x-textarea label="Review & Countermeasures" wire:model="review" rows="1" maxlength="255" />
            </div>
        </div>
        
        <!-- Row 4: PICs -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <x-choices label="PIC 1" wire:model="pic1_id" :options="$this->users" option-label="name" single searchable />
            <x-choices label="PIC 2" wire:model="pic2_id" :options="$this->users" option-label="name" single searchable />
            <x-choices label="PIC 3" wire:model="pic3_id" :options="$this->users" option-label="name" single searchable />
        </div>
        
        <!-- Row 5: Images -->
        <div class="grid grid-cols-1 {{ $isEditMode ? 'md:grid-cols-2' : '' }} gap-4">
            <!-- Image Before -->
            <div class="flex items-start gap-4">
                <div class="flex-1">
                    <x-file label="Upload Picture (Before)" wire:model="file_before_upload" accept="image/jpeg, image/png, image/webp" />
                    
                    <div class="mt-2 text-sm text-primary" wire:loading wire:target="file_before_upload">
                        <span class="loading loading-spinner loading-xs"></span> Uploading...
                    </div>
                </div>
                
                @if($file_before_upload)
                    <div class="w-40 shrink-0">
                        <img src="{{ $file_before_upload->temporaryUrl() }}" class="h-28 w-full object-cover rounded-lg shadow-sm border border-base-300" />
                    </div>
                @elseif($existing_file_before)
                    <div class="w-40 shrink-0">
                        <img src="{{ Storage::url($existing_file_before) }}" class="h-28 w-full object-cover rounded-lg shadow-sm border border-base-300" />
                    </div>
                @endif
            </div>
            
            <!-- Image After -->
            @if($isEditMode)
            <div class="flex items-start gap-4">
                <div class="flex-1">
                    <x-file label="Upload Picture (After)" wire:model="file_after_upload" accept="image/jpeg, image/png, image/webp" />
                    
                    <div class="mt-2 text-sm text-primary" wire:loading wire:target="file_after_upload">
                        <span class="loading loading-spinner loading-xs"></span> Uploading...
                    </div>
                </div>
                
                @if($file_after_upload)
                    <div class="w-40 shrink-0">
                        <img src="{{ $file_after_upload->temporaryUrl() }}" class="h-28 w-full object-cover rounded-lg shadow-sm border border-base-300" />
                    </div>
                @elseif($existing_file_after)
                    <div class="w-40 shrink-0">
                        <img src="{{ Storage::url($existing_file_after) }}" class="h-28 w-full object-cover rounded-lg shadow-sm border border-base-300" />
                    </div>
                @endif
            </div>
            @endif
        </div>
        
    </div>

    <x-slot:actions>
        <x-button label="Cancel" @click="$wire.isModalOpen = false" class="btn-ghost" />
        <x-button label="{{ $isEditMode ? 'Update' : 'Save' }}" wire:click="saveRepair" class="btn-primary" spinner="saveRepair" />
    </x-slot:actions>
</x-modal>
