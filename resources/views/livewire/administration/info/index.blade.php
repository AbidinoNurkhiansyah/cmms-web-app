<?php

use App\Models\Information;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;

new class extends Component {
    use WithPagination, WithFileUploads, Toast;

    public string $search = '';

    // Modals
    public bool $addModal = false;
    public bool $editModal = false;
    public bool $deleteModal = false;

    // Form Fields
    public ?int $formId = null;
    public ?int $deleteId = null;
    public string $date = '';
    public ?int $user_id = null;
    public string $source = '';
    public string $title = '';
    
    // File Uploads
    public $file_path = null;
    public $edit_file_path = null;
    public ?string $old_file_path = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function with(): array
    {
        $query = Information::with('user')
            ->when($this->search, function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('source', 'like', '%' . $this->search . '%')
                  ->orWhereHas('user', function($userQ) {
                      $userQ->where('name', 'like', '%' . $this->search . '%');
                  });
            })
            ->orderBy('date', 'desc');

        return [
            'records' => $query->paginate(15),
            'users' => User::select('id', 'name')->orderBy('name')->get(),
        ];
    }

    public function openAdd(): void
    {
        $this->reset(['formId', 'date', 'user_id', 'source', 'title', 'file_path', 'edit_file_path', 'old_file_path']);
        $this->date = date('Y-m-d');
        $this->user_id = auth()->id();
        $this->addModal = true;
    }

    public function saveAdd(): void
    {
        $this->validate([
            'date'      => 'required|date',
            'user_id'   => 'required|exists:users,id',
            'source'    => 'required|string|max:255',
            'title'     => 'required|string|max:255',
            'file_path' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
        ]);

        $path = null;
        if ($this->file_path) {
            $path = $this->file_path->store('information', 'public');
        }

        Information::create([
            'date'      => $this->date,
            'user_id'   => $this->user_id,
            'source'    => $this->source,
            'title'     => $this->title,
            'file_path' => $path,
        ]);

        $this->addModal = false;
        $this->success('Information Record Created.');
    }

    public function openEdit(int $id): void
    {
        $record = Information::find($id);
        if (!$record) {
            $this->error('Record not found.');
            return;
        }

        $this->formId        = $record->id;
        $this->date          = $record->date ? $record->date->format('Y-m-d') : '';
        $this->user_id       = $record->user_id;
        $this->source        = $record->source ?? '';
        $this->title         = $record->title ?? '';
        $this->old_file_path = $record->file_path;
        $this->file_path     = null;
        $this->edit_file_path = null;
        
        $this->editModal     = true;
    }

    public function saveEdit(): void
    {
        $this->validate([
            'date'      => 'required|date',
            'user_id'   => 'required|exists:users,id',
            'source'    => 'required|string|max:255',
            'title'     => 'required|string|max:255',
            'edit_file_path' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $record = Information::findOrFail($this->formId);

        $path = $record->file_path;
        if ($this->edit_file_path) {
            if ($path && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
            $path = $this->edit_file_path->store('information', 'public');
        }

        $record->update([
            'date'      => $this->date,
            'user_id'   => $this->user_id,
            'source'    => $this->source,
            'title'     => $this->title,
            'file_path' => $path,
        ]);

        $this->editModal = false;
        $this->success('Information Record Updated.');
    }

    public function openDelete(int $id): void
    {
        $this->deleteId = $id;
        $this->deleteModal = true;
    }

    public function deleteRecord(): void
    {
        if (!$this->deleteId) return;

        $record = Information::find($this->deleteId);
        if ($record) {
            if ($record->file_path && Storage::disk('public')->exists($record->file_path)) {
                Storage::disk('public')->delete($record->file_path);
            }
            $record->delete();
            $this->success('Record deleted.');
        } else {
            $this->error('Record not found.');
        }

        $this->deleteModal = false;
        $this->deleteId = null;
    }
};
?>

<div>
    @include('livewire.administration.info.partials.header')
    @include('livewire.administration.info.partials.table')
    @include('livewire.administration.info.partials.modals')
</div>
