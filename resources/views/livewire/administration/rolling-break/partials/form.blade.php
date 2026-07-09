<x-modal wire:model="formModal" title="{{ $recordId ? 'Edit Data' : 'Tambah Data Rolling Break' }}" persistent class="backdrop-blur">
    <x-form wire:submit="save">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-datetime 
                label="Date Time" 
                wire:model="date_input" 
                icon="o-calendar" 
                type="datetime-local" 
                class="col-span-1 md:col-span-2"
                required 
            />

            <x-select 
                label="Shift" 
                wire:model.live="shift" 
                :options="[['id' => '1', 'name' => 'Shift 1'], ['id' => '2', 'name' => 'Shift 2']]" 
                placeholder="Select Shift" 
                required 
            />

            <x-select 
                label="Break Time" 
                wire:model="break_time" 
                :options="$this->breakTimeOptions" 
                placeholder="Select Time" 
                required 
            />

            <x-choices
                label="Fullname"
                wire:model.live="jid_no"
                :options="$this->usersOption"
                option-value="jid_no"
                option-label="name"
                placeholder="Search User..."
                single
                searchable
                required
            />

            <x-input 
                label="JID No" 
                wire:model="jid_no" 
                readonly 
                disabled
            />

            <x-textarea
                label="Notes"
                wire:model="notes"
                placeholder="Reason for late break..."
                rows="2"
                class="col-span-1 md:col-span-2"
            />
        </div>

        <x-slot:actions>
            <x-button label="Batal" wire:click="$set('formModal', false)" class="btn-ghost" />
            <x-button type="submit" label="{{ $recordId ? 'Simpan Perubahan' : 'Simpan' }}" class="btn-primary" spinner="save" />
        </x-slot:actions>
    </x-form>
</x-modal>
