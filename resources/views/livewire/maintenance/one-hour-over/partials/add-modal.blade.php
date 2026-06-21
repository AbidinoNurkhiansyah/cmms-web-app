<x-modal wire:model="addModal" title="New One Hour Over Record" separator>
    <div class="grid grid-cols-2 gap-3">
        <x-input label="Date" type="date" wire:model="date" />
        <x-input label="Group Name" wire:model="group_name" />
        
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
        <div class="col-span-2">
            <x-textarea label="Problem" wire:model="problem" rows="3" />
        </div>
            
        <div class="col-span-2 grid grid-cols-2 gap-3 mt-2 border-t pt-2">
            <div>
                <label class="label text-sm font-semibold">File RSA</label>
                <input type="file" wire:model="file_rsa" class="file-input file-input-bordered file-input-sm w-full" />
            </div>
            <div>
                <label class="label text-sm font-semibold">File RCA</label>
                <input type="file" wire:model="file_rca" class="file-input file-input-bordered file-input-sm w-full" />
            </div>
        </div>
    </div>
    <x-slot:actions>
        <x-button label="Cancel" class="btn-ghost" wire:click="$set('addModal',false)" />
        <x-button label="Submit" class="btn-primary" wire:click="saveAdd" spinner="saveAdd" />
    </x-slot:actions>
</x-modal>
