{{-- Add Modal --}}
<x-modal wire:model="addModal" title="New Work Order Request" separator class="backdrop-blur-sm" box-class="w-11/12 max-w-6xl max-h-[95vh] overflow-hidden flex flex-col">
    <div class="overflow-y-auto pr-2" style="max-height: calc(95vh - 12rem);">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        
        {{-- Left Column: Image Preview & Upload --}}
        <div class="lg:col-span-1 flex flex-col gap-3">
            <h3 class="font-bold text-gray-700">Photo Request</h3>
            
            @if ($addForm->foto_req)
                <div class="relative w-full rounded-xl overflow-hidden shadow-sm border border-base-300 bg-black flex items-center justify-center" style="min-height: 160px;">
                    <img src="{{ $addForm->foto_req->temporaryUrl() }}" class="object-contain w-full h-full max-h-48" alt="Preview" />
                </div>
                <div class="text-sm font-medium text-success flex items-center gap-1">
                    <x-icon name="o-check-circle" class="w-4 h-4" /> Ready to upload
                </div>
            @else
                <div class="w-full rounded-xl bg-base-200 border-2 border-dashed border-base-300 flex flex-col items-center justify-center text-gray-400 p-4" style="min-height: 160px;">
                    <x-icon name="o-photo" class="w-10 h-10 mb-1 opacity-50" />
                    <span class="text-sm">No photo selected</span>
                </div>
            @endif

            <x-file wire:model="addForm.foto_req" label="Select Image" accept="image/jpeg, image/png, image/jpg" class="mt-auto" />
        </div>

        {{-- Right Column: Form Inputs --}}
        <div class="lg:col-span-2 grid grid-cols-1 md:grid-cols-3 gap-3">
            <x-input label="Date" type="date" wire:model="addForm.date" />
            <x-input label="Target Date" type="date" wire:model="addForm.target_date" />
            <x-select label="Order Type" wire:model="addForm.order_type"
                :options="[['id'=>'Install','name'=>'Install'],['id'=>'Repair','name'=>'Repair'],['id'=>'Kaizen','name'=>'Kaizen']]"
                option-value="id" option-label="name" placeholder="Select type" />
            
            <x-input label="Requester" wire:model="addForm.requester" />
            <x-select label="Department" wire:model="addForm.department"
                :options="collect(['Prod 1','Prod 2','Prod 3','Prod 4','Prod 5','Jishuken','IT','HR','GA','EHS','PPIC','PE','ME','QC'])->map(fn($v) => ['id'=>$v,'name'=>$v])"
                option-value="id" option-label="name" placeholder="Select Dept" />
            <x-select label="Priority (Level)" wire:model="addForm.priority"
                :options="[['id'=>'Low','name'=>'Low'],['id'=>'Medium','name'=>'Medium'],['id'=>'High','name'=>'High']]"
                option-value="id" option-label="name" />
                
            <x-choices label="Line Name" wire:model.live="LineName" :options="$lineNames" option-value="name" option-label="name" single searchable />
            <x-choices label="Machine" wire:model.live="asset_id" :options="$machines" option-value="id" option-label="machine_name" single searchable />
            <x-input label="Machine No (Asset)" wire:model="MachineNo" readonly class="bg-base-200" />
            
            <div class="col-span-full">
                <x-textarea label="Problem Description" wire:model="addForm.problem" rows="2" />
            </div>
        </div>

    </div>
    </div>
    <x-slot:actions>
        <x-button label="Cancel" class="btn-ghost" wire:click="$set('addModal',false)" />
        <x-button label="Submit Request" class="btn-primary" wire:click="saveAdd" spinner="saveAdd" />
    </x-slot:actions>
</x-modal>
