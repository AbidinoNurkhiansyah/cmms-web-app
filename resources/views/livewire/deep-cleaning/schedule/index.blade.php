<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\DeepCleaningSchedule;
use App\Models\DeepCleaningMachineItem;
use App\Models\DeepCleaning;
use App\Models\Asset;
use Mary\Traits\Toast;
use Carbon\Carbon;
use App\Livewire\Traits\WithAssetSelection;
use App\Livewire\DeepCleaning\Schedule\Traits\WithScheduleModals;

new class extends Component {
    use WithPagination, Toast, WithAssetSelection, WithScheduleModals;

    public string $selectedMonthYear;
    public int $perPage = 10;

    // Modals
    public bool $generateModal = false;
    public bool $editModal = false;
    public bool $deleteModal = false;
    public bool $itemCheckModal = false;

    // Generate Modal State
    public string $generateTab = 'planning'; // 'planning' or 'done'
    public string $generateMonthYear = '';
    public string $generateLineFilter = '';
    public string $generateSearch = '';

    // Edit Modal State
    public ?int $editingId = null;
    public string $editPlanDate = '';

    // Execution / Item Check Modal State
    public ?int $itemCheckScheduleId = null;
    public ?string $actDate = null;
    public string $itemCheckTab = 'execute'; // 'execute' or 'manage'
    public array $machineItems = []; // Master data items
    public array $scheduleItems = []; // Result items (json)
    public string $newItemCheck = '';
    public string $newStandard = '';

    // Delete State
    public ?int $deleteId = null;

    public function mount()
    {
        $this->selectedMonthYear = Carbon::now()->format('Y-m');

        $this->mountWithAssetSelection();
    }

    public function updatedSelectedMonthYear()
    {
        $this->resetPage();
    }



    public function with(): array
    {
        $dateParts = explode('-', $this->selectedMonthYear);
        $schedulesQuery = DeepCleaningSchedule::query()
            ->whereYear('planDate', $dateParts[0])
            ->whereMonth('planDate', $dateParts[1])
            ->orderBy('planDate', 'asc');
            
        $schedules = (clone $schedulesQuery)->paginate($this->perPage);

        $calendarSchedules = (clone $schedulesQuery)->get()->groupBy(fn($item) => $item->planDate ? $item->planDate->format('Y-m-d') : '');
        $firstDayOfMonth = Carbon::parse($this->selectedMonthYear)->startOfMonth();
        $daysInMonth = $firstDayOfMonth->daysInMonth;
        $startDayOfWeek = $firstDayOfMonth->dayOfWeek; // 0 (Sunday) to 6 (Saturday)

        $generateLines = Asset::whereNotNull('line_name')
            ->distinct()
            ->orderBy('line_name')
            ->pluck('line_name')
            ->map(fn($line) => ['id' => $line, 'name' => $line])
            ->toArray();

        return [
            'schedules' => $schedules,
            'calendarSchedules' => $calendarSchedules,
            'daysInMonth' => $daysInMonth,
            'startDayOfWeek' => $startDayOfWeek,
            'generateLines' => $generateLines,
            'generateMachines' => $this->getGenerateMachines(),
        ];
    }

    public function getGenerateMachines()
    {
        if (!$this->generateModal || empty($this->generateMonthYear))
            return [];

        $parts = explode('-', $this->generateMonthYear);
        if (count($parts) !== 2) return [];

        // All distinct machines with filters
        $query = Asset::query()->whereNotNull('line_name')->whereNotNull('machine_name');

        if (!empty($this->generateLineFilter)) {
            $query->where('line_name', $this->generateLineFilter);
        }

        if (!empty($this->generateSearch)) {
            $query->where(function($q) {
                $q->where('machine_name', 'like', '%' . $this->generateSearch . '%')
                  ->orWhere('asset_no', 'like', '%' . $this->generateSearch . '%');
            });
        }

        $allAssets = $query->get();

        // Scheduled machines for selected month/year
        $scheduledAssets = DeepCleaningSchedule::whereMonth('planDate', $parts[1])
            ->whereYear('planDate', $parts[0])
            ->get();

        $scheduledKeys = $scheduledAssets->map(function ($s) {
            return $s->LineName . '|' . $s->NameMachine . '|' . $s->machine_no;
        })->toArray();

        $result = [];
        foreach ($allAssets as $asset) {
            $key = $asset->line_name . '|' . $asset->machine_name . '|' . $asset->asset_no;
            $isAdded = in_array($key, $scheduledKeys);

            $result[] = [
                'line' => $asset->line_name,
                'machine' => $asset->machine_name,
                'machine_no' => $asset->asset_no,
                'added' => $isAdded,
            ];
        }

        return $result;
    }

    // Logic dipindahkan ke Trait WithScheduleModals

};
?>

<div x-data="{ localViewMode: 'table' }">
    <x-header title="Deep Cleaning Schedule" separator>
        <x-slot:actions>
            <div class="flex flex-row items-center gap-2">
                <div class="w-48">
                    <x-input type="month" wire:model.live="selectedMonthYear" />
                </div>
                <x-button icon="o-plus" class="btn-primary" wire:click="openGenerateModal">
                    Generate
                </x-button>
            </div>
        </x-slot:actions>
    </x-header>

    <!-- View Mode Toggle -->
    <div class="flex justify-end mb-4">
        <div class="join">
            <button class="join-item btn btn-sm" :class="localViewMode === 'table' ? 'btn-active' : ''" @click="localViewMode = 'table'">
                <x-icon name="o-list-bullet" class="w-4 h-4" /> Table
            </button>
            <button class="join-item btn btn-sm" :class="localViewMode === 'calendar' ? 'btn-active' : ''" @click="localViewMode = 'calendar'">
                <x-icon name="o-calendar" class="w-4 h-4" /> Calendar
            </button>
        </div>
    </div>

    <!-- TABLE MODE -->
    @include('livewire.deep-cleaning.schedule.partials.table')

    <!-- CALENDAR MODE -->
    @include('livewire.deep-cleaning.schedule.partials.calendar')

    <!-- MODALS -->
    @include('livewire.deep-cleaning.schedule.partials.generate-modal')
    @include('livewire.deep-cleaning.schedule.partials.edit-modal')
    @include('livewire.deep-cleaning.schedule.partials.execution-modal')
    @include('livewire.deep-cleaning.schedule.partials.delete-modal')
</div>