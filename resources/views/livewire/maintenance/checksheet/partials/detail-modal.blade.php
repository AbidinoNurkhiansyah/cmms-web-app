<x-modal wire:model="photoModal" title="{{ $currentPhotoTitle }}" separator>
    <div class="flex justify-center p-4">
        @if($currentPhotoPath)
            <img src="{{ asset('storage/' . $currentPhotoPath) }}" alt="Checksheet Photo" class="max-w-full rounded-lg shadow-md max-h-[70vh] object-contain" />
        @else
            <x-alert icon="o-exclamation-triangle" class="alert-warning">Photo not found.</x-alert>
        @endif
    </div>
    <x-slot:actions>
        <x-button label="Close" wire:click="$set('photoModal', false)" class="btn-ghost" />
    </x-slot:actions>
</x-modal>
