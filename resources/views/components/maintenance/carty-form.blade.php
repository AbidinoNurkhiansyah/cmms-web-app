<div class="space-y-6">
    <!-- General Info Card -->
    <x-card title="General Information" shadow class="bg-base-100">
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <x-input label="Date" type="date" wire:model="Date" />
            <x-select label="Shift" wire:model="Shift" :options="[['id'=>'1','name'=>'1'],['id'=>'2','name'=>'2'],['id'=>'3','name'=>'3']]" option-value="id" option-label="name" />
            <x-input label="Group Line" wire:model="groupline" />
            <x-input label="Line Name" wire:model="LineName" />
            <x-input label="Machine Name" wire:model="MachineName" />
            <x-input label="Machine No (Asset No)" wire:model="MachineNo" />
            <x-input label="Equipment" wire:model="equipment" />
            <x-input label="Classification" wire:model="classification" />
        </div>
    </x-card>

    <!-- Problem Details Card -->
    <x-card title="Problem & Action Details" shadow class="bg-base-100">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <x-select label="Type of Problem" wire:model="typeofproblem" :options="[['id'=>'Electrical','name'=>'Electrical'],['id'=>'Mechanical','name'=>'Mechanical'],['id'=>'Other','name'=>'Other']]" option-value="id" option-label="name" placeholder="Select" />
            <x-select label="Status" wire:model="Status" :options="[['id'=>'Open','name'=>'Open'],['id'=>'Close','name'=>'Close']]" option-value="id" option-label="name" />
            <x-input label="Spare Part Name" wire:model="sparepartName" />
            <x-input label="Spare Part Type" wire:model="sparepartType" />
            
            <div class="md:col-span-2">
                <x-textarea label="Problem" wire:model="Problem" rows="3" />
            </div>
            <div class="md:col-span-2">
                <x-textarea label="Cause" wire:model="Cause" rows="3" />
            </div>
            
            <div class="md:col-span-full">
                <x-textarea label="Action (Countermeasures)" wire:model="Action" rows="4" />
            </div>
        </div>
    </x-card>

    <!-- Traceability / Visuals Card -->
    <x-card title="Traceability (Images)" shadow class="bg-base-100">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <x-file label="Before Image 1" wire:model="filebefore1" accept="image/png, image/jpeg" />
            <x-file label="Before Image 2" wire:model="filebefore2" accept="image/png, image/jpeg" />
            <x-file label="After Image 1" wire:model="fileafter1" accept="image/png, image/jpeg" />
            <x-file label="After Image 2" wire:model="fileafter2" accept="image/png, image/jpeg" />
        </div>
    </x-card>

    <!-- Time & Personnel Card -->
    <x-card title="Time & Personnel" shadow class="bg-base-100">
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
            <x-input label="Start Repair" type="time" wire:model="start_time" />
            <x-input label="Finish Repair" type="time" wire:model="finish_time" />
            <x-input label="Repair Time (mins)" type="number" wire:model="DownTime" />
            <x-input label="Total Stop (mins)" type="number" wire:model="stopline" />
            <x-input label="Work Time (mins)" type="number" wire:model="worktime" />

            <x-input label="PIC 1" wire:model="PIC" />
            <x-input label="PIC 2" wire:model="pic2" />
            <x-input label="PIC 3" wire:model="pic3" />
            <x-input label="PIC Repair" wire:model="pic_repair" />
        </div>
    </x-card>
</div>
