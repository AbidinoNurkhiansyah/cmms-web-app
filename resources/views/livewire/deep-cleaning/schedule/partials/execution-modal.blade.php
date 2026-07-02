    {{-- Execution / Item Check Modal --}}
    <x-modal wire:model="itemCheckModal" title="Execute TPM Schedule" class="backdrop-blur !transition-none !duration-0" box-class="w-11/12 max-w-5xl !transition-none !transform-none !duration-0">
        @if($itemCheckScheduleId)
            @php
                $currentSchedule = \App\Models\DeepCleaningSchedule::find($itemCheckScheduleId);
                $isApproved = $currentSchedule?->is_approved;
            @endphp
            <div class="flex flex-col gap-3">
                <!-- Header Info -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 p-3 bg-base-200 rounded-lg items-center">
                    <div class="leading-tight">
                        <div class="text-[10px] uppercase font-bold text-base-content/50">Machine</div>
                        <div class="font-semibold text-sm truncate">{{ $currentSchedule?->NameMachine }} <span class="text-xs text-base-content/70">({{ $currentSchedule?->LineName }})</span></div>
                    </div>
                    <div class="leading-tight">
                        <div class="text-[10px] uppercase font-bold text-base-content/50">Asset No</div>
                        <div class="font-semibold text-sm truncate">{{ $currentSchedule?->machine_no }}</div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="text-[10px] uppercase font-bold text-base-content/50 whitespace-nowrap">Actual Date</div>
                        <input type="date" class="input input-sm input-bordered w-full" wire:model="actDate" {{ $isApproved ? 'disabled' : '' }} />
                    </div>
                </div>

                <!-- Tabs -->
                <x-tabs wire:model="itemCheckTab" active-class="bg-neutral text-neutral-content">
                    <x-tab name="execute" label="Checklist Execution" icon="o-clipboard-document-list">
                        <div class="overflow-x-auto overflow-y-auto max-h-[50vh] mt-2">
                            <table class="table table-sm table-zebra">
                                <thead>
                                    <tr>
                                        <th>Item Check</th>
                                        <th>Standard</th>
                                        <th class="w-64">Result</th>
                                        <th class="w-32 text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($machineItems as $item)
                                        @php
                                            $isDone = isset($scheduleItems[$item['itemCheck']]);
                                            $resultVal = $scheduleItems[$item['itemCheck']] ?? '';
                                        @endphp
                                        <tr>
                                            <td class="whitespace-normal">{{ $item['itemCheck'] }}</td>
                                            <td class="whitespace-normal">{{ $item['standard'] }}</td>
                                            <td>
                                                <input type="text" class="input input-sm input-bordered w-full"
                                                    placeholder="Type result..." value="{{ $resultVal }}"
                                                    x-on:change="$wire.updateItemResult('{{ addslashes($item['itemCheck']) }}', $event.target.value)"
                                                    {{ $isApproved ? 'disabled' : '' }}>
                                            </td>
                                            <td class="text-center">
                                                <x-button
                                                    class="btn-sm {{ $isDone ? 'btn-success' : 'btn-ghost border-base-300' }}"
                                                    icon="{{ $isDone ? 'o-check-circle' : 'o-minus-circle' }}"
                                                    label="{{ $isDone ? 'Done' : 'Pending' }}"
                                                    wire:click="toggleItemStatus('{{ addslashes($item['itemCheck']) }}')"
                                                    :disabled="$isApproved" />
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-base-content/60 py-4">No checklist standards
                                                found for this machine. Please add them in the Manage Parameters tab.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </x-tab>

                    <x-tab name="manage" label="Manage Parameters" icon="o-cog-6-tooth">
                        <div class="p-3 border border-base-300 rounded-lg mt-2 flex flex-col gap-2">
                            <div class="text-sm font-semibold opacity-70">Add New Master Parameter</div>
                            <div class="grid grid-cols-1 md:grid-cols-5 gap-2 items-end">
                                <div class="col-span-2">
                                    <x-input label="Item Check" wire:model="newItemCheck"
                                        placeholder="e.g. Check Oil Level" />
                                </div>
                                <div class="col-span-2">
                                    <x-input label="Standard" wire:model="newStandard"
                                        placeholder="e.g. Normal limit is above 50%" />
                                </div>
                                <div>
                                    <x-button label="Add Item" class="btn-neutral w-full" icon="o-plus"
                                        wire:click="saveNewMachineItem" />
                                </div>
                            </div>

                            <div class="divider my-0"></div>

                            <div class="overflow-x-auto overflow-y-auto max-h-[40vh]">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Item Check</th>
                                            <th>Standard</th>
                                            <th class="w-24 text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($machineItems as $item)
                                            <tr>
                                                <td class="whitespace-normal">{{ $item['itemCheck'] }}</td>
                                                <td class="whitespace-normal">{{ $item['standard'] }}</td>
                                                <td class="text-center">
                                                    <x-button class="btn-sm btn-ghost text-error" icon="o-trash"
                                                        wire:click="deleteMachineItem({{ $item['id'] }})" />
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center text-base-content/60 py-4">No parameters yet.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </x-tab>
                </x-tabs>
            </div>

            <x-slot:actions>
                <x-button label="Close" @click="$wire.itemCheckModal = false" />
                @if(!$isApproved)
                    <x-button label="Save Progress" class="btn-neutral" wire:click="saveExecution" />
                    <x-button label="Approve & Complete" class="btn-success text-white" icon="o-check"
                        wire:click="approveSchedule" />
                @endif
            </x-slot:actions>
        @endif
    </x-modal>
