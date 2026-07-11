<x-modal wire:model="formModal" title="{{ $editId ? 'Edit Spare Part' : 'Add New Spare Part' }}"
    box-class="w-11/12 max-w-5xl overflow-visible" separator>
    <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
        {{-- Photo Column --}}
        <div class="md:col-span-4 flex flex-col items-center gap-4 border-r border-gray-100 pr-4">
            <div class="w-full">
                <x-file wire:model="part_photo" accept="image/png, image/jpeg, image/webp" />
            </div>

            <div
                class="mt-2 w-48 h-48 rounded-xl border-4 border-gray-50 shadow-sm overflow-hidden bg-gray-100 flex items-center justify-center">
                @if ($part_photo)
                    <img src="{{ $part_photo->temporaryUrl() }}" class="w-full h-full object-cover">
                @elseif($existing_photo)
                    <img src="{{ asset('storage/' . $existing_photo) }}" class="w-full h-full object-cover">
                @else
                    <x-icon name="o-photo" class="w-16 h-16 text-gray-300" />
                @endif
            </div>
            <div class="text-xs text-gray-400 text-center">Allowed formats: PNG, JPG, WEBP</div>
        </div>

        {{-- Form Column --}}
        <div class="md:col-span-8 grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-2">
            <x-input label="Part Name *" wire:model="part_name" class="md:col-span-2" />
            <x-input label="Part Number" wire:model="part_number" />
            <x-input label="Group" wire:model="group" />

            <x-input label="Maker" wire:model="maker" />
            <x-input label="Machine" wire:model="machine" />

            <x-input label="Rack No" wire:model="no_rack" />
            <x-input label="Initial Stock *" type="number" wire:model="last_stock" />

            <x-input label="Use Qty *" type="number" wire:model="use_qty" />
            <x-input label="Price (IDR) *" type="number" wire:model="price_idr" prefix="Rp" />

            <x-select label="Status *" :options="[['id' => 'Y', 'name' => 'Active'], ['id' => 'N', 'name' => 'Discontinued']]" wire:model="status" class="md:col-span-2" />
        </div>
    </div>

    <x-slot:actions>
        <x-button label="Cancel" class="btn-ghost" wire:click="$set('formModal', false)" />
        <x-button label="Save" class="btn-primary" wire:click="save" spinner="save" />
    </x-slot:actions>
</x-modal>