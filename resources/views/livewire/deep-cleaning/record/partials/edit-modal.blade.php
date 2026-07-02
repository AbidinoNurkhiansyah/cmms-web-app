<x-modal wire:model="editModal" title="Edit Deep Cleaning Record" separator>
    <div class="grid grid-cols-2 gap-3">
        <x-input label="Date" type="date" wire:model="Date" />
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

        <div class="col-span-2">
            <x-choices 
                label="Machine" 
                wire:model.live="MachineName" 
                :options="$machines" 
                option-value="machine_name" 
                option-label="machine_name" 
                search-function="searchMachine" 
                debounce="300ms" 
                :disabled="empty($LineName)"
                searchable
                single />
        </div>

        <x-input label="Asset No" wire:model="MachineNo" readonly placeholder="Auto-filled" class="bg-base-200" />
        <x-select 
            label="Description" 
            wire:model="description" 
            :options="[['id'=>'TPM','name'=>'TPM'], ['id'=>'Preventive','name'=>'Preventive'], ['id'=>'Repair','name'=>'Repair']]" 
            placeholder="Select description..." 
        />

        <div class="col-span-2 border-t pt-3 mt-1">
            <div class="flex justify-between items-center mb-2">
                <span class="text-sm font-semibold">PICs</span>
                <x-button icon="o-plus" class="btn-xs btn-outline btn-primary" wire:click="addPic">Add PIC</x-button>
            </div>
            <div class="grid grid-cols-2 gap-2">
                @foreach($pics as $index => $pic)
                    <div class="flex items-center gap-1">
                        <div class="flex-1">
                            <x-choices 
                                wire:model="pics.{{ $index }}" 
                                :options="$users" 
                                option-value="name" 
                                option-label="name" 
                                search-function="searchUser" 
                                searchable
                                single
                                placeholder="Search Personnel..." />
                        </div>
                        @if(count($pics) > 1)
                            <x-button icon="o-trash" class="btn-sm btn-ghost text-error" wire:click="removePic({{ $index }})" />
                        @endif
                    </div>
                @endforeach
            </div>
        </div>


    </div>
    <x-slot:actions>
        <x-button label="Cancel" class="btn-ghost" wire:click="$set('editModal',false)" />
        <x-button label="Save Changes" class="btn-primary" wire:click="saveEdit" spinner="saveEdit" />
    </x-slot:actions>
</x-modal>
