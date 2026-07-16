{{-- Add Modal --}}
<x-modal wire:model="addModal" title="New Andon Call" box-class="w-11/12 max-w-4xl" separator>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <x-input label="Date Shift" type="date" wire:model="form.date_shift" id="add_date_shift" />
        <x-select label="Shift" wire:model="form.shift"
            :options="[['id'=>'1','name'=>'Shift 1'],['id'=>'2','name'=>'Shift 2'],['id'=>'3','name'=>'Shift 3']]"
            option-value="id" option-label="name" id="add_shift" />
        <x-choices label="PIC Name" wire:model="form.name_pic" :options="$users" option-label="name" option-value="name" search-function="searchUser" single searchable id="add_name_pic" />

        <x-input label="Date In" type="date" wire:model="form.date_in" id="add_date_in" />
        <x-input label="Time In" type="time" wire:model="form.time_in" readonly class="bg-base-200 cursor-not-allowed" id="add_time_in" />
        <x-input label="Status" wire:model="form.status" readonly class="bg-base-200 cursor-not-allowed" id="add_status" />

        <x-choices label="Line Name" wire:model.live="LineName" :options="$lineNames" option-label="name" option-value="name" search-function="searchLine" single searchable id="add_line_name" class="md:col-span-1" />
        <x-choices label="Machine Name" wire:model="MachineName" :options="$machines" option-label="machine_name" option-value="machine_name" search-function="searchMachine" single searchable id="add_machine" class="md:col-span-2" />
        
        <x-textarea label="Stop Info" wire:model="form.stop_info" class="md:col-span-3" rows="2" id="add_stop_info" />
    </div>
    <x-slot:actions>
        <x-button label="Cancel" class="btn-ghost" wire:click="$set('addModal',false)" />
        <x-button label="Submit" class="btn-primary" wire:click="saveAdd" spinner="saveAdd" />
    </x-slot:actions>
</x-modal>

{{-- Edit Modal --}}
<x-modal wire:model="editModal" title="Edit / Close Andon Call" box-class="w-11/12 max-w-6xl" separator>
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
        
        {{-- Left Column: Call Details --}}
        <div class="lg:col-span-3">
            <div class="text-sm font-bold border-b pb-1 mb-4 text-primary">Call Details</div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <x-input label="Date Shift" type="date" wire:model="form.date_shift" id="edit_date_shift" />
                <x-select label="Shift" wire:model="form.shift"
                    :options="[['id'=>'1','name'=>'Shift 1'],['id'=>'2','name'=>'Shift 2'],['id'=>'3','name'=>'Shift 3']]"
                    option-value="id" option-label="name" id="edit_shift" />
                
                <x-input label="Date In" type="date" wire:model="form.date_in" id="edit_date_in" />
                <x-input label="Time In" type="time" wire:model="form.time_in" id="edit_time_in" />
        
                <x-choices label="Line Name" wire:model.live="LineName" :options="$lineNames" option-label="name" option-value="name" search-function="searchLine" single searchable id="edit_line_name" />
                <x-choices label="Machine Name" wire:model="MachineName" :options="$machines" option-label="machine_name" option-value="machine_name" search-function="searchMachine" single searchable id="edit_machine" />
                
                <x-choices label="PIC Name" wire:model="form.name_pic" :options="$users" option-label="name" option-value="name" search-function="searchUser" single searchable id="edit_name_pic" />
                <x-select label="Status" wire:model="form.status"
                    :options="[['id'=>'CALL','name'=>'CALL'],['id'=>'REPAIR','name'=>'REPAIR'],['id'=>'DONE','name'=>'DONE']]"
                    option-value="id" option-label="name" id="edit_status" />
        
                <x-textarea label="Stop Info" wire:model="form.stop_info" class="md:col-span-3" rows="2" id="edit_stop_info" />
            </div>
        </div>

        {{-- Right Column: Repair Actions --}}
        <div class="lg:col-span-2">
            <div class="text-sm font-bold border-b pb-1 mb-4 text-secondary">Repair Actions</div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <x-input label="Finish Time" type="time" wire:model="form.finish_time" id="edit_finish_time" />
                
                <div class="flex items-center gap-4 mt-1 md:mt-8">
                    <x-checkbox label="Mechanic" wire:model="form.mechanic" id="edit_mechanic" />
                    <x-checkbox label="Electric" wire:model="form.electric" id="edit_electric" />
                </div>
                
                <x-textarea label="Actual Cause" wire:model="form.cause_actual" class="md:col-span-2" rows="2" id="edit_cause_actual" />
                
                <x-textarea label="Preventive" wire:model="form.preventive" class="md:col-span-1" rows="3" id="edit_preventive" />
                <x-textarea label="Hasil Repair" wire:model="form.hasil_repair" class="md:col-span-1" rows="3" id="edit_hasil_repair" />
            </div>
        </div>
        
    </div>
    <x-slot:actions>
        <x-button label="Cancel" class="btn-ghost" wire:click="$set('editModal',false)" />
        <x-button label="Save Changes" class="btn-primary" wire:click="saveEdit" spinner="saveEdit" />
    </x-slot:actions>
</x-modal>

{{-- Delete Confirmation Modal --}}
<x-modal wire:model="deleteModal" title="Confirm Deletion" box-class="max-w-md">
    <div class="py-4">
        <div class="flex items-start gap-4 mb-4">
            <x-icon name="o-exclamation-triangle" class="w-10 h-10 text-error flex-shrink-0" />
            <div>
                <h3 class="font-bold text-lg">Are you sure?</h3>
                <p class="text-base-content/70 mt-1">This action cannot be undone. This will permanently delete the Andon record.</p>
            </div>
        </div>
    </div>
    
    <x-slot:actions>
        <x-button label="Cancel" class="btn-ghost" wire:click="$set('deleteModal', false)" />
        <x-button label="Yes, Delete" class="btn-error text-white" wire:click="deleteAndon" spinner="deleteAndon" />
    </x-slot:actions>
</x-modal>
