<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use App\Models\RollingBreak;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Mary\Traits\Toast;

new class extends Component {
    use WithPagination, Toast;

    #[Url(as: 'cari')]
    public $search = '';

    // Form states
    public bool $formModal = false;
    public ?int $recordId = null;
    public string $date_input = '';
    public string $shift = '';
    public string $break_time = '';
    public string $fullname = '';
    public string $jid_no = '';
    public string $notes = '';
    
    // Delete Confirmation State
    public bool $confirmModal = false;
    public ?int $deleteId = null;



    public function updatedSearch()
    {
        $this->resetPage();
    }

    #[Computed]
    public function usersOption()
    {
        return User::select('jid_no', 'name')
            ->whereNotNull('jid_no')
            ->orderBy('name')
            ->get();
    }

    public function updatedJidNo($value)
    {
        if ($value) {
            $user = User::where('jid_no', $value)->first();
            if ($user) {
                $this->fullname = $user->name;
            } else {
                $this->fullname = '';
            }
        }
    }

    public function create()
    {
        $this->resetValidation();
        $this->recordId = null;
        $this->date_input = now()->format('Y-m-d\TH:i');
        $this->shift = '';
        $this->break_time = '';
        $this->fullname = '';
        $this->jid_no = '';
        $this->notes = '';
        $this->formModal = true;
    }

    public function edit(RollingBreak $record)
    {
        $this->resetValidation();
        $this->recordId = $record->id;
        $this->date_input = $record->date_input ? $record->date_input->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i');
        $this->shift = $record->shift;
        $this->break_time = $record->break_time;
        $this->fullname = $record->fullname;
        $this->jid_no = $record->jid_no;
        $this->notes = $record->notes ?? '';
        $this->formModal = true;
    }

    public function save()
    {
        $this->validate([
            'date_input' => 'required|date',
            'shift' => 'required|string',
            'break_time' => 'required|string',
            'jid_no' => 'required|exists:users,jid_no',
            'notes' => 'nullable|string',
        ]);

        // Pastikan nama user terisi
        if(empty($this->fullname)) {
            $user = User::where('jid_no', $this->jid_no)->first();
            $this->fullname = $user ? $user->name : '';
        }

        $data = [
            'date_input' => Carbon::parse($this->date_input)->format('Y-m-d H:i:s'),
            'shift' => $this->shift,
            'break_time' => $this->break_time,
            'fullname' => $this->fullname,
            'jid_no' => $this->jid_no,
            'notes' => $this->notes,
        ];

        if ($this->recordId) {
            RollingBreak::where('id', $this->recordId)->update($data);
            $this->success('Data Rolling Break berhasil diperbarui.');
        } else {
            RollingBreak::create($data);
            $this->success('Data Rolling Break berhasil ditambahkan.');
        }

        $this->formModal = false;
    }

    public function confirmDelete(int $id)
    {
        $this->deleteId = $id;
        $this->confirmModal = true;
    }

    public function executeDelete()
    {
        if ($this->deleteId) {
            RollingBreak::destroy($this->deleteId);
            $this->success('Data Rolling Break berhasil dihapus.');
        }
        
        $this->confirmModal = false;
        $this->deleteId = null;
    }

    #[Computed]
    public function headers(): array
    {
        return [
            ['key' => 'date_input', 'label' => 'Date Time', 'class' => 'w-32 whitespace-nowrap'],
            ['key' => 'shift', 'label' => 'Shift', 'class' => 'w-24 whitespace-nowrap'],
            ['key' => 'break_time', 'label' => 'Break Time', 'class' => 'w-24 whitespace-nowrap'],
            ['key' => 'fullname', 'label' => 'Fullname', 'class' => 'w-48 whitespace-nowrap'],
            ['key' => 'jid_no', 'label' => 'JID No', 'class' => 'w-32 whitespace-nowrap'],
            ['key' => 'notes', 'label' => 'Notes', 'class' => 'w-64'],
            ['key' => 'actions', 'label' => 'Aksi', 'class' => 'w-24 text-center'],
        ];
    }

    #[Computed]
    public function records()
    {
        $query = RollingBreak::orderBy('date_input', 'desc')
            ->orderBy('created_at', 'desc');

        if (!empty($this->search)) {
            $query->where(function (Builder $q) {
                $q->where('fullname', 'like', '%' . $this->search . '%')
                  ->orWhere('jid_no', 'like', '%' . $this->search . '%')
                  ->orWhere('notes', 'like', '%' . $this->search . '%')
                  ->orWhere('break_time', 'like', '%' . $this->search . '%');
            });
        }

        return $query->paginate(10);
    }
    
    // Shift Data for Form
    #[Computed]
    public function breakTimesData()
    {
        return [
            "1" => [
                ['id' => '10:00', 'name' => '10:00'],
                ['id' => '11:35', 'name' => '11:35'],
                ['id' => '12:00', 'name' => '12:00'],
                ['id' => '15:30', 'name' => '15:30'],
                ['id' => '18:00', 'name' => '18:00']
            ],
            "2" => [
                ['id' => '22:00', 'name' => '22:00'],
                ['id' => '23:30', 'name' => '23:30'],
                ['id' => '23:45', 'name' => '23:45'],
                ['id' => '02:50', 'name' => '02:50'],
                ['id' => '03:05', 'name' => '03:05']
            ]
        ];
    }

    #[Computed]
    public function breakTimeOptions()
    {
        if (empty($this->shift)) return [];
        return $this->breakTimesData()[$this->shift] ?? [];
    }

    public function updatedShift()
    {
        $this->break_time = '';
    }
};
?>

<div>
    @include('livewire.administration.rolling-break.partials.header')
    @include('livewire.administration.rolling-break.partials.table')
    @include('livewire.administration.rolling-break.partials.form')
</div>
