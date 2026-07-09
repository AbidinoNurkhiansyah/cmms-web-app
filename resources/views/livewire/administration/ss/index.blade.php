<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use App\Models\SuggestionSystem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Mary\Traits\Toast;

new class extends Component {
    use WithPagination, Toast;

    #[Url(as: 'bulan')]
    public $currentMonth = '';

    #[Url(as: 'cari')]
    public $search = '';

    // Form states
    public bool $ssModal = false;
    public ?int $ssId = null;
    public string $tgl = '';
    public ?int $user_id = null;
    public string $ss_title = '';
    public int $score = 0;
    
    // Delete Confirmation State
    public bool $confirmModal = false;
    public ?int $deleteId = null;

    public function mount()
    {
        if (empty($this->currentMonth)) {
            $this->currentMonth = date('Y-m');
        }
    }

    public function previousMonth()
    {
        $this->currentMonth = Carbon::parse($this->currentMonth . '-01')->subMonth()->format('Y-m');
        $this->resetPage();
    }

    public function nextMonth()
    {
        $this->currentMonth = Carbon::parse($this->currentMonth . '-01')->addMonth()->format('Y-m');
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    #[Computed]
    public function usersOption()
    {
        return User::select('id', 'name')->orderBy('name')->get();
    }

    public function create()
    {
        $this->resetValidation();
        $this->ssId = null;
        $this->tgl = date('Y-m-d');
        $this->user_id = null;
        $this->ss_title = '';
        $this->score = 0;
        $this->ssModal = true;
    }

    public function edit(SuggestionSystem $ss)
    {
        $this->resetValidation();
        $this->ssId = $ss->id;
        $this->tgl = $ss->tgl;
        $this->user_id = $ss->user_id;
        $this->ss_title = $ss->ss_title;
        $this->score = $ss->score;
        $this->ssModal = true;
    }

    public function save()
    {
        $validated = $this->validate([
            'tgl' => 'required|date',
            'user_id' => 'required|exists:users,id',
            'ss_title' => 'required|string|max:255',
            'score' => 'required|integer|min:0',
        ]);

        if ($this->ssId) {
            SuggestionSystem::where('id', $this->ssId)->update($validated);
            $this->success('Data SS berhasil diperbarui.');
        } else {
            SuggestionSystem::create($validated);
            $this->success('Data SS berhasil ditambahkan.');
        }

        $this->ssModal = false;
    }

    public function confirmDelete(int $id)
    {
        $this->deleteId = $id;
        $this->confirmModal = true;
    }

    public function executeDelete()
    {
        if ($this->deleteId) {
            SuggestionSystem::where('id', $this->deleteId)->delete();
            $this->success('Data SS berhasil dihapus.');
        }
        
        $this->confirmModal = false;
        $this->deleteId = null;
    }

    #[Computed]
    public function headers(): array
    {
        return [
            ['key' => 'tgl', 'label' => 'Tgl', 'class' => 'w-24 whitespace-nowrap'],
            ['key' => 'user.name', 'label' => 'Nama', 'class' => 'w-48 whitespace-nowrap'],
            ['key' => 'ss_title', 'label' => 'Judul', 'class' => 'w-auto'],
            ['key' => 'score', 'label' => 'Score', 'class' => 'w-20 text-center font-bold text-primary'],
            ['key' => 'actions', 'label' => 'Aksi', 'class' => 'w-24 text-center'],
        ];
    }

    #[Computed]
    public function suggestionSystems()
    {
        $awal = $this->currentMonth . '-01';
        $akhir = Carbon::parse($awal)->endOfMonth()->format('Y-m-d');

        $query = SuggestionSystem::with('user')
            ->whereBetween('tgl', [$awal, $akhir])
            ->orderBy('tgl', 'desc');

        if (!empty($this->search)) {
            $query->where(function (Builder $q) {
                $q->where('ss_title', 'like', '%' . $this->search . '%')
                  ->orWhereHas('user', function (Builder $userQuery) {
                      $userQuery->where('name', 'like', '%' . $this->search . '%');
                  });
            });
        }

        return $query->paginate(15);
    }
};
?>

<div>
    @include('livewire.administration.ss.partials.header')
    @include('livewire.administration.ss.partials.table')
    @include('livewire.administration.ss.partials.form')
</div>
