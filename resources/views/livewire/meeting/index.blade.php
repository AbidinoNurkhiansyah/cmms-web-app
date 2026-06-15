<?php

use App\Models\Carty;
use App\Models\WorkOrder;
use App\Models\Tpm;
use App\Models\Overhaul;
use App\Models\Sky;
use Livewire\Volt\Component;

new class extends Component {
    
    public $mtcA_records = [];
    public $mtcB_records = [];
    public $workOrders = [];
    public $tpmRecords = [];
    public $overhaulRecords = [];
    public $skyRecords = [];

    public function mount()
    {
        $yesterday = now()->subDay()->startOfDay();
        $tomorrow = now()->addDay()->endOfDay();

        // 1. MTC A Carty
        $this->mtcA_records = Carty::where('groupline', 'MTC A')
            ->whereBetween('Date', [$yesterday, $tomorrow])
            ->where('DownTime', '>', 30)
            ->orderByDesc('id')
            ->take(5)
            ->get();

        // 2. MTC B Carty
        $this->mtcB_records = Carty::where('groupline', 'MTC B')
            ->whereBetween('Date', [$yesterday, $tomorrow])
            ->where('DownTime', '>', 30)
            ->orderByDesc('id')
            ->take(5)
            ->get();

        // 3. Work Orders
        $this->workOrders = WorkOrder::whereBetween('date', [$yesterday, $tomorrow])
            ->where('status', 'Closed')
            ->orderByDesc('id')
            ->take(5)
            ->get();

        // 4. TPM (Deep Cleaning)
        $this->tpmRecords = Tpm::where('Date', '>=', now()->subDays(3)->startOfDay())
            ->orderByDesc('id')
            ->take(5)
            ->get();

        // 5. Overhaul
        $this->overhaulRecords = Overhaul::where('Date', '>=', now()->subDays(3)->startOfDay())
            ->orderByDesc('id')
            ->take(5)
            ->get();

        // 6. SKY
        $this->skyRecords = Sky::where('date', '>=', now()->startOfDay())
            ->orderByDesc('no')
            ->take(5)
            ->get();
    }
};
?>

<div>
    <x-header title="Meeting Dashboard / Information Center" separator progress-indicator />

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-5">
        <!-- MTC A -->
        <x-card title="Maintenance Team A [Downtime > 30 min]" class="border-t-4 border-t-primary">
            <div class="overflow-x-auto">
                <table class="table table-xs w-full">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Line</th>
                            <th>Machine</th>
                            <th>Time</th>
                            <th>Problem</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mtcA_records as $r)
                        <tr>
                            <td>{{ $r->Date ? $r->Date->format('d M') : '-' }}</td>
                            <td>{{ $r->LineName }}</td>
                            <td>{{ $r->MachineName }}</td>
                            <td>{{ $r->DownTime }}m</td>
                            <td class="text-error font-semibold">{{ $r->Problem }}</td>
                            <td>{{ $r->Status }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-gray-500 py-3">No major issues.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>

        <!-- MTC B -->
        <x-card title="Maintenance Team B [Downtime > 30 min]" class="border-t-4 border-t-secondary">
            <div class="overflow-x-auto">
                <table class="table table-xs w-full">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Line</th>
                            <th>Machine</th>
                            <th>Time</th>
                            <th>Problem</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mtcB_records as $r)
                        <tr>
                            <td>{{ $r->Date ? $r->Date->format('d M') : '-' }}</td>
                            <td>{{ $r->LineName }}</td>
                            <td>{{ $r->MachineName }}</td>
                            <td>{{ $r->DownTime }}m</td>
                            <td class="text-error font-semibold">{{ $r->Problem }}</td>
                            <td>{{ $r->Status }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-gray-500 py-3">No major issues.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>
    </div>

    <!-- Additional Tables Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- Work Orders -->
        <x-card title="Work Orders (Closed)">
            <div class="overflow-x-auto">
                <table class="table table-xs w-full">
                    <thead><tr><th>Line</th><th>Desc</th><th>PIC</th></tr></thead>
                    <tbody>
                        @forelse($workOrders as $wo)
                        <tr>
                            <td>{{ $wo->LineName }}</td>
                            <td class="truncate max-w-[100px]">{{ $wo->problem_description }}</td>
                            <td>{{ $wo->pic }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center text-gray-500">No data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>

        <!-- Deep Cleaning -->
        <x-card title="Deep Cleaning (TPM)">
            <div class="overflow-x-auto">
                <table class="table table-xs w-full">
                    <thead><tr><th>Date</th><th>Line</th><th>Machine</th></tr></thead>
                    <tbody>
                        @forelse($tpmRecords as $tpm)
                        <tr>
                            <td>{{ $tpm->Date ? $tpm->Date->format('d M') : '-' }}</td>
                            <td>{{ $tpm->LineName }}</td>
                            <td>{{ $tpm->MachineName }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center text-gray-500">No data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>

        <!-- Overhaul -->
        <x-card title="Overhaul">
            <div class="overflow-x-auto">
                <table class="table table-xs w-full">
                    <thead><tr><th>Date</th><th>Line</th><th>Machine</th></tr></thead>
                    <tbody>
                        @forelse($overhaulRecords as $oh)
                        <tr>
                            <td>{{ $oh->Date ? $oh->Date->format('d M') : '-' }}</td>
                            <td>{{ $oh->LineName }}</td>
                            <td>{{ $oh->MachineName }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center text-gray-500">No data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>

        <!-- SKY -->
        <x-card title="SKY (Safety)">
            <div class="overflow-x-auto">
                <table class="table table-xs w-full">
                    <thead><tr><th>Loc</th><th>User ID</th></tr></thead>
                    <tbody>
                        @forelse($skyRecords as $sky)
                        <tr>
                            <td>{{ $sky->lokasi }}</td>
                            <td>{{ $sky->userId }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="2" class="text-center text-gray-500">No data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>

    </div>
</div>
