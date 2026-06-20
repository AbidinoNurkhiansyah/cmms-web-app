<x-modal wire:model="editModal" title="Edit Record" separator>
    <div class="grid grid-cols-2 gap-3">
        <x-input label="Date" type="date" wire:model="date" />
        <x-input label="Group Name" wire:model="group_name" />
        
        <div class="col-span-2 grid grid-cols-2 gap-3">
            <x-choices-offline 
                label="Line Name" 
                wire:model.live="LineName" 
                :options="$this->lineNames" 
                option-value="name" 
                option-label="name" 
                searchable 
                single />
            <x-choices-offline 
                label="Machine" 
                wire:model="MachineName" 
                :options="$this->machines" 
                option-value="machine_name" 
                option-label="machine_name" 
                searchable 
                single />
        </div>

        <x-textarea label="Problem" wire:model="problem" class="col-span-2" rows="3" />
        <x-select label="Status" wire:model="status" class="col-span-2"
            :options="[['id'=>'Open','name'=>'Open'],['id'=>'Closed','name'=>'Closed']]"
            option-value="id" option-label="name" />
            
        <div class="col-span-2 grid grid-cols-2 gap-3 mt-2 border-t pt-2">
            <div>
                <label class="label text-sm font-semibold">Replace File RSA</label>
                <input type="file" wire:model="file_rsa" class="file-input file-input-bordered file-input-sm w-full" />
            </div>
            <div>
                <label class="label text-sm font-semibold">Replace File RCA</label>
                <input type="file" wire:model="file_rca" class="file-input file-input-bordered file-input-sm w-full" />
            </div>
        </div>
    </div>
    <x-slot:actions>
        <x-button label="Cancel" class="btn-ghost" wire:click="$set('editModal',false)" />
        <x-button label="Save Changes" class="btn-primary" wire:click="saveEdit" spinner="saveEdit" />
    </x-slot:actions>
</x-modal>
