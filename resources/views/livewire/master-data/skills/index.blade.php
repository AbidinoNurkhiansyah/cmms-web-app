<?php

use App\Models\User;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Illuminate\Database\Eloquent\Builder;

new class extends Component {
    use WithPagination;

    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function with(): array
    {
        $headers = [
            ['key' => 'jid_no', 'label' => 'JID No'],
            ['key' => 'name', 'label' => 'Associate Name'],
            ['key' => 'position', 'label' => 'Position'],
            ['key' => 'team', 'label' => 'Team'],
            ['key' => 'training_skills_count', 'label' => 'Total Skills', 'class' => 'text-center whitespace-nowrap']
        ];

        $users = User::withCount('trainingSkills')
            ->when($this->search, function (Builder $query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('jid_no', 'like', '%' . $this->search . '%')
                      ->orWhere('position', 'like', '%' . $this->search . '%')
                      ->orWhere('team', 'like', '%' . $this->search . '%');
            })
            ->orderBy('name')
            ->paginate(15);

        return [
            'users' => $users,
            'headers' => $headers,
        ];
    }
};
?>

<div>
    <x-header title="Skill Management" subtitle="Select an associate to manage their skills" icon="o-users" separator>
        <x-slot:middle class="!justify-end gap-2 flex">
            <x-input placeholder="Search associate name or JID..." wire:model.live.debounce.300ms="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
    </x-header>

    @include('livewire.master-data.skills.partials.table')
</div>
