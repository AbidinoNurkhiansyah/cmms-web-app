<?php

use App\Services\UserService;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;

new class extends Component {
    use WithFileUploads, Toast;

    // Profile fields only
    public string $name = '';
    public string $position = '';
    public string $team = '';
    public string $jid_no = '';
    public string $jobdesc = '';
    public $photo = null;  // uploaded file
    public string $currentPhoto = '';

    public bool $editModal = false;

    public function mount(): void
    {
        $user = auth()->user();
        $this->name = $user->name ?? '';
        $this->position = $user->position ?? '';
        $this->team = $user->team ?? '';
        $this->jid_no = $user->jid_no ?? '';
        $this->jobdesc = $user->jobdesc ?? '';
        $this->currentPhoto = $user->photo ?? '';
    }

    public function saveProfile(UserService $userService): void
    {
        $rules = [
            'name' => 'required|string|max:255',
            'photo' => 'nullable|image|max:2048',
        ];

        if (auth()->user()->is_admin) {
            $rules['jid_no'] = 'required|string|max:255';
        }

        $this->validate($rules);

        $data = [
            'name' => $this->name,
            'position' => $this->position,
            'team' => $this->team,
            'jobdesc' => $this->jobdesc,
        ];

        if (auth()->user()->is_admin) {
            $data['jid_no'] = $this->jid_no;
        }

        if ($this->photo) {
            $userService->updatePhoto(auth()->id(), $this->photo);
            $this->photo = null;
            $this->currentPhoto = auth()->user()->fresh()->photo;
        }

        $userService->updateUser(auth()->id(), $data);
        $this->editModal = false;
        $this->success('Profile updated.');
    }

    public string $searchTerm = '';

    // Search method to handle x-choices searchable event
    public function search(string $value = ''): void
    {
        $this->searchTerm = strtolower($value);
    }

    public function with(
        \App\Services\JobDescriptionService $jobDescriptionService,
        \App\Services\UserActivityService $userActivityService
    ): array {
        $userId = auth()->id();

        // Options for x-choices
        $uniqueUnits = $jobDescriptionService->getUniqueUnits();
        $unitOptions = collect($uniqueUnits)->map(function ($unit) {
            return ['id' => $unit, 'name' => $unit];
        });

        $legacyPositions = ['UNIT HEAD 1', 'FUNCTION HEAD', 'STL-2', 'STL-1', 'SL-2', 'SF-1', 'MOP', 'OP', 'SL-1', 'CLERK-1', 'ST-1'];
        $dbPositions = \App\Models\User::select('position')->distinct()->pluck('position')->filter()->toArray();
        $positionOptions = collect(array_unique(array_merge($legacyPositions, $dbPositions)))->sort()->map(fn($v) => ['id' => $v, 'name' => $v])->values();

        $legacyTeams = ['TPM-OH-SM', 'MAINTENANCE', 'TPM-OH', 'MAINTENANCE A', 'MAINTENANCE B', 'TPM', 'OH', 'ADMIN', 'SUB MATERIAL'];
        $dbTeams = \App\Models\User::select('team')->distinct()->pluck('team')->filter()->toArray();
        $teamOptions = collect(array_unique(array_merge($legacyTeams, $dbTeams)))->sort()->map(fn($v) => ['id' => $v, 'name' => $v])->values();

        // Apply search filter if user is typing in the x-choices
        if (!empty($this->searchTerm)) {
            $unitOptions = $unitOptions->filter(fn($item) => str_contains(strtolower($item['name']), $this->searchTerm));
            $positionOptions = $positionOptions->filter(fn($item) => str_contains(strtolower($item['name']), $this->searchTerm));
            $teamOptions = $teamOptions->filter(fn($item) => str_contains(strtolower($item['name']), $this->searchTerm));
        }

        return [
            'userJobDescriptions' => $jobDescriptionService->getUserJobDescriptions($this->team, $this->jobdesc),
            'activityStats' => $userActivityService->getUserActivityStats($this->jid_no, $userId),
            'officeSkills' => \App\Models\TrainingSkill::where('user_id', $userId)->where('category', 'OFFICE')->get(),
            'genbaSkills' => \App\Models\TrainingSkill::where('user_id', $userId)->where('category', 'GENBA')->get(),
            'elecSkills' => \App\Models\TrainingSkill::where('user_id', $userId)->where('category', 'ELECTRICAL')->get(),
            'mechSkills' => \App\Models\TrainingSkill::where('user_id', $userId)->where('category', 'MECHANICAL')->get(),
            'advElecSkills' => \App\Models\TrainingSkill::where('user_id', $userId)->where('category', 'ADV ELECTRICAL')->get(),
            'unitOptions' => $unitOptions->toArray(),
            'positionOptions' => $positionOptions->toArray(),
            'teamOptions' => $teamOptions->toArray(),
        ];
    }
};
?>

<div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <x-header title="My Profile" subtitle="Manage your personal information and view your performance metrics."
        separator />

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-4">

        <!-- Left Sidebar: Profile Info, Training Skills -->
        <div class="xl:col-span-4 space-y-4">
            @include('livewire.profile.partials.profile-info')

            <x-card title="Training Skills" shadow class="bg-base-100">
                @include('livewire.profile.partials.training-skills')
            </x-card>
        </div>

        <!-- Right Content: Job Desc, Activity Chart, Skill Matrix -->
        <div class="xl:col-span-8 space-y-4">
            @include('livewire.profile.partials.skill-matrix', [
                'officeSkills' => $officeSkills,
                'genbaSkills' => $genbaSkills
            ])
                        @include('livewire.profile.partials.activity-chart', ['activityStats' => $activityStats])
            <x-card title="Job Description" shadow class="bg-base-100">
    @include('livewire.profile.partials.job-description')
</x-card>
            
        </div>
        
    </div>

    {{-- Edit Modal --}}
    @include('livewire.profile.partials.edit-modal')
</div>
