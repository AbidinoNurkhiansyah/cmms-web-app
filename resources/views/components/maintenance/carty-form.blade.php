<div class="space-y-6">
    <!-- General Info Card -->
    <x-card title="General Information" shadow class="bg-base-100">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <x-input label="Date" type="date" wire:model="Date" />
            <x-select label="Shift" wire:model="Shift" :options="[['id' => '1', 'name' => '1'], ['id' => '2', 'name' => '2']]"
                option-value="id" option-label="name" />
            <x-select label="Group Line" wire:model="groupline" :options="[['id' => 'MTC A', 'name' => 'MTC A'], ['id' => 'MTC B', 'name' => 'MTC B']]" option-value="id" option-label="name" placeholder="Select Group Line" />

            <x-choices label="Line Name" wire:model.live="LineName" :options="collect($lineNames)->map(fn($l) => ['id' => $l, 'name' => $l])" option-value="id" option-label="name" single searchable
                search-function="searchLine" placeholder="Select Line..." no-progress debounce="50ms" />
            <x-choices label="Machine Name" wire:model.live="asset_id" :options="$machines" option-label="machine_name"
                option-value="id" single searchable search-function="searchMachine" placeholder="Select Machine..."
                no-progress debounce="50ms" />

            <x-input label="Machine No (Asset No)" wire:model="MachineNo" readonly />
        </div>
    </x-card>

    <!-- Problem Details Card -->
    <x-card title="Problem & Action Details" shadow class="bg-base-100">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="lg:col-span-2">
                <x-select label="Type of Problem" wire:model="typeofproblem"
                    :options="[['id' => 'Electrical', 'name' => 'Electrical'], ['id' => 'Mechanical', 'name' => 'Mechanical'], ['id' => 'Other', 'name' => 'Other']]"
                    option-value="id" option-label="name" placeholder="Select" />
            </div>
            <div class="lg:col-span-2">
                <x-select label="Status" wire:model="Status"
                    :options="[['id' => 'Temporary', 'name' => 'Temporary'], ['id' => 'Permanent', 'name' => 'Permanent']]" option-value="id"
                    option-label="name" />
            </div>

            <div class="lg:col-span-2">
                <x-choices label="Spare Part Name" wire:model.live="sparepartName" :options="$spareparts" option-label="part_name" option-value="part_name"
                    single searchable search-function="searchSparepart" placeholder="Search Spare Part..." no-progress debounce="50ms" />
            </div>
            <div class="lg:col-span-2">
                <x-input label="Spare Part Quantity" wire:model="sparepartQty" type="number" />
            </div>

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
            <!-- Before Image 1 -->
            <div class="flex flex-col gap-2">
                @if ($filebefore1)
                    <img src="{{ is_string($filebefore1) ? asset($filebefore1) : $filebefore1->temporaryUrl() }}" 
                         class="h-32 w-full object-cover rounded border" alt="Preview">
                @endif
                <x-file label="Before Image 1" wire:model="filebefore1" accept="image/png, image/jpeg" />
            </div>
            
            <!-- Before Image 2 -->
            <div class="flex flex-col gap-2">
                @if ($filebefore2)
                    <img src="{{ is_string($filebefore2) ? asset($filebefore2) : $filebefore2->temporaryUrl() }}" 
                         class="h-32 w-full object-cover rounded border" alt="Preview">
                @endif
                <x-file label="Before Image 2" wire:model="filebefore2" accept="image/png, image/jpeg" />
            </div>
            
            <!-- After Image 1 -->
            <div class="flex flex-col gap-2">
                @if ($fileafter1)
                    <img src="{{ is_string($fileafter1) ? asset($fileafter1) : $fileafter1->temporaryUrl() }}" 
                         class="h-32 w-full object-cover rounded border" alt="Preview">
                @endif
                <x-file label="After Image 1" wire:model="fileafter1" accept="image/png, image/jpeg" />
            </div>
            
            <!-- After Image 2 -->
            <div class="flex flex-col gap-2">
                @if ($fileafter2)
                    <img src="{{ is_string($fileafter2) ? asset($fileafter2) : $fileafter2->temporaryUrl() }}" 
                         class="h-32 w-full object-cover rounded border" alt="Preview">
                @endif
                <x-file label="After Image 2" wire:model="fileafter2" accept="image/png, image/jpeg" />
            </div>
        </div>
    </x-card>

    <!-- Time & Personnel Card -->
    <x-card title="Time & Personnel" shadow class="bg-base-100">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <x-input label="Start Repair" type="time" wire:model="start_time" />
            <x-input label="Finish Repair" type="time" wire:model="finish_time" />
            <x-input label="Down Time (mins)" type="number" wire:model="DownTime" />
            <x-input label="Work Time (mins)" type="number" wire:model="worktime" />

            <div class="md:col-span-full space-y-2 mt-4">
                <div class="flex items-center gap-4 border-b pb-2">
                    <span class="font-semibold text-sm">Personnel (PIC)</span>
                    <x-button wire:click.prevent="addPic" icon="o-plus" class="btn-sm btn-ghost text-primary"
                        tooltip="Add PIC" />
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 pt-2">
                    @foreach($pics as $index => $pic)
                        <div class="flex items-end gap-2">
                            <div class="flex-1">
                                <x-choices label="PIC {{ $index + 1 }}" wire:model="pics.{{ $index }}"
                                    :options="$users" option-label="name" option-value="name" searchable search-function="searchUser" placeholder="Select PIC..." single no-progress debounce="50ms" />
                            </div>
                            @if(count($pics) > 1)
                                <x-button icon="o-trash" wire:click.prevent="removePic({{ $index }})"
                                    class="btn-square btn-error btn-sm mb-1 text-white dark:text-gray-700" tooltip="Remove" />
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </x-card>
</div>