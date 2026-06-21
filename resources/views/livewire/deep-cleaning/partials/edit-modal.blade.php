<x-modal wire:model="editModal" title="Edit Deep Cleaning Record" separator>
    <div class="grid grid-cols-2 gap-3">
        <x-input label="Date" type="date" wire:model="Date" />
        <x-select label="Status" wire:model="status"
            :options="[['id'=>'Scheduled','name'=>'Scheduled'],['id'=>'In Progress','name'=>'In Progress'],['id'=>'Done','name'=>'Done']]"
            option-value="id" option-label="name" />
        
        <div class="col-span-2 grid grid-cols-2 gap-3">
            <x-choices 
                label="Line Name" 
                wire:model.live="LineName" 
                :options="$lineNames" 
                option-value="name" 
                option-label="name" 
                search-function="searchLine" 
                debounce="300ms" 
                searchable
                single />
            <x-choices 
                label="Machine" 
                wire:model="MachineName" 
                :options="$machines" 
                option-value="machine_name" 
                option-label="machine_name" 
                search-function="searchMachine" 
                debounce="300ms" 
                :disabled="empty($LineName)"
                searchable
                single />
        </div>

        <div class="col-span-2 border-t pt-3 mt-1">
            <div class="flex justify-between items-center mb-2">
                <span class="text-sm font-semibold">PICs</span>
                <x-button icon="o-plus" class="btn-xs btn-outline btn-primary" wire:click="addPic">Add PIC</x-button>
            </div>
            <div class="grid grid-cols-2 gap-2">
                @foreach($pics as $index => $pic)
                    <div class="flex items-center gap-1">
                        <x-input wire:model="pics.{{ $index }}" placeholder="PIC Name" class="flex-1" />
                        @if(count($pics) > 1)
                            <x-button icon="o-trash" class="btn-sm btn-ghost text-error" wire:click="removePic({{ $index }})" />
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <div class="col-span-2 border-t pt-3 mt-1 grid grid-cols-2 gap-3">
            <x-input label="Item Check" wire:model="itemcheck" />
            <x-input label="Action" wire:model="action" />
            
            <x-select label="Description (Type)" wire:model="description" class="col-span-2"
                :options="[['id'=>'TPM','name'=>'TPM'],['id'=>'Preventive','name'=>'Preventive'],['id'=>'Repair','name'=>'Repair']]"
                option-value="id" option-label="name" placeholder="Select Type" />
            
            <x-input label="Sparepart ID/Name" wire:model="sparepart_id" />
            <x-input label="Sparepart Qty" type="number" wire:model="sparepart_qty" />
        </div>

        <div class="col-span-2 grid grid-cols-2 gap-3 border-t pt-3 mt-1">
            <div>
                <label class="label text-sm font-semibold">Photo Before</label>
                <input type="file" wire:model="before_photo" class="file-input file-input-bordered file-input-sm w-full" accept="image/*" />
            </div>
            <div>
                <label class="label text-sm font-semibold">Photo After</label>
                <input type="file" wire:model="after_photo" class="file-input file-input-bordered file-input-sm w-full" accept="image/*" />
            </div>
        </div>
    </div>
    <x-slot:actions>
        <x-button label="Cancel" class="btn-ghost" wire:click="$set('editModal',false)" />
        <x-button label="Save Changes" class="btn-primary" wire:click="saveEdit" spinner="saveEdit" />
    </x-slot:actions>
</x-modal>
