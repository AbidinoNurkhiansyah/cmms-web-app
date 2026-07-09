<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use App\Models\Sky;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Mary\Traits\Toast;

new class extends Component {
    use WithPagination, WithFileUploads, Toast;

    #[Url(as: 'bulan')]
    public $currentMonth = '';

    #[Url(as: 'cari')]
    public $search = '';

    // Form states
    public bool $kytModal = false;
    public ?int $kytNo = null;
    public string $date = '';
    public ?string $userId = null;
    public string $lokasi = '';
    public string $bahaya = '';
    public string $resiko = '';
    public string $countermeasure = '';
    public $img; 
    public ?string $oldImg = null; 
    
    // Delete Confirmation State
    public bool $confirmModal = false;
    public ?int $deleteNo = null;
    
    // Detail Modal State
    public bool $detailModal = false;
    public ?int $detailNo = null;

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
        return User::select('jid_no', 'name')
            ->whereNotNull('jid_no')
            ->orderBy('name')
            ->get();
    }

    public function create()
    {
        $this->resetValidation();
        $this->kytNo = null;
        $this->date = date('Y-m-d');
        $this->userId = null;
        $this->lokasi = '';
        $this->bahaya = '';
        $this->resiko = '';
        $this->countermeasure = '';
        $this->img = null;
        $this->oldImg = null;
        $this->kytModal = true;
    }

    public function edit(Sky $kyt)
    {
        $this->resetValidation();
        $this->kytNo = $kyt->no;
        $this->date = $kyt->date ? $kyt->date->format('Y-m-d') : date('Y-m-d');
        $this->userId = $kyt->userId;
        $this->lokasi = $kyt->lokasi;
        $this->bahaya = $kyt->bahaya;
        $this->resiko = $kyt->resiko;
        $this->countermeasure = $kyt->countermeasure;
        $this->img = null;
        $this->oldImg = $kyt->img;
        $this->kytModal = true;
    }

    public function save()
    {
        $rules = [
            'date' => 'required|date',
            'userId' => 'required|exists:users,jid_no',
            'lokasi' => 'required|string|max:255',
            'bahaya' => 'required|string',
            'resiko' => 'required|string',
            'countermeasure' => 'required|string',
        ];

        if (!$this->kytNo && !$this->oldImg) {
            $rules['img'] = 'required|image|max:5120'; // 5MB Max
        } else {
            $rules['img'] = 'nullable|image|max:5120';
        }

        $this->validate($rules);

        $data = [
            'date' => $this->date,
            'userId' => $this->userId,
            'lokasi' => $this->lokasi,
            'bahaya' => $this->bahaya,
            'resiko' => $this->resiko,
            'countermeasure' => $this->countermeasure,
        ];

        if ($this->img) {
            $filename = time() . '_' . $this->img->getClientOriginalName();
            
            // Simpan gambar ke storage/app/public/mtc_img/kyt
            $this->img->storeAs('mtc_img/kyt', $filename, 'public');
            
            // Hapus gambar lama jika ada
            if ($this->oldImg && Storage::disk('public')->exists('mtc_img/kyt/' . $this->oldImg)) {
                Storage::disk('public')->delete('mtc_img/kyt/' . $this->oldImg);
            }
            
            $data['img'] = $filename;
        }

        if ($this->kytNo) {
            Sky::where('no', $this->kytNo)->update($data);
            $this->success('Data KYT berhasil diperbarui.');
        } else {
            Sky::create($data);
            $this->success('Data KYT berhasil ditambahkan.');
        }

        $this->kytModal = false;
    }

    public function confirmDelete(int $no)
    {
        $this->deleteNo = $no;
        $this->confirmModal = true;
    }

    public function executeDelete()
    {
        if ($this->deleteNo) {
            $kyt = Sky::find($this->deleteNo);
            if ($kyt) {
                if ($kyt->img && Storage::disk('public')->exists('mtc_img/kyt/' . $kyt->img)) {
                    Storage::disk('public')->delete('mtc_img/kyt/' . $kyt->img);
                }
                $kyt->delete();
                $this->success('Data KYT berhasil dihapus.');
            }
        }
        
        $this->confirmModal = false;
        $this->deleteNo = null;
    }

    public function showDetail(int $no)
    {
        $this->detailNo = $no;
        $this->detailModal = true;
    }

    #[Computed]
    public function detailKyt()
    {
        return $this->detailNo ? Sky::with('user')->find($this->detailNo) : null;
    }

    #[Computed]
    public function headers(): array
    {
        return [
            ['key' => 'date', 'label' => 'Tgl', 'class' => 'w-24 whitespace-nowrap'],
            ['key' => 'user.name', 'label' => 'User', 'class' => 'w-48 whitespace-nowrap'],
            ['key' => 'lokasi', 'label' => 'Lokasi', 'class' => 'w-32'],
            ['key' => 'bahaya', 'label' => 'Bahaya', 'class' => 'w-48'],
            ['key' => 'actions', 'label' => 'Aksi', 'class' => 'w-32 text-center'],
        ];
    }

    #[Computed]
    public function records()
    {
        $awal = $this->currentMonth . '-01';
        $akhir = Carbon::parse($awal)->endOfMonth()->format('Y-m-d');

        $query = Sky::with('user')
            ->select('no', 'date', 'userId', 'lokasi', 'bahaya', 'countermeasure', 'resiko', 'img')
            ->whereBetween('date', [$awal, $akhir])
            ->orderBy('date', 'desc');

        if (!empty($this->search)) {
            $query->where(function (Builder $q) {
                $q->where('lokasi', 'like', '%' . $this->search . '%')
                  ->orWhere('bahaya', 'like', '%' . $this->search . '%')
                  ->orWhere('resiko', 'like', '%' . $this->search . '%')
                  ->orWhereHas('user', function (Builder $userQuery) {
                      $userQuery->where('name', 'like', '%' . $this->search . '%');
                  });
            });
        }

        return $query->paginate(10);
    }
    
    // Helper function for image path
    public function getImageUrl($filename)
    {
        // Jika file ada di folder public lokal:
        if (File::exists(public_path('mtc_img/kyt/' . $filename))) {
            return asset('mtc_img/kyt/' . $filename);
        }
        
        // Default ke symlink storage
        return asset('storage/mtc_img/kyt/' . $filename);
    }
};
?>

<div>
    @include('livewire.administration.kyt.partials.header')
    @include('livewire.administration.kyt.partials.table')
    @include('livewire.administration.kyt.partials.form')
    @include('livewire.administration.kyt.partials.detail')
</div>
