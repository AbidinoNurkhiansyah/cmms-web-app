<?php

use App\Models\Carty;
use App\Models\WorkOrder;
use App\Models\DeepCleaning;
use App\Models\Overhaul;
use App\Models\Sky;
use App\Models\RollingBreak;
use App\Models\SparePartRepair;
use App\Models\Information;
use Livewire\Volt\Component;

new class extends Component {

    public $mtcA_records    = [];
    public $mtcB_records    = [];
    public $workOrders      = [];
    public $tpmRecords      = [];
    public $overhaulRecords = [];
    public $skyRecords      = [];
    public $rollingBreaks   = [];
    public $partRepairs     = [];
    public $myInfos         = [];

    public function mount(): void
    {
        // Senin = ambil 3 hari ke belakang, hari lain = 1 hari
        $x       = (date('N') == 1) ? 3 : 1;
        $minDate = now()->subDays($x)->setTime(7, 30, 0);
        $maxDate = now()->addDay()->setTime(7, 30, 0);

        $this->mtcA_records = Carty::where('groupline', 'MTC A')
            ->whereBetween('start_time', [$minDate, $maxDate])
            ->where('DownTime', '>', 30)
            ->orderByDesc('id')
            ->take(10)
            ->get();

        $this->mtcB_records = Carty::where('groupline', 'MTC B')
            ->whereBetween('start_time', [$minDate, $maxDate])
            ->where('DownTime', '>', 30)
            ->orderByDesc('id')
            ->take(10)
            ->get();

        $this->workOrders = WorkOrder::whereBetween('actual_date', [
                $minDate->copy()->startOfDay(),
                $maxDate->copy()->endOfDay(),
            ])
            ->where('status', 'Closed')
            ->orderByDesc('id')
            ->take(10)
            ->get();

        $this->tpmRecords = DeepCleaning::where('Date', '>=', $minDate->copy()->startOfDay())
            ->orderByDesc('id')
            ->take(10)
            ->get();

        $this->overhaulRecords = Overhaul::where('date', '>=', $minDate->copy()->startOfDay())
            ->orderByDesc('id')
            ->take(10)
            ->get();

        $this->skyRecords = Sky::with('user')
            ->where('date', '>=', now()->startOfDay())
            ->orderByDesc('no')
            ->take(10)
            ->get();

        $this->rollingBreaks = RollingBreak::with('user')
            ->whereBetween('date_input', [$minDate, $maxDate])
            ->orderByDesc('date_input')
            ->take(10)
            ->get();

        $this->partRepairs = SparePartRepair::with(['pic1', 'pic2', 'pic3', 'sparePart'])
            ->whereBetween('date', [
                $minDate->copy()->startOfDay(),
                $maxDate->copy()->endOfDay(),
            ])
            ->orderByDesc('id')
            ->take(10)
            ->get();

        $this->myInfos = Information::with('user')
            ->whereBetween('date', [
                $minDate->copy()->startOfDay(),
                $maxDate->copy()->endOfDay(),
            ])
            ->orderByDesc('id')
            ->take(10)
            ->get();
    }
};
?>

<div>
    <x-header title="Meeting Dashboard / Information Center" separator progress-indicator />

    <div x-data="{ tab: 'maintenance' }">

        @include('livewire.meeting.partials.tab-nav')

        @include('livewire.meeting.partials.maintenance')
        @include('livewire.meeting.partials.work-orders')
        @include('livewire.meeting.partials.part-repair')
        @include('livewire.meeting.partials.tpm-overhaul')
        @include('livewire.meeting.partials.safety-info')

    </div>
</div>
