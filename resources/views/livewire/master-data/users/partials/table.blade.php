<x-card>
    <x-table :headers="[
        ['key' => 'jid_no', 'label' => 'JID No'],
        ['key' => 'name', 'label' => 'Associate Name'],
        ['key' => 'position', 'label' => 'Position'],
        ['key' => 'team', 'label' => 'Team'],
        ['key' => 'status', 'label' => 'Status'],
        ['key' => 'action_col', 'label' => 'Action', 'class' => 'text-center'],
    ]" :rows="$users" with-pagination>
        @scope('cell_name', $user)
            <div class="flex items-center gap-3">
                <x-avatar :image="$user->photo ? asset('storage/' . $user->photo) : asset('images/default-avatar.png')" class="!w-10 !h-10" />
                <div class="font-bold">{{ $user->name }}</div>
            </div>
        @endscope

        @scope('cell_status', $user)
        @if(auth()->user()?->is_admin)
            <x-button label="{{ $user->status }}"
                class="btn-xs {{ $user->status === 'Active' ? 'btn-success text-white hover:brightness-110 shadow-sm' : 'btn-ghost text-gray-500 hover:text-gray-700' }}"
                wire:click="toggleStatus({{ $user->id }}, '{{ $user->status }}')"
                wire:confirm="Change status to {{ $user->status === 'Active' ? 'Not Active' : 'Active' }}?" />
        @else
            <x-badge label="{{ $user->status }}"
                class="{{ $user->status === 'Active' ? 'badge-success text-white shadow-sm' : 'badge-ghost text-gray-500' }}" />
        @endif
        @endscope

        @scope('cell_action_col', $user)
        @if(auth()->user()?->is_admin)
            <div class="flex gap-2 justify-center">
                <x-button icon="o-pencil-square"
                    class="btn-ghost btn-sm text-blue-500 hover:bg-blue-50 hover:text-blue-700 transition-all rounded-full"
                    wire:click="openEdit({{ $user->id }})" spinner />
                <x-button icon="o-trash"
                    class="btn-ghost btn-sm text-red-500 hover:bg-red-50 hover:text-red-700 transition-all rounded-full"
                    wire:click="confirmDelete({{ $user->id }})" spinner />
            </div>
        @endif
        @endscope
    </x-table>
</x-card>