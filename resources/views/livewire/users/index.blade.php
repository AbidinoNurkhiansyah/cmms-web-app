<?php

use App\Services\UserService;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;

new class extends Component {
    use WithPagination, Toast;

    public string $search = '';

    // Edit modal state
    public bool $editModal    = false;
    public ?int $editId       = null;
    public string $editName   = '';
    public string $editUsername = '';
    public string $editPosition = '';
    public string $editTeam   = '';
    public string $editJobdesc = '';
    public string $editRank   = '';
    public string $editRepair = '';
    public string $newPassword = '';
    public string $confirmPassword = '';

    // Add modal state
    public bool $addModal     = false;
    public string $addName    = '';
    public string $addUsername = '';
    public string $addEmail   = '';
    public string $addPassword = '';
    public string $addJidNo   = '';
    public string $addPosition = '';
    public string $addTeam    = '';
    public string $addJobdesc = '';
    public string $addRank    = '';
    public string $addRepair  = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function with(UserService $userService): array
    {
        return [
            'users' => $userService->getPaginatedUsers(25, $this->search),
        ];
    }

    public function openEdit(int $id, UserService $userService): void
    {
        $user = $userService->getUserById($id);
        $this->editId       = $id;
        $this->editName     = $user->name ?? '';
        $this->editUsername = $user->username ?? '';
        $this->editPosition = $user->position ?? '';
        $this->editTeam     = $user->team ?? '';
        $this->editJobdesc  = $user->jobdesc ?? '';
        $this->editRank     = $user->rank ?? '';
        $this->editRepair   = $user->repair ?? '';
        $this->newPassword  = '';
        $this->confirmPassword = '';
        $this->editModal    = true;
    }

    public function saveEdit(UserService $userService): void
    {
        $this->validate([
            'editName'     => 'required|string|max:255',
            'editUsername' => 'required|string|max:100',
            'newPassword'  => 'nullable|min:3|max:15|same:confirmPassword',
        ]);

        $data = [
            'name'     => $this->editName,
            'username' => $this->editUsername,
            'position' => $this->editPosition,
            'team'     => $this->editTeam,
            'jobdesc'  => $this->editJobdesc,
            'rank'     => $this->editRank,
            'repair'   => $this->editRepair,
        ];

        if (!empty($this->newPassword)) {
            $data['password'] = $this->newPassword;
        }

        $userService->updateUser($this->editId, $data);
        $this->editModal = false;
        $this->success('User updated successfully.');
    }

    public function toggleStatus(int $id, string $currentStatus, UserService $userService): void
    {
        $newStatus = $currentStatus === 'Active' ? 'Not Active' : 'Active';
        $userService->updateStatus($id, $newStatus);
        $this->success("Status changed to {$newStatus}.");
    }

    public function openAdd(): void
    {
        $this->reset(['addName','addUsername','addEmail','addPassword','addJidNo','addPosition','addTeam','addJobdesc','addRank','addRepair']);
        $this->addModal = true;
    }

    public function saveAdd(UserService $userService): void
    {
        $this->validate([
            'addName'     => 'required|string|max:255',
            'addUsername' => 'required|string|max:100|unique:users,username',
            'addEmail'    => 'required|email|unique:users,email',
            'addPassword' => 'required|min:3|max:15',
            'addJidNo'    => 'nullable|string|max:20|unique:users,jid_no',
        ]);

        $userService->createUser([
            'name'     => $this->addName,
            'username' => $this->addUsername,
            'email'    => $this->addEmail,
            'password' => $this->addPassword,
            'jid_no'   => $this->addJidNo ?: null,
            'position' => $this->addPosition,
            'team'     => $this->addTeam,
            'jobdesc'  => $this->addJobdesc,
            'rank'     => $this->addRank,
            'repair'   => $this->addRepair,
        ]);

        $this->addModal = false;
        $this->success('User created successfully.');
    }
};
?>

<div>
    <x-header title="User Management" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Search name, JID, username..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            @if(auth()->user()?->is_admin)
                <x-button label="Add User" icon="o-plus" class="btn-primary" wire:click="openAdd" />
            @endif
        </x-slot:actions>
    </x-header>

    <x-card>
        <x-table
            :headers="[
                ['key' => 'jid_no',   'label' => 'JID No'],
                ['key' => 'name',     'label' => 'Associate Name'],
                ['key' => 'position', 'label' => 'Position'],
                ['key' => 'team',     'label' => 'Team'],
                ['key' => 'status',   'label' => 'Status'],
            ]"
            :rows="$users"
            with-pagination
        >
            @scope('cell_status', $user)
                @if(auth()->user()?->is_admin)
                    <x-button
                        label="{{ $user->status }}"
                        class="{{ $user->status === 'Active' ? 'btn-success' : 'btn-ghost' }} btn-xs"
                        wire:click="toggleStatus({{ $user->id }}, '{{ $user->status }}')"
                        wire:confirm="Change status to {{ $user->status === 'Active' ? 'Not Active' : 'Active' }}?"
                    />
                @else
                    <x-badge label="{{ $user->status }}" class="{{ $user->status === 'Active' ? 'badge-success' : 'badge-ghost' }}" />
                @endif
            @endscope

            @scope('actions', $user)
                @if(auth()->user()?->is_admin)
                    <x-button icon="o-pencil-square" class="btn-ghost btn-xs" wire:click="openEdit({{ $user->id }})" />
                @endif
            @endscope
        </x-table>
    </x-card>

    {{-- Edit Modal --}}
    <x-modal wire:model="editModal" title="Edit User" separator>
        <div class="grid grid-cols-2 gap-3">
            <x-input label="Associate Name" wire:model="editName" class="col-span-2" />
            <x-input label="Username" wire:model="editUsername" />
            <x-input label="Position" wire:model="editPosition" />
            <x-input label="Team" wire:model="editTeam" />
            <x-input label="Unit (Jobdesc)" wire:model="editJobdesc" />
            <x-input label="Rank (MTC)" wire:model="editRank" />
            <x-input label="Repair Team (MTC)" wire:model="editRepair" />
            <x-input label="New Password" wire:model="newPassword" type="password" placeholder="Leave blank to keep current" />
            <x-input label="Confirm Password" wire:model="confirmPassword" type="password" />
        </div>
        <x-slot:actions>
            <x-button label="Cancel" class="btn-ghost" wire:click="$set('editModal', false)" />
            <x-button label="Save Changes" class="btn-primary" wire:click="saveEdit" spinner="saveEdit" />
        </x-slot:actions>
    </x-modal>

    {{-- Add Modal --}}
    <x-modal wire:model="addModal" title="Add New User" separator>
        <div class="grid grid-cols-2 gap-3">
            <x-input label="Associate Name" wire:model="addName" class="col-span-2" />
            <x-input label="Username" wire:model="addUsername" />
            <x-input label="Email" wire:model="addEmail" type="email" />
            <x-input label="Password" wire:model="addPassword" type="password" />
            <x-input label="JID No" wire:model="addJidNo" placeholder="e.g. JID00001" />
            <x-input label="Position" wire:model="addPosition" />
            <x-input label="Team" wire:model="addTeam" />
            <x-input label="Unit (Jobdesc)" wire:model="addJobdesc" />
            <x-input label="Rank (MTC)" wire:model="addRank" />
            <x-input label="Repair Team (MTC)" wire:model="addRepair" />
        </div>
        <x-slot:actions>
            <x-button label="Cancel" class="btn-ghost" wire:click="$set('addModal', false)" />
            <x-button label="Create User" class="btn-primary" wire:click="saveAdd" spinner="saveAdd" />
        </x-slot:actions>
    </x-modal>
</div>
