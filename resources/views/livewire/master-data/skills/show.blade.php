<?php

use App\Models\TrainingSkill;
use App\Models\User;
use Livewire\Volt\Component;
use Mary\Traits\Toast;

new class extends Component {
    use Toast;

    public User $user;
    
    // Form modal state
    public bool $formModal = false;
    public ?int $editId = null;
    
    public string $category = '';
    public string $skillName = '';
    public int $actualLevel = 1;
    public int $targetLevel = 4;

    // Delete modal state
    public bool $deleteModal = false;
    public ?int $deleteId = null;

    public function mount(int $id): void
    {
        $this->user = User::findOrFail($id);
    }

    public function with(): array
    {
        $headers = [
            ['key' => 'category', 'label' => 'Category'],
            ['key' => 'skill_name', 'label' => 'Skill Name'],
            ['key' => 'actual_level', 'label' => 'Actual Level'],
            ['key' => 'target_level', 'label' => 'Target Level'],
            ['key' => 'actions', 'label' => 'Actions', 'class' => 'w-24 text-center']
        ];

        // Fetch user's skills
        $skills = TrainingSkill::where('user_id', $this->user->id)
            ->orderBy('category')
            ->orderBy('skill_name')
            ->get();

        $categoryOptions = [
            ['id' => 'OFFICE', 'name' => 'Office (Matrix)'],
            ['id' => 'GENBA', 'name' => 'Genba (Matrix)'],
            ['id' => 'ELECTRICAL', 'name' => 'Electrical'],
            ['id' => 'MECHANICAL', 'name' => 'Mechanical'],
            ['id' => 'ADV ELECTRICAL', 'name' => 'Advance Electrical'],
        ];

        return [
            'skills' => $skills,
            'headers' => $headers,
            'categoryOptions' => $categoryOptions,
        ];
    }

    public function openAdd(): void
    {
        $this->reset(['editId', 'category', 'skillName', 'actualLevel', 'targetLevel']);
        $this->actualLevel = 1;
        $this->targetLevel = 4;
        $this->formModal = true;
    }

    public function openEdit(int $id): void
    {
        $skill = TrainingSkill::where('user_id', $this->user->id)->findOrFail($id);
        
        $this->editId = $id;
        $this->category = $skill->category;
        $this->skillName = $skill->skill_name;
        $this->actualLevel = $skill->actual_level;
        $this->targetLevel = $skill->target_level;
        
        $this->formModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'category' => 'required|string',
            'skillName' => 'required|string|max:255',
            'actualLevel' => 'required|integer|min:0|max:4',
            'targetLevel' => 'required|integer|min:1|max:4',
        ]);

        $actualLevel = (int) $this->actualLevel;
        $targetLevel = (int) $this->targetLevel;

        if ($actualLevel > $targetLevel) {
            $this->addError('actualLevel', 'Actual Level cannot exceed Target Level.');
            return;
        }

        $data = [
            'user_id' => $this->user->id,
            'category' => $this->category,
            'skill_name' => $this->skillName,
            'actual_level' => $actualLevel,
            'target_level' => $targetLevel,
        ];

        if ($this->editId) {
            TrainingSkill::where('user_id', $this->user->id)->findOrFail($this->editId)->update($data);
            $this->success('Skill updated successfully.');
        } else {
            TrainingSkill::create($data);
            $this->success('Skill added successfully.');
        }

        $this->formModal = false;
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId = $id;
        $this->deleteModal = true;
    }

    public function deleteSkill(): void
    {
        if ($this->deleteId) {
            TrainingSkill::where('user_id', $this->user->id)->findOrFail($this->deleteId)->delete();
            $this->deleteModal = false;
            $this->deleteId = null;
            $this->success('Skill deleted successfully.');
        }
    }
};
?>

<div>
    @include('livewire.master-data.skills.partials.show-header')
    @include('livewire.master-data.skills.partials.show-table')
    @include('livewire.master-data.skills.partials.show-form-modal')

    {{-- Delete Modal --}}
    @include('livewire.master-data.skills.partials.delete-modal')

</div>
