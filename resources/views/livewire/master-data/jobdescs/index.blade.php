<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Services\JobDescriptionService;
use Mary\Traits\Toast;

new class extends Component {
    use WithPagination;
    use Toast;

    public $search = '';
    
    // Modal states
    public $addModal = false;
    public $editModal = false;
    public $deleteModal = false;

    // Form fields (Add)
    public $addTeam = '';
    public $addDescription = '';
    public $addUnits = [];

    // Form fields (Edit)
    public $editId = null;
    public $editTeam = '';
    public $editDescription = '';
    public $editUnits = [];

    // Delete state
    public $deleteId = null;
    public $jobdescToDelete = null;

    public function with(JobDescriptionService $jobDescriptionService): array
    {
        return [
            'jobdescs' => $jobDescriptionService->getPaginatedJobDescriptions(10, $this->search),
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function saveAdd(JobDescriptionService $jobDescriptionService)
    {
        $this->validate([
            'addTeam' => 'required|string|max:255',
            'addDescription' => 'required|string',
            'addUnits' => 'nullable|array',
        ]);

        try {
            $jobDescriptionService->createJobDescription([
                'team' => $this->addTeam,
                'description' => $this->addDescription,
                'units' => $this->addUnits,
            ]);

            $this->addModal = false;
            $this->reset(['addTeam', 'addDescription', 'addUnits']);
            $this->success('Job description added successfully.');
        } catch (\Exception $e) {
            $this->error('Failed to add job description.');
        }
    }

    public function openEditModal($id, JobDescriptionService $jobDescriptionService)
    {
        // Using eloquent directly here for simplicity, or we could add findById to service
        // We'll use the repository via model
        $jobdesc = \App\Models\JobDescription::findOrFail($id);
        
        $this->editId = $jobdesc->id;
        $this->editTeam = $jobdesc->team;
        $this->editDescription = $jobdesc->description;
        $this->editUnits = $jobdesc->units ?? [];
        
        $this->editModal = true;
    }

    public function saveEdit(JobDescriptionService $jobDescriptionService)
    {
        $this->validate([
            'editTeam' => 'required|string|max:255',
            'editDescription' => 'required|string',
            'editUnits' => 'nullable|array',
        ]);

        try {
            $jobDescriptionService->updateJobDescription($this->editId, [
                'team' => $this->editTeam,
                'description' => $this->editDescription,
                'units' => $this->editUnits,
            ]);

            $this->editModal = false;
            $this->success('Job description updated successfully.');
        } catch (\Exception $e) {
            $this->error('Failed to update job description.');
        }
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $jobdesc = \App\Models\JobDescription::findOrFail($id);
        $this->jobdescToDelete = $jobdesc->toArray();
        $this->deleteModal = true;
    }

    public function deleteJobDescription(JobDescriptionService $jobDescriptionService)
    {
        try {
            $jobDescriptionService->deleteJobDescription($this->deleteId);
            $this->deleteModal = false;
            $this->deleteId = null;
            $this->jobdescToDelete = null;
            $this->success('Job description deleted successfully.');
        } catch (\Exception $e) {
            $this->error('Failed to delete job description.');
        }
    }
}; ?>

<div>
    <x-header title="Master Data Jobdesc" separator>
        <x-slot:actions>
            <x-input placeholder="Search..." wire:model.live.debounce.500ms="search" icon="o-magnifying-glass" clearable />
            <x-button label="Add Jobdesc" icon="o-plus" class="btn-primary" wire:click="$set('addModal', true)" />
        </x-slot:actions>
    </x-header>

    @include('livewire.master-data.jobdescs.partials.table')
    @include('livewire.master-data.jobdescs.partials.add-modal')
    @include('livewire.master-data.jobdescs.partials.edit-modal')
    @include('livewire.master-data.jobdescs.partials.delete-modal')
</div>
