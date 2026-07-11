<x-modal wire:model="addModal" title="New Asset" box-class="w-11/12 max-w-4xl overflow-visible" separator>
    <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
        {{-- Photo Column --}}
        <div class="md:col-span-4 flex flex-col items-center gap-4 border-r border-gray-100 pr-4">
            <div class="w-full">
                <x-file wire:model="addPhoto" accept="image/png, image/jpeg, image/webp" />
            </div>
            
            <div class="mt-2 w-full aspect-square rounded-lg border-2 border-dashed border-gray-200 overflow-hidden bg-gray-50 flex items-center justify-center">
                @if ($addPhoto)
                    <img src="{{ $addPhoto->temporaryUrl() }}" class="w-full h-full object-cover">
                @else
                    <x-icon name="o-photo" class="w-16 h-16 text-gray-300" />
                @endif
            </div>
            <div class="text-xs text-gray-400 text-center">Allowed formats: PNG, JPG, WEBP<br>Max size: 4MB</div>
        </div>

        {{-- Form Column --}}
        <div class="md:col-span-8 grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-input label="Asset No" wire:model="addAssetNo" placeholder="e.g. 13XQID014" class="md:col-span-2" />
            <x-select label="Line Name" wire:model="addLineName" :options="$lineOptions" placeholder="Select line" option-value="id" option-label="name" class="md:col-span-2" />
            <x-input label="Machine Name" wire:model="addMachineName" class="md:col-span-2" />
            <x-input label="Maker" wire:model="addMaker" class="md:col-span-2" />
            <x-select label="Rank" wire:model="addRank"
                :options="[['id'=>'A','name'=>'A'],['id'=>'B','name'=>'B'],['id'=>'C','name'=>'C'],['id'=>'D','name'=>'D']]"
                option-value="id" option-label="name" placeholder="Select rank" />
            <x-select label="Year" wire:model="addYear" :options="$yearOptions" option-value="id" option-label="name" placeholder="Select year" />
        </div>
    </div>
    <x-slot:actions>
        <x-button label="Cancel" class="btn-ghost" wire:click="$set('addModal',false)" />
        <x-button label="Submit" class="btn-primary" wire:click="saveAdd" spinner="saveAdd" />
    </x-slot:actions>
</x-modal>
