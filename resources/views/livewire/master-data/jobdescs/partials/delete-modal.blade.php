<x-modal wire:model="deleteModal" title="Confirm Delete" separator>
    <div class="py-4">
        Are you sure you want to delete this job description?
        @if($jobdescToDelete)
            <div class="mt-2 font-bold">{{ $jobdescToDelete['description'] }}</div>
            <div class="mt-1 text-sm text-gray-500">Team: {{ $jobdescToDelete['team'] }}</div>
        @endif
    </div>
    <x-slot:actions>
        <x-button label="Cancel" class="btn-ghost" wire:click="$set('deleteModal', false)" />
        <x-button label="Delete" class="btn-error" wire:click="deleteJobDescription" spinner="deleteJobDescription" />
    </x-slot:actions>
</x-modal>
