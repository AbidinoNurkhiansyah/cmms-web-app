{{-- Header --}}
<x-header subtitle="JID: {{ $user->jid_no }} | Team: {{ $user->team ?: '-' }} | Position: {{ $user->position ?: '-' }}" separator>
    <x-slot:title>
        <div class="flex items-center gap-3">
            <x-button icon="o-arrow-left" link="{{ route('skills.index') }}" class="btn-circle btn-ghost btn-sm" wire:navigate />
            <span>Manage Skills: {{ $user->name }}</span>
        </div>
    </x-slot:title>
    <x-slot:actions>
        <x-button label="Add Skill" icon="o-plus" class="btn-primary" wire:click="openAdd" spinner />
    </x-slot:actions>
</x-header>
