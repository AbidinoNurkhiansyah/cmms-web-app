{{-- Edit Modal --}}
<x-modal wire:model="editModal" title="Process Work Order" subtitle="Edit & Confirmation" separator class="backdrop-blur-sm">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        {{-- Readonly request info --}}
        <x-input label="Order Date" type="date" wire:model="editForm.date" readonly />
        <x-input label="Target Date" type="date" wire:model="editForm.target_date" readonly />
        <x-input label="Order Type" wire:model="editForm.order_type" readonly />
        
        <x-input label="Requester" wire:model="editForm.requester" readonly />
        <x-input label="Department" wire:model="editForm.department" readonly />
        <x-input label="Priority" wire:model="editForm.priority" readonly />
        
        <x-input label="Line Name" wire:model="editForm.line_name" readonly />
        <x-input label="Machine Name" wire:model="editForm.machine_name" readonly />
        <x-input label="Machine No" wire:model="editForm.machine_no" readonly />
        
        <div class="col-span-full">
            <x-textarea label="Problem Description" wire:model="editForm.problem" rows="2" readonly />
        </div>
        
        @if($editForm->existing_foto_req)
        <div class="col-span-full">
            <label class="block text-sm font-medium mb-1">Request Photo</label>
            <img src="{{ Storage::url($editForm->existing_foto_req) }}" alt="Request Photo" class="max-h-40 rounded-lg shadow-sm border border-base-200">
        </div>
        @endif

        <hr class="col-span-full my-2 border-base-300" />
        
        {{-- Processing info --}}
        <div class="col-span-full">
            <x-textarea label="Confirmation Note (Action Taken)" wire:model="editForm.confirmation_note" rows="3" />
        </div>

        <div class="col-span-full grid grid-cols-2 gap-4">
            <div>
                <x-file wire:model="editForm.foto_confirm1" label="Photo Confirmation 1" accept="image/*" />
                @if($editForm->existing_foto_confirm1)
                    <img src="{{ Storage::url($editForm->existing_foto_confirm1) }}" class="mt-2 max-h-32 rounded-lg" />
                @endif
            </div>
            <div>
                <x-file wire:model="editForm.foto_confirm2" label="Photo Confirmation 2" accept="image/*" />
                @if($editForm->existing_foto_confirm2)
                    <img src="{{ Storage::url($editForm->existing_foto_confirm2) }}" class="mt-2 max-h-32 rounded-lg" />
                @endif
            </div>
        </div>

        <x-select label="Status" wire:model="editForm.status"
            :options="[['id'=>'Open','name'=>'Open'],['id'=>'In Progress','name'=>'In Progress'],['id'=>'Done','name'=>'Done']]"
            option-value="id" option-label="name" />
            
        <x-input label="Actual Complete Date" type="date" wire:model="editForm.actual_date" />
        
        <x-select label="Team" wire:model="editForm.pic"
            :options="[['id'=>'Repair','name'=>'Repair'],['id'=>'TPM','name'=>'TPM'],['id'=>'OH','name'=>'OH']]"
            option-value="id" option-label="name" placeholder="Select Team" />
            
        <x-input label="PIC 1" wire:model="editForm.pic1" placeholder="Engineer 1" />
        <x-input label="PIC 2" wire:model="editForm.pic2" placeholder="Engineer 2" />
        <x-input label="PIC 3" wire:model="editForm.pic3" placeholder="Engineer 3" />
        
    </div>
    <x-slot:actions>
        <x-button label="Cancel" class="btn-ghost" wire:click="$set('editModal',false)" />
        <x-button label="Save Process" class="btn-primary" wire:click="saveEdit" spinner="saveEdit" />
    </x-slot:actions>
</x-modal>
