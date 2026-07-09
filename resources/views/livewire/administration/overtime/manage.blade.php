<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Overtime;
use App\Models\User;
use Carbon\Carbon;
use Mary\Traits\Toast;

new class extends Component {
    use WithPagination;
    use Toast;

    public string $search = '';
    public string $searchMonth = '';

    public bool $showModal = false;
    public bool $editMode = false;
    public ?int $editId = null;

    // Delete Modal
    public bool $deleteModal = false;
    public ?int $deleteId = null;

    // Form fields
    public ?int $user_id = null;
    public string $date = '';
    public string $hours_1 = '';
    public string $hours_2 = '';

    public function mount()
    {
        $this->searchMonth = Carbon::today()->format('Y-m');
        $this->date = Carbon::today()->format('Y-m-d');
    }

    public function with()
    {
        $query = Overtime::with('user');

        if (!empty($this->search)) {
            $query->whereHas('user', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('team', 'like', '%' . $this->search . '%');
            });
        }

        if (!empty($this->searchMonth)) {
            $query->where('date', 'like', $this->searchMonth . '%');
        }

        return [
            'overtimes' => $query->orderBy('date', 'desc')->orderBy('id', 'desc')->paginate(15),
            'users' => User::whereIn('team', ['MTC', 'PE', 'ME'])->orderBy('name')->get()
        ];
    }

    public function create()
    {
        $this->reset(['user_id', 'hours_1', 'hours_2', 'editId']);
        $this->date = Carbon::today()->format('Y-m-d');
        $this->editMode = false;
        $this->showModal = true;
    }

    public function edit($id)
    {
        $overtime = Overtime::findOrFail($id);
        $this->editId = $overtime->id;
        $this->user_id = $overtime->user_id;
        $this->date = $overtime->date;
        $this->hours_1 = $overtime->hours_1;
        $this->hours_2 = $overtime->hours_2;
        $this->editMode = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'hours_1' => 'required|numeric|min:0',
            'hours_2' => 'required|numeric|min:0',
        ]);

        if ($this->editMode && $this->editId) {
            $overtime = Overtime::findOrFail($this->editId);
            $overtime->update([
                'user_id' => $this->user_id,
                'date' => $this->date,
                'hours_1' => $this->hours_1,
                'hours_2' => $this->hours_2,
            ]);
            $this->success('Data lembur berhasil diperbarui.');
        } else {
            Overtime::create([
                'user_id' => $this->user_id,
                'date' => $this->date,
                'hours_1' => $this->hours_1,
                'hours_2' => $this->hours_2,
            ]);
            $this->success('Data lembur berhasil ditambahkan.');
        }

        $this->showModal = false;
        $this->resetPage();
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->deleteModal = true;
    }

    public function executeDelete()
    {
        if ($this->deleteId) {
            $overtime = Overtime::findOrFail($this->deleteId);
            $overtime->delete();
            $this->success('Data lembur berhasil dihapus.');
            $this->deleteModal = false;
            $this->deleteId = null;
        }
    }
}; ?>

<div>
    @include('livewire.administration.overtime.partials.manage-header')
    @include('livewire.administration.overtime.partials.manage-filters')
    @include('livewire.administration.overtime.partials.manage-table')
    @include('livewire.administration.overtime.partials.manage-modal')
    @include('livewire.administration.overtime.partials.manage-delete-modal')
</div>