@php
    $users = \App\Models\User::orderBy('name')->get()->map(fn($u) => ['id' => $u->jid_no, 'name' => $u->name]);
    $usersSelect = [['id' => '', 'name' => 'Select PIC']] + $users->toArray();
    $statuses = [['id'=>'Open','name'=>'Open'],['id'=>'Planned','name'=>'Planned'],['id'=>'In Progress','name'=>'In Progress'],['id'=>'Done','name'=>'Done']];
@endphp

{{-- ADD MODAL --}}
<x-modal wire:model="addModal" title="New Overhaul Report" separator class="backdrop-blur-sm" box-class="w-11/12 max-w-5xl max-h-[85vh] overflow-y-auto">
    <x-form wire:submit.prevent="saveAdd" no-separator>
        <div x-data="{ tab: 'utama' }">
            <div class="w-full flex overflow-x-auto border-b border-base-content/10 mb-4 rounded-lg bg-base-200/30 p-1 gap-1">
                <button type="button" @click.prevent="tab = 'utama'" :class="tab === 'utama' ? 'bg-neutral text-neutral-content shadow-sm' : 'hover:bg-base-200/50 text-base-content/70'" class="flex-1 text-center font-bold py-2.5 rounded-md transition-all cursor-pointer flex justify-center items-center gap-2">
                    <x-icon name="o-document-text" class="w-5 h-5" />
                    <span class="hidden sm:block">Data Utama</span>
                </button>
                <button type="button" @click.prevent="tab = 'action'" :class="tab === 'action' ? 'bg-neutral text-neutral-content shadow-sm' : 'hover:bg-base-200/50 text-base-content/70'" class="flex-1 text-center font-bold py-2.5 rounded-md transition-all cursor-pointer flex justify-center items-center gap-2">
                    <x-icon name="o-wrench-screwdriver" class="w-5 h-5" />
                    <span class="hidden sm:block">Problem & Action</span>
                </button>
                <button type="button" @click.prevent="tab = 'dokumentasi'" :class="tab === 'dokumentasi' ? 'bg-neutral text-neutral-content shadow-sm' : 'hover:bg-base-200/50 text-base-content/70'" class="flex-1 text-center font-bold py-2.5 rounded-md transition-all cursor-pointer flex justify-center items-center gap-2">
                    <x-icon name="o-photo" class="w-5 h-5" />
                    <span class="hidden sm:block">Dokumentasi</span>
                </button>
            </div>

            {{-- TAB UTAMA --}}
            <div x-show="tab === 'utama'" x-cloak x-transition>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <x-input label="Date" type="date" wire:model="date" class="[&::-webkit-calendar-picker-indicator]:absolute [&::-webkit-calendar-picker-indicator]:right-4" required />
                    <x-input label="Start Time" type="datetime-local" wire:model="start_time" wire:change="calculateTime" class="[&::-webkit-calendar-picker-indicator]:absolute [&::-webkit-calendar-picker-indicator]:right-4" />
                    <x-input label="Finish Time" type="datetime-local" wire:model="end_time" wire:change="calculateTime" class="[&::-webkit-calendar-picker-indicator]:absolute [&::-webkit-calendar-picker-indicator]:right-4" />
                    
                    <x-choices label="Line Name" wire:model.live="LineName" :options="$lineNames" option-value="name" option-label="name" search-function="searchLine" single searchable clearable required />
                    <x-choices label="Machine Name" wire:model.live="asset_id" :options="$machines" option-value="id" option-label="machine_name" search-function="searchMachine" single searchable clearable required />
                    <x-input label="Asset No" wire:model="MachineNo" readonly />
                    
                    <x-choices label="PIC 1" wire:model.live="PIC" :options="$usersList" option-value="id" option-label="name" placeholder="Select PIC" search-function="searchUser" single searchable clearable />
                    <x-choices label="PIC 2" wire:model.live="pic1" :options="$usersList1" option-value="id" option-label="name" placeholder="Select PIC" search-function="searchUser1" single searchable clearable />
                    <x-choices label="PIC 3" wire:model.live="pic2" :options="$usersList2" option-value="id" option-label="name" placeholder="Select PIC" search-function="searchUser2" single searchable clearable />
                    
                    <x-input label="Repair Time (Mins)" type="number" wire:model="repair_time" readonly />
                    <x-input label="Work Time" type="number" wire:model="work_time" readonly />
                </div>
            </div>

            {{-- TAB PROBLEM & ACTION --}}
            <div x-show="tab === 'action'" x-cloak x-transition>
                <div class="mb-4">
                    <x-textarea label="Problem" wire:model="problem" rows="2" />
                </div>

                <div class="divider mt-4 text-sm font-semibold">Repair Steps (Micro Analysis)</div>
                <div class="space-y-2">
                    @foreach($steps as $index => $step)
                        <div class="flex flex-col md:flex-row md:items-center gap-2">
                            <div class="flex-1 w-full">
                                <x-input placeholder="Step Repair..." wire:model="steps.{{ $index }}.step_repair" />
                            </div>
                            <div class="w-full md:w-24">
                                <x-input type="number" placeholder="Mins" wire:model="steps.{{ $index }}.minutes" />
                            </div>
                            <div class="flex-1 w-full">
                                <x-input placeholder="Obstacle..." wire:model="steps.{{ $index }}.obstacle" />
                            </div>
                            <div class="flex justify-end">
                                <x-button icon="o-trash" class="btn-ghost btn-sm text-error" wire:click="removeStep({{ $index }})" spinner />
                            </div>
                        </div>
                    @endforeach
                    <div class="flex justify-end pt-1">
                        <x-button label="Add Step" icon="o-plus" class="btn-sm btn-outline" wire:click="addStep" spinner />
                    </div>
                </div>

                <div class="divider mt-6 text-sm font-semibold">Spare Parts Used</div>
                <div class="space-y-2">
                    @foreach($oh_spareparts as $index => $sp)
                        <div class="flex flex-col md:flex-row md:items-center gap-2">
                            <div class="flex-1 w-full">
                                <x-choices 
                                    placeholder="Type / Name" 
                                    wire:model="oh_spareparts.{{ $index }}.type" 
                                    :options="$spareparts" 
                                    option-value="part_name" 
                                    option-label="part_name" 
                                    search-function="searchSparepart"
                                    searchable 
                                    single 
                                />
                            </div>
                            <div class="w-full md:w-24">
                                <x-input type="number" placeholder="Qty" wire:model="oh_spareparts.{{ $index }}.qty" />
                            </div>
                            <div class="flex-1 w-full md:w-1/4">
                                <x-input placeholder="Maker" wire:model="oh_spareparts.{{ $index }}.maker" />
                            </div>
                            <div class="flex-1 w-full">
                                <x-input placeholder="Remarks" wire:model="oh_spareparts.{{ $index }}.remarks" />
                            </div>
                            <div class="flex justify-end">
                                <x-button icon="o-trash" class="btn-ghost btn-sm text-error" wire:click="removeOhSparepart({{ $index }})" spinner />
                            </div>
                        </div>
                    @endforeach
                    <div class="flex justify-end pt-1">
                        <x-button label="Add Spare Part" icon="o-plus" class="btn-sm btn-outline" wire:click="addOhSparepart" spinner />
                    </div>
                </div>
            </div>

            {{-- TAB DOKUMENTASI --}}
            <div x-show="tab === 'dokumentasi'" x-cloak x-transition>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <x-textarea label="Explanation" wire:model="explanation" rows="2" />
                    <x-textarea label="Next Improvement" wire:model="next_improvement" rows="2" />
                    <x-textarea label="Yokotenkai Repair/Improvement" wire:model="yokotenkai" rows="2" />
                </div>
                <div class="divider">Photos</div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <x-file label="Before 1" wire:model="photo_before_1" accept="image/*" />
                        @if($photo_before_1)
                            <div class="mt-2"><img src="{{ $photo_before_1->temporaryUrl() }}" class="rounded shadow w-full aspect-square object-cover" /></div>
                        @endif
                    </div>
                    <div>
                        <x-file label="After 1" wire:model="photo_after_1" accept="image/*" />
                        @if($photo_after_1)
                            <div class="mt-2"><img src="{{ $photo_after_1->temporaryUrl() }}" class="rounded shadow w-full aspect-square object-cover" /></div>
                        @endif
                    </div>
                    <div>
                        <x-file label="Before 2" wire:model="photo_before_2" accept="image/*" />
                        @if($photo_before_2)
                            <div class="mt-2"><img src="{{ $photo_before_2->temporaryUrl() }}" class="rounded shadow w-full aspect-square object-cover" /></div>
                        @endif
                    </div>
                    <div>
                        <x-file label="After 2" wire:model="photo_after_2" accept="image/*" />
                        @if($photo_after_2)
                            <div class="mt-2"><img src="{{ $photo_after_2->temporaryUrl() }}" class="rounded shadow w-full aspect-square object-cover" /></div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <x-slot:actions>
            <x-button label="Cancel" class="btn-ghost" @click="$wire.addModal = false" />
            <x-button label="Save Report" class="btn-primary" type="submit" spinner="saveAdd" />
        </x-slot:actions>
    </x-form>
</x-modal>


{{-- EDIT MODAL --}}
<x-modal wire:model="editModal" title="Edit Overhaul Report" separator class="backdrop-blur-sm" box-class="w-11/12 max-w-5xl max-h-[85vh] overflow-y-auto">
    <x-form wire:submit.prevent="saveEdit" no-separator>
        <div x-data="{ tab: 'utama' }">
            <div class="w-full flex overflow-x-auto border-b border-base-content/10 mb-4 rounded-lg bg-base-200/30 p-1 gap-1">
                <button type="button" @click.prevent="tab = 'utama'" :class="tab === 'utama' ? 'bg-neutral text-neutral-content shadow-sm' : 'hover:bg-base-200/50 text-base-content/70'" class="flex-1 text-center font-bold py-2.5 rounded-md transition-all cursor-pointer flex justify-center items-center gap-2">
                    <x-icon name="o-document-text" class="w-5 h-5" />
                    <span class="hidden sm:block">Data Utama</span>
                </button>
                <button type="button" @click.prevent="tab = 'action'" :class="tab === 'action' ? 'bg-neutral text-neutral-content shadow-sm' : 'hover:bg-base-200/50 text-base-content/70'" class="flex-1 text-center font-bold py-2.5 rounded-md transition-all cursor-pointer flex justify-center items-center gap-2">
                    <x-icon name="o-wrench-screwdriver" class="w-5 h-5" />
                    <span class="hidden sm:block">Problem & Action</span>
                </button>
                <button type="button" @click.prevent="tab = 'dokumentasi'" :class="tab === 'dokumentasi' ? 'bg-neutral text-neutral-content shadow-sm' : 'hover:bg-base-200/50 text-base-content/70'" class="flex-1 text-center font-bold py-2.5 rounded-md transition-all cursor-pointer flex justify-center items-center gap-2">
                    <x-icon name="o-photo" class="w-5 h-5" />
                    <span class="hidden sm:block">Dokumentasi</span>
                </button>
            </div>

            {{-- TAB UTAMA --}}
            <div x-show="tab === 'utama'" x-cloak x-transition>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <x-input label="Date" type="date" wire:model="date" class="[&::-webkit-calendar-picker-indicator]:absolute [&::-webkit-calendar-picker-indicator]:right-4" required />
                    <x-input label="Start Time" type="datetime-local" wire:model="start_time" wire:change="calculateTime" class="[&::-webkit-calendar-picker-indicator]:absolute [&::-webkit-calendar-picker-indicator]:right-4" />
                    <x-input label="Finish Time" type="datetime-local" wire:model="end_time" wire:change="calculateTime" class="[&::-webkit-calendar-picker-indicator]:absolute [&::-webkit-calendar-picker-indicator]:right-4" />
                    
                    <x-choices label="Line Name" wire:model.live="LineName" :options="$lineNames" option-value="name" option-label="name" search-function="searchLine" single searchable clearable required />
                    <x-choices label="Machine Name" wire:model.live="asset_id" :options="$machines" option-value="id" option-label="machine_name" search-function="searchMachine" single searchable clearable required />
                    <x-input label="Asset No" wire:model="MachineNo" readonly />
                    
                    <x-choices label="PIC 1" wire:model.live="PIC" :options="$usersList" option-value="id" option-label="name" placeholder="Select PIC" search-function="searchUser" single searchable clearable />
                    <x-choices label="PIC 2" wire:model.live="pic1" :options="$usersList1" option-value="id" option-label="name" placeholder="Select PIC" search-function="searchUser1" single searchable clearable />
                    <x-choices label="PIC 3" wire:model.live="pic2" :options="$usersList2" option-value="id" option-label="name" placeholder="Select PIC" search-function="searchUser2" single searchable clearable />
                    
                    <x-input label="Repair Time (Mins)" type="number" wire:model="repair_time" readonly />
                    <x-input label="Work Time" type="number" wire:model="work_time" readonly />
                </div>
            </div>

            {{-- TAB PROBLEM & ACTION --}}
            <div x-show="tab === 'action'" x-cloak x-transition>
                <div class="mb-4">
                    <x-textarea label="Problem" wire:model="problem" rows="2" />
                </div>

                <div class="divider mt-4 text-sm font-semibold">Repair Steps (Micro Analysis)</div>
                <div class="space-y-2">
                    @foreach($steps as $index => $step)
                        <div class="flex flex-col md:flex-row md:items-center gap-2">
                            <div class="flex-1 w-full">
                                <x-input placeholder="Step Repair..." wire:model="steps.{{ $index }}.step_repair" />
                            </div>
                            <div class="w-full md:w-24">
                                <x-input type="number" placeholder="Mins" wire:model="steps.{{ $index }}.minutes" />
                            </div>
                            <div class="flex-1 w-full">
                                <x-input placeholder="Obstacle..." wire:model="steps.{{ $index }}.obstacle" />
                            </div>
                            <div class="flex justify-end">
                                <x-button icon="o-trash" class="btn-ghost btn-sm text-error" wire:click="removeStep({{ $index }})" spinner />
                            </div>
                        </div>
                    @endforeach
                    <div class="flex justify-end pt-1">
                        <x-button label="Add Step" icon="o-plus" class="btn-sm btn-outline" wire:click="addStep" spinner />
                    </div>
                </div>

                <div class="divider mt-8 text-sm md:text-base font-semibold">Spare Parts Used</div>
                <div class="space-y-2">
                    @foreach($oh_spareparts as $index => $sp)
                        <div class="flex flex-col md:flex-row md:items-center gap-2">
                            <div class="flex-1 w-full">
                                <x-choices 
                                    placeholder="Type / Name" 
                                    wire:model="oh_spareparts.{{ $index }}.type" 
                                    :options="$spareparts" 
                                    option-value="part_name" 
                                    option-label="part_name" 
                                    search-function="searchSparepart"
                                    searchable 
                                    single 
                                />
                            </div>
                            <div class="w-full md:w-24">
                                <x-input type="number" placeholder="Qty" wire:model="oh_spareparts.{{ $index }}.qty" />
                            </div>
                            <div class="flex-1 w-full md:w-1/4">
                                <x-input placeholder="Maker" wire:model="oh_spareparts.{{ $index }}.maker" />
                            </div>
                            <div class="flex-1 w-full">
                                <x-input placeholder="Remarks" wire:model="oh_spareparts.{{ $index }}.remarks" />
                            </div>
                            <div class="flex justify-end">
                                <x-button icon="o-trash" class="btn-ghost btn-sm text-error" wire:click="removeOhSparepart({{ $index }})" spinner />
                            </div>
                        </div>
                    @endforeach
                    <div class="flex justify-end pt-1">
                        <x-button label="Add Spare Part" icon="o-plus" class="btn-sm btn-outline" wire:click="addOhSparepart" spinner />
                    </div>
                </div>
            </div>

            {{-- TAB DOKUMENTASI --}}
            <div x-show="tab === 'dokumentasi'" x-cloak x-transition>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <x-textarea label="Explanation" wire:model="explanation" rows="2" />
                    <x-textarea label="Next Improvement" wire:model="next_improvement" rows="2" />
                    <x-textarea label="Yokotenkai Repair/Improvement" wire:model="yokotenkai" rows="2" />
                </div>
                <div class="divider">Photos</div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <x-file label="Before 1" wire:model="photo_before_1" accept="image/*" />
                        @if($photo_before_1)
                            <div class="mt-2"><img src="{{ $photo_before_1->temporaryUrl() }}" class="rounded shadow w-full aspect-square object-cover" /></div>
                        @elseif($existing_photo_before_1)
                            <div class="mt-2"><img src="{{ Storage::url($existing_photo_before_1) }}" class="rounded shadow w-full aspect-square object-cover" /></div>
                        @endif
                    </div>
                    <div>
                        <x-file label="After 1" wire:model="photo_after_1" accept="image/*" />
                        @if($photo_after_1)
                            <div class="mt-2"><img src="{{ $photo_after_1->temporaryUrl() }}" class="rounded shadow w-full aspect-square object-cover" /></div>
                        @elseif($existing_photo_after_1)
                            <div class="mt-2"><img src="{{ Storage::url($existing_photo_after_1) }}" class="rounded shadow w-full aspect-square object-cover" /></div>
                        @endif
                    </div>
                    <div>
                        <x-file label="Before 2" wire:model="photo_before_2" accept="image/*" />
                        @if($photo_before_2)
                            <div class="mt-2"><img src="{{ $photo_before_2->temporaryUrl() }}" class="rounded shadow w-full aspect-square object-cover" /></div>
                        @elseif($existing_photo_before_2)
                            <div class="mt-2"><img src="{{ Storage::url($existing_photo_before_2) }}" class="rounded shadow w-full aspect-square object-cover" /></div>
                        @endif
                    </div>
                    <div>
                        <x-file label="After 2" wire:model="photo_after_2" accept="image/*" />
                        @if($photo_after_2)
                            <div class="mt-2"><img src="{{ $photo_after_2->temporaryUrl() }}" class="rounded shadow w-full aspect-square object-cover" /></div>
                        @elseif($existing_photo_after_2)
                            <div class="mt-2"><img src="{{ Storage::url($existing_photo_after_2) }}" class="rounded shadow w-full aspect-square object-cover" /></div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <x-slot:actions>
            <x-button label="Cancel" class="btn-ghost" @click="$wire.editModal = false" />
            <x-button label="Save Changes" class="btn-primary" type="submit" spinner="saveEdit" />
        </x-slot:actions>
    </x-form>
</x-modal>
