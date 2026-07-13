            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-4">
                {{-- Left Column: Request Details (Readonly) --}}
                <div class="flex flex-col gap-3">
                    <div>
                        <h3 class="font-bold text-base-content/70 flex items-center gap-2 mb-2">
                            <x-icon name="o-document-text" class="w-5 h-5" /> Request Information
                        </h3>
                        <div class="grid grid-cols-2 gap-x-3 gap-y-2">
                            <x-input label="Order Date" type="date" wire:model="editForm.date" />
                            <x-input label="Target Date" type="date" wire:model="editForm.target_date" />
                            <x-input label="Requester" wire:model="editForm.requester" />
                            <x-select label="Department" wire:model="editForm.department" :options="$this->departments"
                                option-value="id" option-label="name" placeholder="Select Dept" />
                            <x-select label="Order Type" wire:model="editForm.order_type"
                                :options="[['id' => 'Install', 'name' => 'Install'], ['id' => 'Repair', 'name' => 'Repair'], ['id' => 'Kaizen', 'name' => 'Kaizen']]"
                                option-value="id" option-label="name" placeholder="Select type" />
                            <x-select label="Priority" wire:model="editForm.priority"
                                :options="[['id' => 'Low', 'name' => 'Low'], ['id' => 'Normal', 'name' => 'Normal'], ['id' => 'High', 'name' => 'High'], ['id' => 'Critical', 'name' => 'Critical']]"
                                option-value="id" option-label="name" placeholder="Select priority" />
                        </div>
                    </div>

                    <div>
                        <h3 class="font-bold text-base-content/70 flex items-center gap-2 mb-2">
                            <x-icon name="o-cog" class="w-5 h-5" /> Machine Details
                        </h3>
                        <div class="grid grid-cols-2 gap-x-3 gap-y-2 bg-base-200/50 p-4 rounded-lg">
                            <div class="col-span-2">
                                <x-choices label="Line Name" wire:model.live="LineName" :options="$lineNames"
                                    option-value="name" option-label="name" single searchable />
                            </div>
                            <x-choices label="Machine" wire:model.live="asset_id" :options="$machines" option-value="id"
                                option-label="machine_name" single searchable />
                            <x-input label="Machine No (Asset)" wire:model="MachineNo" readonly class="bg-base-200/50" />

                            <div class="col-span-2">
                                <x-textarea label="Problem Description" wire:model="editForm.problem" rows="2" />
                            </div>

                            <div class="col-span-2">
                                <x-file wire:model="editForm.foto_req" label="Request Photo" accept="image/*" />
                                @if($editForm->foto_req)
                                    <img src="{{ $editForm->foto_req->temporaryUrl() }}"
                                        class="mt-2 max-h-40 w-full object-cover rounded-lg shadow-sm border border-base-300">
                                @elseif($editForm->existing_foto_req)
                                    <img src="{{ Storage::url($editForm->existing_foto_req) }}" alt="Request Photo"
                                        class="mt-2 max-h-40 w-full object-cover rounded-lg shadow-sm border border-base-300">
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Column: Processing & Execution --}}
                <div class="flex flex-col gap-4">
                    <div>
                        <h3 class="font-bold text-primary flex items-center gap-2 mb-2">
                            <x-icon name="o-wrench-screwdriver" class="w-5 h-5" /> Action Taken
                        </h3>
                        <div class="grid grid-cols-2 gap-x-3 gap-y-2">
                            <div class="col-span-2">
                                <x-textarea label="Confirmation Note" wire:model="editForm.confirmation_note"
                                    rows="3" placeholder="Describe the action taken..." />
                            </div>

                            <div>
                                <x-file wire:model="editForm.foto_confirm1" label="Photo 1" accept="image/*" />
                                @if($editForm->foto_confirm1)
                                    <img src="{{ $editForm->foto_confirm1->temporaryUrl() }}"
                                        class="mt-2 max-h-24 object-cover rounded-lg w-full border border-base-200" />
                                @elseif($editForm->existing_foto_confirm1)
                                    <img src="{{ Storage::url($editForm->existing_foto_confirm1) }}"
                                        class="mt-2 max-h-24 object-cover rounded-lg w-full border border-base-200" />
                                @endif
                            </div>
                            <div>
                                <x-file wire:model="editForm.foto_confirm2" label="Photo 2" accept="image/*" />
                                @if($editForm->foto_confirm2)
                                    <img src="{{ $editForm->foto_confirm2->temporaryUrl() }}"
                                        class="mt-2 max-h-24 object-cover rounded-lg w-full border border-base-200" />
                                @elseif($editForm->existing_foto_confirm2)
                                    <img src="{{ Storage::url($editForm->existing_foto_confirm2) }}"
                                        class="mt-2 max-h-24 object-cover rounded-lg w-full border border-base-200" />
                                @endif
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="font-bold text-primary flex items-center gap-2 mb-2">
                            <x-icon name="o-user-group" class="w-5 h-5" /> Status & Team
                        </h3>
                        <div class="grid grid-cols-2 gap-x-3 gap-y-2">
                            <x-select label="Status" wire:model="editForm.status"
                                :options="[['id' => 'Open', 'name' => 'Open'], ['id' => 'In Progress', 'name' => 'In Progress'], ['id' => 'Done', 'name' => 'Done']]" option-value="id" option-label="name" />

                            <x-input label="Actual Date" type="date" wire:model="editForm.actual_date" />

                            <div class="col-span-2">
                                <x-choices label="Team" wire:model.live="editForm.pic" :options="$this->teamOptions"
                                    option-value="id" option-label="name" placeholder="Select Team" single searchable />
                            </div>

                            <div class="col-span-2 grid grid-cols-3 gap-x-2">
                                <x-choices label="PIC 1" wire:model="editForm.pic1" :options="$users" option-value="name" option-label="name" single searchable search-function="searchUser" />
                                <x-choices label="PIC 2" wire:model="editForm.pic2" :options="$users" option-value="name" option-label="name" single searchable search-function="searchUser" />
                                <x-choices label="PIC 3" wire:model="editForm.pic3" :options="$users" option-value="name" option-label="name" single searchable search-function="searchUser" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <x-button label="Cancel" class="btn-ghost" link="{{ route('work-orders.index') }}" wire:navigate />
                <x-button label="Save Process" icon="o-check" class="btn-primary" wire:click="saveEdit" spinner="saveEdit" />
            </div>
