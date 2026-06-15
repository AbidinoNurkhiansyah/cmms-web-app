<?php

use App\Services\UserService;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;

new class extends Component {
    use WithFileUploads, Toast;

    public string $name        = '';
    public string $position    = '';
    public string $team        = '';
    public string $jid_no      = '';
    public string $jobdesc     = '';
    public string $rank        = '';
    public $photo              = null;  // uploaded file
    public string $currentPhoto = '';

    // Password change
    public bool   $passwordModal    = false;
    public string $currentPassword  = '';
    public string $newPassword      = '';
    public string $confirmPassword  = '';

    public function mount(): void
    {
        $user = auth()->user();
        $this->name         = $user->name        ?? '';
        $this->position     = $user->position    ?? '';
        $this->team         = $user->team        ?? '';
        $this->jid_no       = $user->jid_no      ?? '';
        $this->jobdesc      = $user->jobdesc     ?? '';
        $this->rank         = $user->rank        ?? '';
        $this->currentPhoto = $user->photo       ?? '';
    }

    public function saveProfile(UserService $userService): void
    {
        $this->validate([
            'name'  => 'required|string|max:255',
            'photo' => 'nullable|image|max:2048',
        ]);

        $data = [
            'name'     => $this->name,
            'position' => $this->position,
            'team'     => $this->team,
            'jobdesc'  => $this->jobdesc,
            'rank'     => $this->rank,
        ];

        if ($this->photo) {
            $userService->updatePhoto(auth()->id(), $this->photo);
            $this->photo = null;
            $this->currentPhoto = auth()->user()->fresh()->photo;
        }

        $userService->updateUser(auth()->id(), $data);
        $this->success('Profile updated.');
    }

    public function openPasswordModal(): void
    {
        $this->reset(['currentPassword', 'newPassword', 'confirmPassword']);
        $this->passwordModal = true;
    }

    public function changePassword(UserService $userService): void
    {
        $this->validate([
            'currentPassword' => 'required',
            'newPassword'     => 'required|min:3|max:15|same:confirmPassword',
        ]);

        if (!\Hash::check($this->currentPassword, auth()->user()->password)) {
            $this->addError('currentPassword', 'Current password is incorrect.');
            return;
        }

        $userService->changePassword(auth()->id(), $this->newPassword);
        $this->passwordModal = false;
        $this->success('Password changed successfully.');
    }
};
?>

<div>
    <x-header title="My Profile" separator />

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Profile Card --}}
        <x-card class="lg:col-span-1 text-center">
            <div class="flex flex-col items-center gap-3 py-4">
                @php
                    $photoUrl = $currentPhoto
                        ? Storage::url($currentPhoto)
                        : 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&background=ce1818&color=fff&size=128';
                @endphp
                <img src="{{ $photoUrl }}" alt="Avatar"
                     class="rounded-full w-28 h-28 object-cover border-4 border-error shadow">

                <div>
                    <h3 class="text-lg font-bold">{{ $name }}</h3>
                    <p class="text-sm opacity-60">{{ $jid_no ?: '—' }}</p>
                    <p class="text-sm opacity-60">{{ $position ?: '—' }}</p>
                </div>

                <x-button label="Change Password" icon="o-key" class="btn-outline btn-sm"
                    wire:click="openPasswordModal" />
            </div>
        </x-card>

        {{-- Edit Form --}}
        <x-card title="Edit Profile" class="lg:col-span-2">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-input label="Associate Name" wire:model="name" class="md:col-span-2" />
                <x-input label="JID No" wire:model="jid_no" readonly class="opacity-60" />
                <x-input label="Position" wire:model="position" />
                <x-input label="Team" wire:model="team" />
                <x-input label="Unit (Jobdesc)" wire:model="jobdesc" />
                <x-input label="MTC Rank" wire:model="rank" />

                {{-- Photo upload --}}
                <div class="md:col-span-2">
                    <label class="label text-sm font-semibold">Profile Photo</label>
                    <input type="file" wire:model="photo" accept="image/*"
                           class="file-input file-input-bordered file-input-sm w-full" />
                    @error('photo') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <x-slot:actions>
                <x-button label="Save Profile" icon="o-check" class="btn-primary"
                    wire:click="saveProfile" spinner="saveProfile" />
            </x-slot:actions>
        </x-card>
    </div>

    {{-- Change Password Modal --}}
    <x-modal wire:model="passwordModal" title="Change Password" separator>
        <div class="space-y-3">
            <x-input label="Current Password" wire:model="currentPassword" type="password" />
            @error('currentPassword') <p class="text-error text-xs -mt-2">{{ $message }}</p> @enderror
            <x-input label="New Password" wire:model="newPassword" type="password" placeholder="3–15 characters" />
            <x-input label="Confirm New Password" wire:model="confirmPassword" type="password" />
        </div>
        <x-slot:actions>
            <x-button label="Cancel" class="btn-ghost" wire:click="$set('passwordModal', false)" />
            <x-button label="Save" class="btn-primary" wire:click="changePassword" spinner="changePassword" />
        </x-slot:actions>
    </x-modal>
</div>
