<x-card class="text-center shadow bg-base-100">
    <div class="flex flex-col items-center gap-4 py-4">
        @php
            $photoUrl = $currentPhoto
                ? Storage::url($currentPhoto)
                : 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&background=ce1818&color=fff&size=128';
        @endphp
        
        <div class="relative">
            <img src="{{ $photoUrl }}" alt="Avatar"
                 class="rounded-full w-32 h-32 object-cover border-4 border-base-200 shadow-md">
            <div class="absolute bottom-1 right-1 w-5 h-5 bg-success rounded-full border-2 border-white"></div>
        </div>

        <div>
            <h3 class="text-xl font-extrabold text-gray-800 dark:text-gray-100">{{ $name }}</h3>
            <p class="text-sm font-medium text-primary mt-1">{{ $position ?: '—' }}</p>
            <div class="mt-3 flex flex-wrap justify-center gap-2">
                <span class="badge badge-ghost font-mono">{{ $jid_no ?: 'No JID' }}</span>
                <span class="badge badge-ghost">{{ $team ?: 'No Team' }}</span>
            </div>
        </div>

        <div class="mt-2 w-full px-6">
            <x-button label="Edit Profile" icon="o-pencil-square" class="btn-outline w-full" wire:click="$set('editModal', true)" />
        </div>
    </div>
</x-card>
