@props(['submitLabel' => 'Save Record'])

<div class="mt-4 flex justify-end gap-2">
    <x-button label="Cancel" link="{{ route('maintenance.cardty') }}"
        class="btn-ghost hover:bg-base-200 dark:hover:bg-gray-700" />
    <x-button label="{{ $submitLabel }}" wire:click="save" icon="o-check" class="btn-primary" spinner="save" />
</div>
