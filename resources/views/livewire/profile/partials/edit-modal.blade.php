<x-modal wire:model="editModal" title="Edit Profile" box-class="w-11/12 max-w-4xl overflow-visible" separator>
    <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
        {{-- Photo Column --}}
        <div class="md:col-span-4 flex flex-col items-center gap-4 border-r border-gray-100 dark:border-gray-800 pr-4">
            <div class="w-full">
                <x-file wire:model="photo" accept="image/png, image/jpeg, image/webp" />
            </div>
            
            <div class="mt-2 w-40 h-40 rounded-full border-4 border-gray-50 shadow-sm overflow-hidden bg-gray-100 flex items-center justify-center">
                @if ($photo)
                    <img src="{{ $photo->temporaryUrl() }}" class="w-full h-full object-cover">
                @elseif($currentPhoto)
                    <img src="{{ Storage::url($currentPhoto) }}" class="w-full h-full object-cover">
                @else
                    <x-icon name="o-user" class="w-16 h-16 text-gray-300" />
                @endif
            </div>
            <div class="text-xs text-gray-400 text-center">Allowed formats: PNG, JPG, WEBP<br>Max: 2MB</div>
        </div>

        {{-- Form Column --}}
        <div class="md:col-span-8 grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-input label="Associate Name" wire:model="name" class="md:col-span-2 font-medium" />
            <x-input label="JID No" wire:model="jid_no" :readonly="!auth()->user()->is_admin" class="{{ !auth()->user()->is_admin ? 'opacity-70 bg-base-200' : '' }} md:col-span-2" />
            <x-choices label="Position" wire:model="position" :options="$positionOptions" placeholder="Select Position" single searchable />
            <x-choices label="Team" wire:model="team" :options="$teamOptions" placeholder="Select Team" single searchable />
            <x-choices label="Unit (Jobdesc)" wire:model="jobdesc" :options="$unitOptions" placeholder="Select a unit" class="md:col-span-2" single searchable />
        </div>
    </div>
    
    <x-slot:actions>
        <x-button label="Cancel" class="btn-ghost" wire:click="$set('editModal', false)" />
        <x-button label="Save Changes" class="btn-primary" wire:click="saveProfile" spinner="saveProfile" />
    </x-slot:actions>
</x-modal>
