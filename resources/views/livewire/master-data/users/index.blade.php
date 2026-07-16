<?php

use App\Services\UserService;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;

new class extends Component {
    use WithPagination, Toast, WithFileUploads;

    public string $search = '';

    // Delete modal state
    public bool $deleteModal = false;
    public ?int $deleteId = null;

    // Edit modal state
    public bool $editModal = false;
    public ?int $editId = null;
    public string $editName = '';
    public string $editJidNo = '';
    public ?string $editPosition = '';
    public ?string $editTeam = '';
    public ?string $editJobdesc = '';
    public ?string $editRole = '';
    public $editPhoto;
    public string $currentPhoto = '';

    // Add modal state
    public bool $addModal = false;
    public string $addName = '';
    public string $addJidNo = '';
    public ?string $addPosition = '';
    public ?string $addTeam = '';
    public ?string $addJobdesc = '';
    public ?string $addRole = '';
    public $addPhoto;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId = $id;
        $this->deleteModal = true;
    }

    public function with(UserService $userService, \App\Services\JobDescriptionService $jobDescriptionService): array
    {
        $uniqueUnits = $jobDescriptionService->getUniqueUnits();

        $unitOptions = collect($uniqueUnits)->map(function ($unit) {
            return ['id' => $unit, 'name' => $unit];
        })->toArray();

        // Generate options from existing DB values combined with known legacy values
        $legacyPositions = ['UNIT HEAD 1', 'FUNCTION HEAD', 'STL-2', 'STL-1', 'SL-2', 'SF-1', 'MOP', 'OP', 'SL-1', 'CLERK-1', 'ST-1'];
        $dbPositions = \App\Models\User::select('position')->distinct()->pluck('position')->filter()->toArray();
        $positionOptions = collect(array_unique(array_merge($legacyPositions, $dbPositions)))->sort()->map(fn($v) => ['id' => $v, 'name' => $v])->values()->toArray();

        $legacyTeams = ['TPM-OH-SM', 'MAINTENANCE', 'TPM-OH', 'MAINTENANCE A', 'MAINTENANCE B', 'TPM', 'OH', 'ADMIN', 'SUB MATERIAL'];
        $dbTeams = \App\Models\User::select('team')->distinct()->pluck('team')->filter()->toArray();
        $teamOptions = collect(array_unique(array_merge($legacyTeams, $dbTeams)))->sort()->map(fn($v) => ['id' => $v, 'name' => $v])->values()->toArray();

        $roleOptions = \Spatie\Permission\Models\Role::all()->map(fn($role) => ['id' => $role->name, 'name' => str_replace('Maintenance', 'MTC', ucfirst(str_replace('_', ' ', $role->name)))])->toArray();

        return [
            'users' => $userService->getPaginatedUsers(25, $this->search),
            'unitOptions' => $unitOptions,
            'positionOptions' => $positionOptions,
            'teamOptions' => $teamOptions,
            'roleOptions' => $roleOptions,
        ];
    }

    public function openEdit(int $id, UserService $userService): void
    {
        $user = $userService->getUserById($id);
        $this->editId = $id;
        $this->editName = $user->name ?? '';
        $this->editJidNo = $user->jid_no ?? '';
        $this->editPosition = $user->position ?? '';
        $this->editTeam = $user->team ?? '';
        $this->editJobdesc = $user->jobdesc ?? '';
        $this->editRole = $user->roles->first()?->name ?? '';
        $this->currentPhoto = $user->photo ?? '';
        $this->editPhoto = null;
        $this->editModal = true;
    }

    public function saveEdit(UserService $userService): void
    {
        $this->validate([
            'editName' => 'required|string|max:255',
            'editJidNo' => 'required|string|max:20|unique:users,jid_no,' . $this->editId,
        ]);

        $data = [
            'name' => $this->editName,
            'username' => $this->editName, // Sync username with name
            'jid_no' => $this->editJidNo,
            'position' => $this->editPosition,
            'team' => $this->editTeam,
            'jobdesc' => $this->editJobdesc,
        ];

        if ($this->editPhoto) {
            $data['photo'] = $this->editPhoto->store('users', 'public');
        }

        $user = $userService->updateUser($this->editId, $data);
        
        // Sync Roles
        if ($this->editRole) {
            $user->syncRoles([$this->editRole]);
        } else {
            $user->syncRoles([]);
        }

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
        $this->reset(['addName', 'addJidNo', 'addPosition', 'addTeam', 'addJobdesc', 'addPhoto', 'addRole']);
        $this->addModal = true;
    }

    public function saveAdd(UserService $userService): void
    {
        $this->validate([
            'addName' => 'required|string|max:255|unique:users,username',
            'addJidNo' => 'required|string|max:20|unique:users,jid_no',
        ]);

        $data = [
            'name' => $this->addName,
            'username' => $this->addName,
            'email' => strtolower($this->addJidNo) . '@cmms.local',
            'password' => $this->addJidNo,
            'jid_no' => $this->addJidNo,
            'position' => $this->addPosition,
            'team' => $this->addTeam,
            'jobdesc' => $this->addJobdesc,
        ];

        if ($this->addPhoto) {
            $data['photo'] = $this->addPhoto->store('users', 'public');
        }

        $user = $userService->createUser($data);
        
        if ($this->addRole) {
            $user->assignRole($this->addRole);
        }

        $this->addModal = false;
        $this->success('User created successfully.');
    }

    public function deleteUser(UserService $userService): void
    {
        if ($this->deleteId) {
            $userService->deleteUser($this->deleteId);
            $this->deleteModal = false;
            $this->deleteId = null;
            $this->success('User deleted successfully.');
        }
    }
};
?>

<div>
    <x-header title="User Management" icon="o-users" separator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Search name, JID, username..." wire:model.live.debounce.300ms="search" clearable
                icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            @if(auth()->user()?->is_admin)
                <x-button label="Add User" icon="o-plus" class="btn-primary" wire:click="openAdd" spinner />
            @endif
        </x-slot:actions>
    </x-header>

    @include('livewire.master-data.users.partials.table')

    {{-- Edit Modal --}}
    @include('livewire.master-data.users.partials.edit-modal')

    {{-- Add Modal --}}
    @include('livewire.master-data.users.partials.add-modal')

    {{-- Delete Modal --}}
    @include('livewire.master-data.users.partials.delete-modal')
</div>