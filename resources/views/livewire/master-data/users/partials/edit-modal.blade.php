<x-modal wire:model="editModal" title="Edit User" box-class="w-11/12 max-w-4xl overflow-visible" separator>
    <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
        {{-- Photo Column --}}
        <div class="md:col-span-4 flex flex-col items-center gap-4 border-r border-gray-100 pr-4">
            <div class="w-full">
                <x-file wire:model="editPhoto" accept="image/png, image/jpeg, image/webp" />
            </div>
            
            <div class="mt-2 w-40 h-40 rounded-full border-4 border-gray-50 shadow-sm overflow-hidden bg-gray-100 flex items-center justify-center">
                @if ($editPhoto)
                    <img src="{{ $editPhoto->temporaryUrl() }}" class="w-full h-full object-cover">
                @elseif($currentPhoto)
                    <img src="{{ asset('storage/' . $currentPhoto) }}" class="w-full h-full object-cover">
                @else
                    <x-icon name="o-user" class="w-16 h-16 text-gray-300" />
                @endif
            </div>
            <div class="text-xs text-gray-400 text-center">Allowed formats: PNG, JPG, WEBP</div>
            @if($currentPhoto && !$editPhoto)
                <div class="text-xs text-center text-gray-500">
                    Current photo. Upload a new one to replace it.
                </div>
            @endif
        </div>

        {{-- Form Column --}}
        <div class="md:col-span-8 grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-input label="Associate Name" wire:model="editName" class="md:col-span-2" />
            <x-input label="JID No" wire:model="editJidNo" placeholder="e.g. JID00001" class="md:col-span-2" />
            <x-choices label="Position" wire:model="editPosition" :options="$positionOptions" placeholder="Select Position" single searchable />
            <x-choices label="Team" wire:model="editTeam" :options="$teamOptions" placeholder="Select Team" single searchable />
            <x-choices label="Unit (Jobdesc)" wire:model="editJobdesc" :options="$unitOptions" placeholder="Select a unit" class="md:col-span-2" single searchable />
        </div>
    </div>
    <x-slot:actions>
        <x-button label="Cancel" class="btn-ghost" wire:click="$set('editModal', false)" />
        <x-button label="Save Changes" class="btn-primary" wire:click="saveEdit" spinner="saveEdit" />
    </x-slot:actions>
</x-modal>
