<x-modal wire:model="addModal" title="Add New User" box-class="w-11/12 max-w-4xl overflow-visible" separator>
    <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
        {{-- Photo Column --}}
        <div class="md:col-span-4 flex flex-col items-center gap-4 border-r border-gray-100 pr-4">
            <div class="w-full">
                <x-file wire:model="addPhoto" accept="image/png, image/jpeg, image/webp" />
            </div>
            
            <div class="mt-2 w-40 h-40 rounded-full border-4 border-gray-50 shadow-sm overflow-hidden bg-gray-100 flex items-center justify-center">
                @if ($addPhoto)
                    <img src="{{ $addPhoto->temporaryUrl() }}" class="w-full h-full object-cover">
                @else
                    <x-icon name="o-user" class="w-16 h-16 text-gray-300" />
                @endif
            </div>
            <div class="text-xs text-gray-400 text-center">Allowed formats: PNG, JPG, WEBP</div>
        </div>

        {{-- Form Column --}}
        <div class="md:col-span-8 grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-input label="Associate Name" wire:model="addName" class="md:col-span-2" />
            <x-input label="JID No" wire:model="addJidNo" placeholder="e.g. JID00001" class="md:col-span-2" />
            <x-choices label="Position" wire:model="addPosition" :options="$positionOptions" placeholder="Select Position" single searchable clearable />
            <x-choices label="Team" wire:model="addTeam" :options="$teamOptions" placeholder="Select Team" single searchable clearable />
            <x-choices label="Unit (Jobdesc)" wire:model="addJobdesc" :options="$unitOptions" placeholder="Select a unit" single searchable clearable />
            <x-choices label="Role (System Access)" wire:model="addRole" :options="$roleOptions" placeholder="Select a Role" single searchable clearable />
        </div>
    </div>
    <x-slot:actions>
        <x-button label="Cancel" class="btn-ghost" wire:click="$set('addModal', false)" />
        <x-button label="Create User" class="btn-primary" wire:click="saveAdd" spinner="saveAdd" />
    </x-slot:actions>
</x-modal>
