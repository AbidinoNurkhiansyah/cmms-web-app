<x-modal wire:model="addModal" title="Tambah History Machine" separator class="backdrop-blur-sm" box-class="w-11/12 max-w-3xl max-h-[85vh] overflow-y-auto">
    <x-form wire:submit.prevent="saveAdd" no-separator>
        <div x-data="{ tab: 'info' }">
            <div class="w-full flex overflow-x-auto border-b border-base-content/10 mb-4 rounded-lg bg-base-200/30 p-1 gap-1">
                <button type="button" @click.prevent="tab = 'info'" :class="tab === 'info' ? 'bg-neutral text-neutral-content shadow-sm' : 'hover:bg-base-200/50 text-base-content/70'" class="flex-1 text-center font-bold py-2.5 rounded-md transition-all cursor-pointer flex justify-center items-center gap-2">
                    <x-icon name="o-information-circle" class="w-5 h-5" />
                    <span class="hidden sm:block">Info Dasar</span>
                </button>
                <button type="button" @click.prevent="tab = 'masalah'" :class="tab === 'masalah' ? 'bg-neutral text-neutral-content shadow-sm' : 'hover:bg-base-200/50 text-base-content/70'" class="flex-1 text-center font-bold py-2.5 rounded-md transition-all cursor-pointer flex justify-center items-center gap-2">
                    <x-icon name="o-exclamation-triangle" class="w-5 h-5" />
                    <span class="hidden sm:block">Detail Masalah</span>
                </button>
                <button type="button" @click.prevent="tab = 'part'" :class="tab === 'part' ? 'bg-neutral text-neutral-content shadow-sm' : 'hover:bg-base-200/50 text-base-content/70'" class="flex-1 text-center font-bold py-2.5 rounded-md transition-all cursor-pointer flex justify-center items-center gap-2">
                    <x-icon name="o-cog" class="w-5 h-5" />
                    <span class="hidden sm:block">Part Change</span>
                </button>
            </div>

            <!-- TAB 1: Info Dasar -->
            <div x-show="tab === 'info'" x-cloak x-transition>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-2">
                    <!-- Asset Selection -->
                    <div class="col-span-1 md:col-span-2 grid grid-cols-1 md:grid-cols-3 gap-2 p-2 bg-base-200/50 rounded-lg">
                        <x-choices wire:model.live="LineName" :options="$lineNames" option-label="name" option-value="name"
                            label="Line" placeholder="Pilih Line..." single searchable class="bg-base-100" />

                        <x-choices wire:model.live="MachineNo" :options="$machines" option-label="machine_name"
                            option-value="asset_no" label="Machine" placeholder="Pilih Mesin..." single searchable
                            class="bg-base-100" />

                        <x-input label="Asset No" wire:model="MachineNo" readonly class="bg-base-200/50" />
                    </div>

                    <!-- Dates -->
                    <x-input type="date" label="Tgl Berlaku" wire:model="tgl_berlaku" />
                    <x-input type="date" label="Row Date" wire:model="row_date" />
                    
                    <!-- PIC & Frequency -->
                    <x-choices wire:model="pic_id" :options="$this->users" option-label="name" option-value="id"
                        label="PIC" placeholder="Pilih PIC..." single searchable />
                    <x-input label="Frequency" wire:model="frequency" placeholder="Contoh: 1 Bulan" />
                </div>
            </div>

            <!-- TAB 2: Detail Masalah -->
            <div x-show="tab === 'masalah'" x-cloak x-transition>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-2">
                    <x-textarea label="Problem" wire:model="problem" rows="3" />
                    <x-textarea label="Cause" wire:model="cause" rows="3" />
                    <x-textarea label="Corrective Action" wire:model="corrective_action" rows="3" />
                </div>
            </div>

            <!-- TAB 3: Part Change -->
            <div x-show="tab === 'part'" x-cloak x-transition>
                <div class="mt-2 p-2 bg-base-200/50 rounded-lg">
                    <div class="flex justify-between items-center mb-2">
                        <span class="font-bold text-sm">Daftar Sparepart yang Diganti</span>
                        <x-button type="button" icon="o-plus" class="btn-xs btn-outline btn-primary" wire:click="addSparepart" label="Add Part" spinner/>
                    </div>

                    @foreach($usedSpareparts as $index => $sp)
                        <div class="grid grid-cols-12 gap-1 mb-1 items-end">
                            <div class="col-span-8">
                                <x-choices
                                    wire:model="usedSpareparts.{{ $index }}.spare_part_id"
                                    :options="$spareparts"
                                    option-label="part_name"
                                    option-value="id"
                                    placeholder="Cari sparepart..."
                                    searchable
                                    single
                                    class="bg-base-100"
                                />
                            </div>
                            <div class="col-span-3">
                                <x-input type="number" wire:model="usedSpareparts.{{ $index }}.qty" placeholder="Qty" class="bg-base-100" />
                            </div>
                            <div class="col-span-1 flex justify-end">
                                <x-button type="button" icon="o-trash" class="btn-sm btn-ghost text-error" wire:click="removeSparepart({{ $index }})" spinner/>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <x-slot:actions>
            <x-button type="button" label="Batal" wire:click="$set('addModal', false)" class="btn-ghost" />
            <x-button type="submit" label="Simpan" class="btn-primary" icon="o-check" spinner="saveAdd" />
        </x-slot:actions>
    </x-form>
</x-modal>


<x-modal wire:model="editModal" title="Ubah History Machine" separator class="backdrop-blur-sm" box-class="w-11/12 max-w-3xl max-h-[85vh] overflow-y-auto">
    <x-form wire:submit.prevent="saveEdit" no-separator>
        <div x-data="{ tab: 'info' }">
            <div class="w-full flex overflow-x-auto border-b border-base-content/10 mb-4 rounded-lg bg-base-200/30 p-1 gap-1">
                <button type="button" @click.prevent="tab = 'info'" :class="tab === 'info' ? 'bg-neutral text-neutral-content shadow-sm' : 'hover:bg-base-200/50 text-base-content/70'" class="flex-1 text-center font-bold py-2.5 rounded-md transition-all cursor-pointer flex justify-center items-center gap-2">
                    <x-icon name="o-information-circle" class="w-5 h-5" />
                    <span class="hidden sm:block">Info Dasar</span>
                </button>
                <button type="button" @click.prevent="tab = 'masalah'" :class="tab === 'masalah' ? 'bg-neutral text-neutral-content shadow-sm' : 'hover:bg-base-200/50 text-base-content/70'" class="flex-1 text-center font-bold py-2.5 rounded-md transition-all cursor-pointer flex justify-center items-center gap-2">
                    <x-icon name="o-exclamation-triangle" class="w-5 h-5" />
                    <span class="hidden sm:block">Detail Masalah</span>
                </button>
                <button type="button" @click.prevent="tab = 'part'" :class="tab === 'part' ? 'bg-neutral text-neutral-content shadow-sm' : 'hover:bg-base-200/50 text-base-content/70'" class="flex-1 text-center font-bold py-2.5 rounded-md transition-all cursor-pointer flex justify-center items-center gap-2">
                    <x-icon name="o-cog" class="w-5 h-5" />
                    <span class="hidden sm:block">Part Change</span>
                </button>
            </div>

            <!-- TAB 1: Info Dasar -->
            <div x-show="tab === 'info'" x-cloak x-transition>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-2">
                    <!-- Asset Selection -->
                    <div class="col-span-1 md:col-span-2 grid grid-cols-1 md:grid-cols-3 gap-2 p-2 bg-base-200/50 rounded-lg">
                        <x-choices wire:model.live="LineName" :options="$lineNames" option-label="name" option-value="name"
                            label="Line" placeholder="Pilih Line..." single searchable class="bg-base-100" />

                        <x-choices wire:model.live="MachineNo" :options="$machines" option-label="machine_name"
                            option-value="asset_no" label="Machine" placeholder="Pilih Mesin..." single searchable
                            class="bg-base-100" />

                        <x-input label="Asset No" wire:model="MachineNo" readonly class="bg-base-200/50" />
                    </div>

                    <!-- Dates -->
                    <x-input type="date" label="Tgl Berlaku" wire:model="tgl_berlaku" />
                    <x-input type="date" label="Row Date" wire:model="row_date" />
                    
                    <!-- PIC & Frequency -->
                    <x-choices wire:model="pic_id" :options="$this->users" option-label="name" option-value="id"
                        label="PIC" placeholder="Pilih PIC..." single searchable />
                    <x-input label="Frequency" wire:model="frequency" placeholder="Contoh: 1 Bulan" />
                </div>
            </div>

            <!-- TAB 2: Detail Masalah -->
            <div x-show="tab === 'masalah'" x-cloak x-transition>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-2">
                    <x-textarea label="Problem" wire:model="problem" rows="3" />
                    <x-textarea label="Cause" wire:model="cause" rows="3" />
                    <x-textarea label="Corrective Action" wire:model="corrective_action" rows="3" />
                </div>
            </div>

            <!-- TAB 3: Part Change -->
            <div x-show="tab === 'part'" x-cloak x-transition>
                <div class="mt-2 p-2 bg-base-200/50 rounded-lg">
                    <div class="flex justify-between items-center mb-2">
                        <span class="font-bold text-sm">Daftar Sparepart yang Diganti</span>
                        <x-button type="button" icon="o-plus" class="btn-xs btn-outline btn-primary" wire:click="addSparepart" label="Add Part" spinner/>
                    </div>

                    @foreach($usedSpareparts as $index => $sp)
                        <div class="grid grid-cols-12 gap-1 mb-1 items-end">
                            <div class="col-span-8">
                                <x-choices
                                    wire:model="usedSpareparts.{{ $index }}.spare_part_id"
                                    :options="$spareparts"
                                    option-label="part_name"
                                    option-value="id"
                                    placeholder="Cari sparepart..."
                                    searchable
                                    single
                                    class="bg-base-100"
                                />
                            </div>
                            <div class="col-span-3">
                                <x-input type="number" wire:model="usedSpareparts.{{ $index }}.qty" placeholder="Qty" class="bg-base-100" />
                            </div>
                            <div class="col-span-1 flex justify-end">
                                <x-button type="button" icon="o-trash" class="btn-sm btn-ghost text-error" wire:click="removeSparepart({{ $index }})" spinner/>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <x-slot:actions>
            <x-button type="button" label="Batal" wire:click="$set('editModal', false)" class="btn-ghost" />
            <x-button type="submit" label="Simpan Perubahan" class="btn-primary" icon="o-check" spinner="saveEdit" />
        </x-slot:actions>
    </x-form>
</x-modal>
