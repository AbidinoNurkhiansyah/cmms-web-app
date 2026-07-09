<?php

use Livewire\Volt\Component;
use App\Services\AttendanceService;
use Livewire\Attributes\Computed;
use Carbon\Carbon;
use Mary\Traits\Toast;

new class extends Component {
    use Toast;
    
    public $date;
    public $selectedTeam = 'all';
    public $search = '';

    // User Detail State
    public bool $showingUserDetail = false;
    public ?int $selectedUserId = null;
    public string $selectedUserName = '';
    
    public function mount() {
        // Default to today if not provided via query string
        $this->date = request()->query('date', Carbon::today()->format('Y-m-d'));
    }
    
    public function showUserDetail($userId, $userName) {
        $this->selectedUserId = $userId;
        $this->selectedUserName = $userName;
        $this->showingUserDetail = true;
    }
    
    public function setDate($date) {
        $this->date = Carbon::parse($date)->format('Y-m-d');
        $this->selectedTeam = 'all';
    }
    
    public function previousDay() {
        $this->date = Carbon::parse($this->date)->subDay()->format('Y-m-d');
        $this->selectedTeam = 'all';
    }
    
    public function nextDay() {
        $this->date = Carbon::parse($this->date)->addDay()->format('Y-m-d');
        $this->selectedTeam = 'all';
    }
    
    #[Computed]
    public function attendanceData() {
        $data = app(AttendanceService::class)->getAttendanceForDate($this->date, $this->search);
        return $data;
    }
    
    #[Computed]
    public function currentTeamData() {
        $data = collect($this->attendanceData['grouped']);
        if ($this->selectedTeam === 'all' || !$this->selectedTeam) {
            return $data->flatten(); // Much faster than looping and merging manually
        }
        return $data[$this->selectedTeam] ?? collect();
    }
    
    public function getStatusOptions() {
        return [' ', 'P', 'S', 'CL', 'A', 'AL', 'AL 1/2', 'SL', 'UL', 'CD', 'BT'];
    }
    
    public function getStatusColor($status) {
        return match ($status) {
            'A' => 'bg-error text-error-content text-white',
            'SL' => 'bg-info text-info-content',
            'AL', 'AL 1/2' => 'bg-warning text-warning-content',
            'CL' => 'bg-fuchsia-300 text-fuchsia-900',
            'UL' => 'bg-orange-400 text-orange-900',
            'S' => 'bg-success text-success-content',
            'CD' => 'bg-orange-300 text-orange-900',
            'BT' => 'bg-yellow-300 text-yellow-900',
            'P' => 'bg-base-300 text-base-content',
            default => ''
        };
    }
    
    public function updateStatus($id, $status) {
        app(AttendanceService::class)->updateStatus($id, $status);
        $this->success("Status updated successfully.");
    }
    
    public function markAllPresent($team) {
        if ($team === 'all' || !$team) {
            app(AttendanceService::class)->updateAllStatus($this->date, 'P');
            $this->success("All empty statuses marked as Present.");
        } else {
            app(AttendanceService::class)->updateSectionStatus($team, $this->date, 'P');
            $this->success("All empty statuses for $team marked as Present.");
        }
    }
};
?>

<div>
    @include('livewire.administration.attendance.partials.header')
    @include('livewire.administration.attendance.partials.table')

    <!-- Drawer for User Detail -->
    <x-drawer wire:model="showingUserDetail" title="Rekap Absen: {{ $selectedUserName }}" right class="w-11/12 lg:w-1/3">
        @if($showingUserDetail)
            <div class="mt-4">
                <livewire:administration.attendance.user-detail :user-id="$selectedUserId" :key="$selectedUserId" />
            </div>
            
            <x-slot:actions>
                <x-button label="Tutup" @click="$wire.showingUserDetail = false" class="btn-primary" />
            </x-slot:actions>
        @endif
    </x-drawer>
</div>
