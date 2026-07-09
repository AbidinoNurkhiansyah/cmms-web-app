<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Services\AttendanceService;
use Carbon\Carbon;

new class extends Component {
    use WithPagination;

    public $userId;
    public $month;
    public $year;

    public function mount($userId)
    {
        $this->userId = $userId;
        $this->month = Carbon::now()->month;
        $this->year = Carbon::now()->year;
    }

    public function updatingMonth()
    {
        $this->resetPage();
    }

    public function updatingYear()
    {
        $this->resetPage();
    }

    public function getStatusColor($status)
    {
        return match (trim($status)) {
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

    public function with()
    {
        return [
            'histories' => app(AttendanceService::class)->getUserAttendanceHistory(
                $this->userId,
                $this->month,
                $this->year
            )
        ];
    }
};
?>

<div>
    <div class="flex gap-2 mb-4">
        <x-select 
            wire:model.live="month" 
            :options="[
                ['id' => 1, 'name' => 'Januari'],
                ['id' => 2, 'name' => 'Februari'],
                ['id' => 3, 'name' => 'Maret'],
                ['id' => 4, 'name' => 'April'],
                ['id' => 5, 'name' => 'Mei'],
                ['id' => 6, 'name' => 'Juni'],
                ['id' => 7, 'name' => 'Juli'],
                ['id' => 8, 'name' => 'Agustus'],
                ['id' => 9, 'name' => 'September'],
                ['id' => 10, 'name' => 'Oktober'],
                ['id' => 11, 'name' => 'November'],
                ['id' => 12, 'name' => 'Desember'],
            ]"
            class="select-sm !bg-base-100" />
        
        <x-select 
            wire:model.live="year" 
            :options="collect(range(date('Y') - 2, date('Y') + 1))->map(fn($y) => ['id' => $y, 'name' => $y])->toArray()"
            class="select-sm !bg-base-100" />
    </div>

    <div class="overflow-x-auto">
        <table class="table table-sm table-zebra w-full border border-base-200">
            <thead class="bg-base-200">
                <tr>
                    <th>Tanggal</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($histories as $history)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($history->date)->format('d M Y') }}</td>
                        <td class="text-center">
                            @if(trim($history->status) !== '')
                                <div class="px-2 py-0.5 text-xs font-bold rounded shadow-sm {{ $this->getStatusColor($history->status) }} inline-block min-w-[3rem]">
                                    {{ $history->status }}
                                </div>
                            @else
                                <span class="text-base-content/30 text-xs italic">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="text-center py-4 text-base-content/50">Tidak ada histori absensi pada bulan ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="mt-4">
        {{ $histories->links() }}
    </div>
</div>
