@php
    $users = \App\Models\User::whereNotNull('repair')->orderBy('repair')->get()->map(fn($u) => ['id' => $u->jid_no, 'name' => $u->repair]);
    $usersSelect = [['id' => '', 'name' => 'Select PIC']] + $users->toArray();
    $statuses = [['id'=>'Open','name'=>'Open'],['id'=>'Planned','name'=>'Planned'],['id'=>'In Progress','name'=>'In Progress'],['id'=>'Done','name'=>'Done']];
@endphp

{{-- ADD MODAL --}}
<x-modal wire:model="addModal" title="New Overhaul Report" separator class="backdrop-blur-sm" box-class="w-11/12 max-w-5xl">
    <x-form wire:submit.prevent="saveAdd" no-separator>
        <x-tabs selected="tab-utama">
            {{-- TAB UTAMA --}}
            <x-tab name="tab-utama" label="Data Utama" icon="o-document-text">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <x-input label="Date" type="date" wire:model="date" required />
                    <x-input label="Start Time" type="datetime-local" wire:model="start_time" wire:change="calculateTime" />
                    <x-input label="Finish Time" type="datetime-local" wire:model="end_time" wire:change="calculateTime" />
                    
                    <x-choices label="Line Name" wire:model.live="LineName" :options="$lineNames" option-value="name" option-label="name" single searchable required />
                    <x-choices label="Machine Name" wire:model.live="asset_id" :options="$machines" option-value="id" option-label="machine_name" single searchable required />
                    <x-input label="Asset No" wire:model="MachineNo" readonly />
                    
                    <x-select label="PIC 1" wire:model="PIC" :options="$usersSelect" option-value="id" option-label="name" />
                    <x-select label="PIC 2" wire:model="pic1" :options="$usersSelect" option-value="id" option-label="name" />
                    <x-select label="PIC 3" wire:model="pic2" :options="$usersSelect" option-value="id" option-label="name" />
                    
                    <x-input label="Repair Time (Mins)" type="number" wire:model="repair_time" readonly />
                    <x-input label="Work Time" type="number" wire:model="work_time" />
                </div>
            </x-tab>

            {{-- TAB PROBLEM & ACTION --}}
            <x-tab name="tab-action" label="Problem & Action" icon="o-wrench-screwdriver">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <x-textarea label="Problem" wire:model="problem" rows="2" />
                    <x-textarea label="Description" wire:model="description" rows="2" />
                </div>

                <div class="divider">Repair Steps (Micro Analysis)</div>
                <div class="space-y-2">
                    @foreach($steps as $index => $step)
                        <div class="flex items-center gap-2">
                            <div class="flex-1">
                                <x-input placeholder="Step Repair..." wire:model="steps.{{ $index }}.step_repair" />
                            </div>
                            <div class="w-24">
                                <x-input type="number" placeholder="Mins" wire:model="steps.{{ $index }}.minutes" />
                            </div>
                            <div class="flex-1">
                                <x-input placeholder="Obstacle..." wire:model="steps.{{ $index }}.obstacle" />
                            </div>
                            <x-button icon="o-trash" class="btn-ghost btn-sm text-error" wire:click="removeStep({{ $index }})" />
                        </div>
                    @endforeach
                    <div class="flex justify-end">
                        <x-button label="Add Step" icon="o-plus" class="btn-sm btn-outline" wire:click="addStep" />
                    </div>
                </div>

                <div class="divider mt-6">Spare Parts Used</div>
                <div class="space-y-2">
                    @foreach($spareparts as $index => $sp)
                        <div class="flex items-center gap-2">
                            <div class="flex-1">
                                <x-input placeholder="Type / Name" wire:model="spareparts.{{ $index }}.type" />
                            </div>
                            <div class="w-24">
                                <x-input type="number" placeholder="Qty" wire:model="spareparts.{{ $index }}.qty" />
                            </div>
                            <div class="w-1/4">
                                <x-input placeholder="Maker" wire:model="spareparts.{{ $index }}.maker" />
                            </div>
                            <div class="flex-1">
                                <x-input placeholder="Remarks" wire:model="spareparts.{{ $index }}.remarks" />
                            </div>
                            <x-button icon="o-trash" class="btn-ghost btn-sm text-error" wire:click="removeSparepart({{ $index }})" />
                        </div>
                    @endforeach
                    <div class="flex justify-end">
                        <x-button label="Add Spare Part" icon="o-plus" class="btn-sm btn-outline" wire:click="addSparepart" />
                    </div>
                </div>
            </x-tab>

            {{-- TAB DOKUMENTASI --}}
            <x-tab name="tab-dokumentasi" label="Dokumentasi" icon="o-photo">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-textarea label="Explanation" wire:model="explanation" rows="2" class="col-span-1 md:col-span-2" />
                    <x-textarea label="Next Improvement" wire:model="next_improvement" rows="2" />
                    <x-textarea label="Yokotenkai Repair/Improvement" wire:model="yokotenkai" rows="2" />
                </div>
                <div class="divider">Photos</div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <x-file label="Before 1" wire:model="photo_before_1" accept="image/*" />
                    <x-file label="After 1" wire:model="photo_after_1" accept="image/*" />
                    <x-file label="Before 2" wire:model="photo_before_2" accept="image/*" />
                    <x-file label="After 2" wire:model="photo_after_2" accept="image/*" />
                </div>
            </x-tab>
        </x-tabs>
        
        <x-slot:actions>
            <x-button label="Cancel" class="btn-ghost" wire:click="$set('addModal',false)" />
            <x-button label="Save Report" class="btn-primary" type="submit" spinner="saveAdd" />
        </x-slot:actions>
    </x-form>
</x-modal>


{{-- EDIT MODAL --}}
<x-modal wire:model="editModal" title="Edit Overhaul Report" separator class="backdrop-blur-sm" box-class="w-11/12 max-w-5xl">
    <x-form wire:submit.prevent="saveEdit" no-separator>
        <x-tabs selected="tab-utama-edit">
            {{-- TAB UTAMA --}}
            <x-tab name="tab-utama-edit" label="Data Utama" icon="o-document-text">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <x-input label="Date" type="date" wire:model="date" required />
                    <x-input label="Start Time" type="datetime-local" wire:model="start_time" wire:change="calculateTime" />
                    <x-input label="Finish Time" type="datetime-local" wire:model="end_time" wire:change="calculateTime" />
                    
                    <x-choices label="Line Name" wire:model.live="LineName" :options="$lineNames" option-value="name" option-label="name" single searchable required />
                    <x-choices label="Machine Name" wire:model.live="asset_id" :options="$machines" option-value="id" option-label="machine_name" single searchable required />
                    <x-input label="Asset No" wire:model="MachineNo" readonly />
                    
                    <x-select label="PIC 1" wire:model="PIC" :options="$usersSelect" option-value="id" option-label="name" />
                    <x-select label="PIC 2" wire:model="pic1" :options="$usersSelect" option-value="id" option-label="name" />
                    <x-select label="PIC 3" wire:model="pic2" :options="$usersSelect" option-value="id" option-label="name" />
                    
                    <x-input label="Repair Time (Mins)" type="number" wire:model="repair_time" readonly />
                    <x-input label="Work Time" type="number" wire:model="work_time" />
                </div>
            </x-tab>

            {{-- TAB PROBLEM & ACTION --}}
            <x-tab name="tab-action-edit" label="Problem & Action" icon="o-wrench-screwdriver">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <x-textarea label="Problem" wire:model="problem" rows="2" />
                    <x-textarea label="Description" wire:model="description" rows="2" />
                </div>

                <div class="divider">Repair Steps (Micro Analysis)</div>
                <div class="space-y-2">
                    @foreach($steps as $index => $step)
                        <div class="flex items-center gap-2">
                            <div class="flex-1">
                                <x-input placeholder="Step Repair..." wire:model="steps.{{ $index }}.step_repair" />
                            </div>
                            <div class="w-24">
                                <x-input type="number" placeholder="Mins" wire:model="steps.{{ $index }}.minutes" />
                            </div>
                            <div class="flex-1">
                                <x-input placeholder="Obstacle..." wire:model="steps.{{ $index }}.obstacle" />
                            </div>
                            <x-button icon="o-trash" class="btn-ghost btn-sm text-error" wire:click="removeStep({{ $index }})" />
                        </div>
                    @endforeach
                    <div class="flex justify-end">
                        <x-button label="Add Step" icon="o-plus" class="btn-sm btn-outline" wire:click="addStep" />
                    </div>
                </div>

                <div class="divider mt-6">Spare Parts Used</div>
                <div class="space-y-2">
                    @foreach($spareparts as $index => $sp)
                        <div class="flex items-center gap-2">
                            <div class="flex-1">
                                <x-input placeholder="Type / Name" wire:model="spareparts.{{ $index }}.type" />
                            </div>
                            <div class="w-24">
                                <x-input type="number" placeholder="Qty" wire:model="spareparts.{{ $index }}.qty" />
                            </div>
                            <div class="w-1/4">
                                <x-input placeholder="Maker" wire:model="spareparts.{{ $index }}.maker" />
                            </div>
                            <div class="flex-1">
                                <x-input placeholder="Remarks" wire:model="spareparts.{{ $index }}.remarks" />
                            </div>
                            <x-button icon="o-trash" class="btn-ghost btn-sm text-error" wire:click="removeSparepart({{ $index }})" />
                        </div>
                    @endforeach
                    <div class="flex justify-end">
                        <x-button label="Add Spare Part" icon="o-plus" class="btn-sm btn-outline" wire:click="addSparepart" />
                    </div>
                </div>
            </x-tab>

            {{-- TAB DOKUMENTASI --}}
            <x-tab name="tab-dokumentasi-edit" label="Dokumentasi" icon="o-photo">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-textarea label="Explanation" wire:model="explanation" rows="2" class="col-span-1 md:col-span-2" />
                    <x-textarea label="Next Improvement" wire:model="next_improvement" rows="2" />
                    <x-textarea label="Yokotenkai Repair/Improvement" wire:model="yokotenkai" rows="2" />
                </div>
                <div class="divider">Photos</div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <x-file label="Before 1" wire:model="photo_before_1" accept="image/*" />
                        @if($existing_photo_before_1)
                            <div class="mt-2"><img src="{{ Storage::url($existing_photo_before_1) }}" class="rounded shadow w-full aspect-square object-cover" /></div>
                        @endif
                    </div>
                    <div>
                        <x-file label="After 1" wire:model="photo_after_1" accept="image/*" />
                        @if($existing_photo_after_1)
                            <div class="mt-2"><img src="{{ Storage::url($existing_photo_after_1) }}" class="rounded shadow w-full aspect-square object-cover" /></div>
                        @endif
                    </div>
                    <div>
                        <x-file label="Before 2" wire:model="photo_before_2" accept="image/*" />
                        @if($existing_photo_before_2)
                            <div class="mt-2"><img src="{{ Storage::url($existing_photo_before_2) }}" class="rounded shadow w-full aspect-square object-cover" /></div>
                        @endif
                    </div>
                    <div>
                        <x-file label="After 2" wire:model="photo_after_2" accept="image/*" />
                        @if($existing_photo_after_2)
                            <div class="mt-2"><img src="{{ Storage::url($existing_photo_after_2) }}" class="rounded shadow w-full aspect-square object-cover" /></div>
                        @endif
                    </div>
                </div>
            </x-tab>
        </x-tabs>
        
        <x-slot:actions>
            <x-button label="Cancel" class="btn-ghost" wire:click="$set('editModal',false)" />
            <x-button label="Save Changes" class="btn-primary" type="submit" spinner="saveEdit" />
        </x-slot:actions>
    </x-form>
</x-modal>
