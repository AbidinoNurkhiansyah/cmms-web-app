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

new class extends Component {
    use WithPagination, Toast, WithAssetSelection;

    public string $selectedMonthYear;
    public int $perPage = 10;

    // Modals
    public bool $generateModal = false;
    public bool $editModal = false;
    public bool $deleteModal = false;
    public bool $itemCheckModal = false;

    // Generate Modal State
    public string $generateTab = 'planning'; // 'planning' or 'done'
    public int $generateMonth;
    public int $generateYear;

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

        $this->generateMonth = Carbon::now()->month;
        $this->generateYear = Carbon::now()->year;

        $this->mountWithAssetSelection();
    }

    public function updatedSelectedMonthYear()
    {
        $this->resetPage();
    }

    public function openCalendarList($date)
    {
        $this->calendarSelectedDate = $date;
        $this->calendarListModal = true;
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

        return [
            'schedules' => $schedules,
            'calendarSchedules' => $calendarSchedules,
            'daysInMonth' => $daysInMonth,
            'startDayOfWeek' => $startDayOfWeek,
            'months' => [
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
            ],
            'years' => collect(range(Carbon::now()->year - 2, Carbon::now()->year + 3))
                ->map(fn($year) => ['id' => $year, 'name' => (string) $year])
                ->toArray(),
            'generateMachines' => $this->getGenerateMachines(),
        ];
    }

    public function getGenerateMachines()
    {
        if (!$this->generateModal)
            return [];

        // All distinct machines
        $allAssets = Asset::whereNotNull('line_name')->whereNotNull('machine_name')->get();

        // Scheduled machines for selected month/year
        $scheduledAssets = DeepCleaningSchedule::whereMonth('planDate', $this->generateMonth)
            ->whereYear('planDate', $this->generateYear)
            ->get();

        $scheduledKeys = $scheduledAssets->map(function ($s) {
            return $s->LineName . '|' . $s->NameMachine . '|' . $s->machine_no;
        })->toArray();

        $result = [];
        foreach ($allAssets as $asset) {
            $key = $asset->line_name . '|' . $asset->machine_name . '|' . $asset->asset_no;
            $isAdded = in_array($key, $scheduledKeys);

            if ($this->generateTab === 'planning' && $isAdded)
                continue;
            if ($this->generateTab === 'done' && !$isAdded)
                continue;

            $result[] = [
                'line' => $asset->line_name,
                'machine' => $asset->machine_name,
                'machine_no' => $asset->asset_no,
                'added' => $isAdded,
            ];
        }

        return $result;
    }

    public function openGenerateModal()
    {
        $parts = explode('-', $this->selectedMonthYear);
        if (count($parts) === 2) {
            $this->generateYear = (int) $parts[0];
            $this->generateMonth = (int) $parts[1];
        } else {
            $this->generateMonth = (int) now()->format('m');
            $this->generateYear = (int) now()->format('Y');
        }
        $this->generateTab = 'planning';
        $this->generateModal = true;
    }

    public function addToSchedule($line, $machine, $machineNo)
    {
        $planDate = Carbon::create($this->generateYear, $this->generateMonth, 1)->format('Y-m-d');

        DeepCleaningSchedule::create([
            'planDate' => $planDate,
            'LineName' => $line,
            'NameMachine' => $machine,
            'machine_no' => $machineNo,
            'items' => [],
            'is_approved' => false,
            'postponed' => false,
        ]);

        $this->success("Added $machine to schedule.");
    }

    public function togglePostpone($id)
    {
        $schedule = DeepCleaningSchedule::find($id);
        if ($schedule) {
            $schedule->postponed = !$schedule->postponed;
            $schedule->save();
            $status = $schedule->postponed ? 'Postponed' : 'Active';
            $this->success("Schedule status changed to $status.");
        }
    }

    // Keep this for any existing standalone calls, though UI now uses modal
    public function updateActDate($id, $date)
    {
        $schedule = DeepCleaningSchedule::find($id);
        if ($schedule && $date) {
            $schedule->act_date = $date;
            $schedule->save();
            $this->success("Actual date saved.");
        }
    }

    public function toggleReport($id)
    {
        $schedule = DeepCleaningSchedule::find($id);
        if (!$schedule)
            return;

        if (!$schedule->act_date && !$schedule->is_approved) {
            $this->error("Please set Actual Date first before reporting.");
            return;
        }

        $schedule->is_approved = !$schedule->is_approved;
        $schedule->save();

        if ($schedule->is_approved) {
            // Auto create Deep Cleaning Record
            $report = DeepCleaning::create([
                'Date' => $schedule->act_date,
                'LineName' => $schedule->LineName,
                'MachineNo' => $schedule->machine_no,
                'MachineName' => $schedule->NameMachine,
                'description' => 'TPM',
                'status' => 'Done',
                'pics' => []
            ]);

            // Copy items
            $results = $schedule->items ?? [];
            foreach ($results as $itemCheck => $resultValue) {
                $report->items()->create([
                    'itemcheck' => $itemCheck,
                    'action' => $resultValue,
                    'status' => 'Done',
                    'description' => 'TPM Checklist'
                ]);
            }

            $this->success("Report Approved and Record Created.");
        } else {
            $this->info("Report Approval Canceled.");
        }
    }

    public function openEditModal($id)
    {
        $schedule = DeepCleaningSchedule::find($id);
        if ($schedule) {
            $this->editingId = $id;
            $this->editPlanDate = $schedule->planDate ? $schedule->planDate->format('Y-m-d') : '';
            $this->LineName = $schedule->LineName;
            $this->updatedLineName($this->LineName);
            $this->asset_id = $this->machines->firstWhere('asset_no', $schedule->machine_no)?->id;
            $this->updatedAssetId($this->asset_id);
            $this->editModal = true;
        }
    }

    public function saveEdit()
    {
        $this->validate([
            'editPlanDate' => 'required|date',
            'LineName' => 'required|string',
            'MachineName' => 'required|string',
        ]);

        $schedule = DeepCleaningSchedule::find($this->editingId);
        if ($schedule) {
            $schedule->update([
                'planDate' => $this->editPlanDate,
                'LineName' => $this->LineName,
                'NameMachine' => $this->MachineName,
                'machine_no' => $this->MachineNo,
            ]);
            $this->success("Schedule updated.");
            $this->editModal = false;
        }
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->deleteModal = true;
    }

    public function deleteSchedule()
    {
        if ($this->deleteId) {
            DeepCleaningSchedule::destroy($this->deleteId);
            $this->success('Schedule deleted.');
        }
        $this->deleteModal = false;
        $this->deleteId = null;
    }

    // --- ITEM CHECK LOGIC ---
    public function openItemCheckModal($id)
    {
        $schedule = DeepCleaningSchedule::find($id);
        if (!$schedule)
            return;

        $this->itemCheckScheduleId = $id;
        $this->scheduleItems = is_array($schedule->items) ? $schedule->items : [];
        $this->actDate = $schedule->act_date ? $schedule->act_date->format('Y-m-d') : '';
        $this->newItemCheck = '';
        $this->newStandard = '';
        $this->itemCheckTab = 'execute'; // Default tab

        $this->loadMachineItems($schedule->LineName, $schedule->NameMachine);
        $this->itemCheckModal = true;
    }

    public function saveExecution()
    {
        $schedule = DeepCleaningSchedule::find($this->itemCheckScheduleId);
        if ($schedule) {
            $schedule->act_date = $this->actDate ?: null;
            $schedule->items = $this->scheduleItems;
            $schedule->save();
            $this->success("Schedule execution saved.");
        }
    }

    public function approveSchedule()
    {
        $schedule = DeepCleaningSchedule::find($this->itemCheckScheduleId);
        if (!$schedule)
            return;

        if (!$this->actDate) {
            $this->error("Please set Actual Date first before reporting.");
            return;
        }

        $schedule->act_date = $this->actDate;
        $schedule->items = $this->scheduleItems;
        $schedule->is_approved = true;
        $schedule->save();

        // Auto create Deep Cleaning Record
        $report = DeepCleaning::create([
            'Date' => $schedule->act_date,
            'LineName' => $schedule->LineName,
            'MachineNo' => $schedule->machine_no,
            'MachineName' => $schedule->NameMachine,
            'description' => 'TPM',
            'status' => 'Done',
            'pics' => []
        ]);

        // Copy items
        $results = $schedule->items ?? [];
        foreach ($results as $itemCheck => $resultValue) {
            $report->items()->create([
                'itemcheck' => $itemCheck,
                'action' => $resultValue,
                'status' => 'Done',
                'description' => 'TPM Checklist'
            ]);
        }

        $this->success("Report Approved and Record Created.");
        $this->itemCheckModal = false;
    }

    public function loadMachineItems($line, $machine)
    {
        $this->machineItems = DeepCleaningMachineItem::where('lineName', $line)
            ->where('machineName', $machine)
            ->get()
            ->toArray();
    }

    public function saveNewMachineItem()
    {
        $this->validate([
            'newItemCheck' => 'required|string',
            'newStandard' => 'required|string',
        ]);

        $schedule = DeepCleaningSchedule::find($this->itemCheckScheduleId);
        if ($schedule) {
            DeepCleaningMachineItem::create([
                'lineName' => $schedule->LineName,
                'machineName' => $schedule->NameMachine,
                'itemCheck' => $this->newItemCheck,
                'standard' => $this->newStandard,
            ]);
            $this->success("Machine Item added.");
            $this->loadMachineItems($schedule->LineName, $schedule->NameMachine);
            $this->newItemCheck = '';
            $this->newStandard = '';
        }
    }

    public function deleteMachineItem($id)
    {
        DeepCleaningMachineItem::destroy($id);
        $this->success("Machine Item deleted.");
        $schedule = DeepCleaningSchedule::find($this->itemCheckScheduleId);
        if ($schedule) {
            $this->loadMachineItems($schedule->LineName, $schedule->NameMachine);
        }
    }

    public function updateItemResult($itemCheck, $resultValue)
    {
        $this->scheduleItems[$itemCheck] = $resultValue;
        $schedule = DeepCleaningSchedule::find($this->itemCheckScheduleId);
        if ($schedule) {
            $schedule->items = $this->scheduleItems;
            $schedule->save();
        }
    }

    public function toggleItemStatus($itemCheck)
    {
        if (isset($this->scheduleItems[$itemCheck]) && $this->scheduleItems[$itemCheck] !== '') {
            unset($this->scheduleItems[$itemCheck]);
        } else {
            $this->scheduleItems[$itemCheck] = 'Done'; // default value
        }

        $schedule = DeepCleaningSchedule::find($this->itemCheckScheduleId);
        if ($schedule) {
            $schedule->items = $this->scheduleItems;
            $schedule->save();
        }
    }

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
    <div x-show="localViewMode === 'table'">
        <x-card>
            <div class="overflow-x-auto w-full">
            <x-table :headers="[
        ['key' => 'LineName', 'label' => 'Line'],
        ['key' => 'NameMachine', 'label' => 'Machine'],
        ['key' => 'machine_no', 'label' => 'Asset No'],
        ['key' => 'planDate', 'label' => 'Plan Date'],
        ['key' => 'status_badge', 'label' => 'Status'],
        ['key' => 'progress', 'label' => 'Checklist'],
        ['key' => 'actions', 'label' => 'Actions', 'class' => 'w-24 text-center'],
    ]" :rows="$schedules" with-pagination
                class="text-sm" no-hover>

                @scope('cell_planDate', $row)
                {{ $row->planDate ? $row->planDate->format('Y-m-d') : '-' }}
                @endscope

                @scope('cell_status_badge', $row)
                @if($row->is_approved)
                    <x-badge value="Approved" class="badge-success text-white" />
                @elseif($row->postponed)
                    <x-badge value="Postponed" class="badge-error text-white" />
                @elseif($row->act_date)
                    <x-badge value="In Progress" class="badge-warning" />
                @else
                    <x-badge value="Planning" class="badge-neutral" />
                @endif

                @if($row->act_date)
                    <div class="text-xs text-base-content/60 mt-1">{{ $row->act_date->format('Y-m-d') }}</div>
                @endif
                @endscope

                @scope('cell_progress', $row)
                @php $filledCount = is_array($row->items) ? count($row->items) : 0; @endphp
                @if($filledCount > 0)
                    <x-badge value="{{ $filledCount }} Items Filled" class="badge-ghost" />
                @else
                    <span class="text-base-content/40">-</span>
                @endif
                @endscope

                @scope('cell_actions', $row)
                <div class="flex gap-2 justify-center items-center">
                    <x-button label="{{ $row->is_approved ? 'View' : 'Execute' }}"
                        class="{{ $row->is_approved ? 'btn-ghost' : 'btn-primary' }} btn-sm"
                        wire:click="openItemCheckModal({{ $row->id }})"
                        icon="{{ $row->is_approved ? 'o-eye' : 'o-play' }}" />

                    <!-- Alpine.js Teleported Dropdown -->
                    <div x-data="{ open: false, top: 0, left: 0 }" @scroll.window="open = false"
                        @close-dropdowns.window="if ($event.detail.id !== {{ $row->id }}) open = false" @click.stop>
                        <button x-ref="triggerBtn"
                            @click="open = !open; if(open) { window.dispatchEvent(new CustomEvent('close-dropdowns', { detail: { id: {{ $row->id }} } })); let r = $refs.triggerBtn.getBoundingClientRect(); top = r.top - 4; left = r.left - 200; }"
                            class="btn btn-ghost btn-sm">
                            <x-icon name="o-ellipsis-vertical" class="w-5 h-5" />
                        </button>

                        <template x-teleport="body">
                            <div x-show="open" @click.away="open = false" x-transition.opacity.duration.200ms
                                class="fixed z-[9999]" :style="`top: ${top}px; left: ${left}px; width: 192px;`">
                                <ul class="menu bg-base-100 shadow-xl rounded-box border border-base-200">
                                    @if(!$row->is_approved)
                                        <li><a @click="open = false; $wire.togglePostpone({{ $row->id }})"><x-icon
                                                    name="{{ $row->postponed ? 'o-play-circle' : 'o-pause-circle' }}"
                                                    class="w-4 h-4" />
                                                {{ $row->postponed ? 'Activate' : 'Postpone' }}</a>
                                        </li>
                                        <li><a @click="open = false; $wire.openEditModal({{ $row->id }})"><x-icon
                                                    name="o-pencil" class="w-4 h-4" /> Edit</a></li>
                                    @else
                                        <li><a @click="open = false; $wire.toggleReport({{ $row->id }})"><x-icon
                                                    name="o-x-circle" class="w-4 h-4" /> Cancel</a></li>
                                    @endif
                                    <li><a @click="open = false; $wire.confirmDelete({{ $row->id }})"
                                            class="text-error"><x-icon name="o-trash" class="w-4 h-4" /> Delete</a></li>
                                </ul>
                            </div>
                        </template>
                    </div>
                </div>
                @endscope
            </x-table>
        </div>
    </x-card>
    </div>

    <!-- CALENDAR MODE -->
    <div x-show="localViewMode === 'calendar'" x-cloak>
        <x-card>
            <div class="grid grid-cols-7 gap-2">
                <!-- Days Header -->
                @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $dayName)
                    <div class="text-center font-bold text-sm text-base-content/70 py-2">{{ $dayName }}</div>
                @endforeach

                <!-- Empty slots for first week -->
                @for($i = 0; $i < $startDayOfWeek; $i++)
                    <div class="p-2 border border-transparent"></div>
                @endfor

                <!-- Days -->
                @for($day = 1; $day <= $daysInMonth; $day++)
                    @php
                        $dateStr = \Carbon\Carbon::parse($selectedMonthYear . '-' . $day)->format('Y-m-d');
                        $daySchedules = $calendarSchedules->get($dateStr, collect());
                        $isToday = $dateStr === now()->format('Y-m-d');
                    @endphp
                    <div class="min-h-[100px] border {{ $isToday ? 'border-primary bg-primary/5' : 'border-base-200 bg-base-100' }} rounded-lg p-2 flex flex-col gap-1 transition-all">
                        <div class="text-right text-sm font-semibold {{ $isToday ? 'text-primary' : 'text-base-content/50' }}">{{ $day }}</div>
                        @if($daySchedules->count() > 0)
                            @php $first = $daySchedules->first(); @endphp
                            <div class="text-xs p-1.5 rounded bg-base-200 border border-base-300 hover:border-primary/50 cursor-pointer transition-colors flex flex-col shadow-sm"
                                 onclick="document.getElementById('modal_cal_{{ $dateStr }}').showModal()">
                                <span class="font-bold truncate text-base-content">{{ $first->NameMachine }}</span>
                                <span class="truncate text-base-content/70">{{ $first->LineName }}</span>
                                <span class="truncate text-base-content/50">{{ $first->machine_no }}</span>
                            </div>
                            @if($daySchedules->count() > 1)
                                <div class="text-xs text-center text-primary font-semibold cursor-pointer hover:underline mt-1"
                                     onclick="document.getElementById('modal_cal_{{ $dateStr }}').showModal()">
                                    + {{ $daySchedules->count() - 1 }} more
                                </div>
                            @endif
                        @endif
                    </div>

                    {{-- Modal Detail Harian (Rendered di dalam loop agar instan) --}}
                    @if($daySchedules->count() > 0)
                    <dialog id="modal_cal_{{ $dateStr }}" class="modal modal-bottom sm:modal-middle backdrop-blur-sm">
                        <div class="modal-box">
                            <h3 class="font-bold text-lg mb-4">Jadwal Tanggal {{ \Carbon\Carbon::parse($dateStr)->format('d M Y') }}</h3>
                            <div class="flex flex-col gap-2 max-h-[60vh] overflow-y-auto pr-2">
                                @foreach($daySchedules as $sch)
                                    <div class="flex items-center justify-between p-3 border border-base-200 rounded-lg bg-base-100 shadow-sm">
                                        <div class="flex flex-col overflow-hidden mr-2">
                                            <span class="font-bold text-sm truncate">{{ $sch->NameMachine }}</span>
                                            <span class="text-xs text-base-content/70 truncate">{{ $sch->LineName }} &bull; {{ $sch->machine_no }}</span>
                                        </div>
                                        <div class="flex items-center gap-3 shrink-0">
                                            @if($sch->is_approved)
                                                <x-badge value="Approved" class="badge-success text-white badge-sm" />
                                            @elseif($sch->postponed)
                                                <x-badge value="Postponed" class="badge-error text-white badge-sm" />
                                            @elseif($sch->act_date)
                                                <x-badge value="In Progress" class="badge-warning badge-sm" />
                                            @else
                                                <x-badge value="Planning" class="badge-neutral badge-sm" />
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="modal-action">
                                <form method="dialog">
                                    <button class="btn btn-ghost">Tutup</button>
                                </form>
                            </div>
                        </div>
                        <form method="dialog" class="modal-backdrop">
                            <button>close</button>
                        </form>
                    </dialog>
                    @endif
                @endfor
            </div>
        </x-card>
    </div>

    <!-- Calendar List Modal (Removed, now generated inline per day for instant load) -->

    {{-- Generate Modal --}}
    <x-modal wire:model="generateModal" title="Generate Jadwal TPM" class="backdrop-blur" box-class="w-11/12 max-w-5xl">
        <div class="flex flex-col gap-4">
            <div class="flex gap-2">
                <div class="w-48">
                    <x-select label="Plan Bulan" wire:model.live="generateMonth" :options="$months" option-value="id"
                        option-label="name" />
                </div>
                <div class="w-48">
                    <x-select label="Plan Tahun" wire:model.live="generateYear" :options="$years" option-value="id"
                        option-label="name" />
                </div>
            </div>

            <x-tabs wire:model="generateTab" active-class="bg-neutral text-neutral-content">
                <x-tab name="planning" label="Belum Terjadwal">
                    <div class="overflow-x-auto max-h-96">
                        <table class="table table-sm table-pin-rows">
                            <thead>
                                <tr>
                                    <th>Line</th>
                                    <th>Mesin</th>
                                    <th>Mesin No</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($generateMachines as $m)
                                    <tr>
                                        <td>{{ $m['line'] }}</td>
                                        <td>{{ $m['machine'] }}</td>
                                        <td>{{ $m['machine_no'] }}</td>
                                        <td>
                                            <x-button label="Tambah" class="btn-sm btn-success"
                                                wire:click="addToSchedule('{{ addslashes($m['line']) }}', '{{ addslashes($m['machine']) }}', '{{ addslashes($m['machine_no']) }}')" />
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Semua mesin sudah terjadwal di bulan ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </x-tab>
                <x-tab name="done" label="Sudah Terjadwal">
                    <div class="overflow-x-auto max-h-96">
                        <table class="table table-sm table-pin-rows">
                            <thead>
                                <tr>
                                    <th>Line</th>
                                    <th>Mesin</th>
                                    <th>Mesin No</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($generateMachines as $m)
                                    <tr class="bg-success/20">
                                        <td>{{ $m['line'] }}</td>
                                        <td>{{ $m['machine'] }}</td>
                                        <td>{{ $m['machine_no'] }}</td>
                                        <td><x-badge value="Added" class="badge-success" /></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Belum ada mesin yang terjadwal.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </x-tab>
            </x-tabs>
        </div>
        <x-slot:actions>
            <x-button label="Tutup" @click="$wire.generateModal = false" />
        </x-slot:actions>
    </x-modal>

    {{-- Edit Modal --}}
    <x-modal wire:model="editModal" title="Edit Jadwal TPM">
        <div class="grid grid-cols-1 gap-4">
            <x-input type="date" label="Tanggal" wire:model="editPlanDate" />

            <x-choices label="Line" wire:model.live="LineName" :options="$lineNames" option-value="name"
                option-label="name" single />

            @if($LineName)
                <x-choices label="Machine" wire:model.live="asset_id" :options="$machines" option-value="id"
                    option-label="machine_name" single />

                <x-input label="Asset No" wire:model="MachineNo" readonly />
            @endif
        </div>

        <x-slot:actions>
            <x-button label="Cancel" @click="$wire.editModal = false" />
            <x-button label="Save" class="btn-primary" wire:click="saveEdit" />
        </x-slot:actions>
    </x-modal>

    {{-- Execution / Item Check Modal --}}
    <x-modal wire:model="itemCheckModal" title="Execute TPM Schedule" box-class="w-11/12 max-w-5xl">
        @if($itemCheckScheduleId)
            @php
                $currentSchedule = \App\Models\DeepCleaningSchedule::find($itemCheckScheduleId);
                $isApproved = $currentSchedule?->is_approved;
            @endphp
            <div class="flex flex-col gap-6">
                <!-- Header Info -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4 bg-base-200 rounded-lg">
                    <div>
                        <div class="text-xs text-base-content/70">Machine</div>
                        <div class="font-bold">{{ $currentSchedule?->NameMachine }} ({{ $currentSchedule?->LineName }})
                        </div>
                    </div>
                    <div>
                        <div class="text-xs text-base-content/70">Asset No</div>
                        <div class="font-bold">{{ $currentSchedule?->machine_no }}</div>
                    </div>
                    <div>
                        <x-input type="date" label="Actual Date" wire:model="actDate" :disabled="$isApproved" />
                    </div>
                </div>

                <!-- Tabs -->
                <x-tabs wire:model="itemCheckTab" active-class="bg-neutral text-neutral-content">
                    <x-tab name="execute" label="Checklist Execution" icon="o-clipboard-document-list">
                        <div class="overflow-x-auto mt-4">
                            <table class="table table-sm table-zebra">
                                <thead>
                                    <tr>
                                        <th>Item Check</th>
                                        <th>Standard</th>
                                        <th class="w-64">Result</th>
                                        <th class="w-32 text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($machineItems as $item)
                                        @php
                                            $isDone = isset($scheduleItems[$item['itemCheck']]);
                                            $resultVal = $scheduleItems[$item['itemCheck']] ?? '';
                                        @endphp
                                        <tr>
                                            <td class="whitespace-normal">{{ $item['itemCheck'] }}</td>
                                            <td class="whitespace-normal">{{ $item['standard'] }}</td>
                                            <td>
                                                <input type="text" class="input input-sm input-bordered w-full"
                                                    placeholder="Type result..." value="{{ $resultVal }}"
                                                    x-on:change="$wire.updateItemResult('{{ addslashes($item['itemCheck']) }}', $event.target.value)"
                                                    {{ $isApproved ? 'disabled' : '' }}>
                                            </td>
                                            <td class="text-center">
                                                <x-button
                                                    class="btn-sm {{ $isDone ? 'btn-success' : 'btn-ghost border-base-300' }}"
                                                    icon="{{ $isDone ? 'o-check-circle' : 'o-minus-circle' }}"
                                                    label="{{ $isDone ? 'Done' : 'Pending' }}"
                                                    wire:click="toggleItemStatus('{{ addslashes($item['itemCheck']) }}')"
                                                    :disabled="$isApproved" />
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-base-content/60 py-4">No checklist standards
                                                found for this machine. Please add them in the Manage Parameters tab.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </x-tab>

                    <x-tab name="manage" label="Manage Parameters" icon="o-cog-6-tooth">
                        <div class="p-4 border border-base-300 rounded-lg mt-4 flex flex-col gap-4">
                            <div class="text-sm font-semibold opacity-70">Add New Master Parameter</div>
                            <div class="grid grid-cols-1 md:grid-cols-5 gap-2 items-end">
                                <div class="col-span-2">
                                    <x-input label="Item Check" wire:model="newItemCheck"
                                        placeholder="e.g. Check Oil Level" />
                                </div>
                                <div class="col-span-2">
                                    <x-input label="Standard" wire:model="newStandard"
                                        placeholder="e.g. Normal limit is above 50%" />
                                </div>
                                <div>
                                    <x-button label="Add Item" class="btn-neutral w-full" icon="o-plus"
                                        wire:click="saveNewMachineItem" />
                                </div>
                            </div>

                            <div class="divider my-0"></div>

                            <div class="overflow-x-auto">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Item Check</th>
                                            <th>Standard</th>
                                            <th class="w-24 text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($machineItems as $item)
                                            <tr>
                                                <td class="whitespace-normal">{{ $item['itemCheck'] }}</td>
                                                <td class="whitespace-normal">{{ $item['standard'] }}</td>
                                                <td class="text-center">
                                                    <x-button class="btn-sm btn-ghost text-error" icon="o-trash"
                                                        wire:click="deleteMachineItem({{ $item['id'] }})" />
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center text-base-content/60 py-4">No parameters yet.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </x-tab>
                </x-tabs>
            </div>

            <x-slot:actions>
                <x-button label="Close" @click="$wire.itemCheckModal = false" />
                @if(!$isApproved)
                    <x-button label="Save Progress" class="btn-neutral" wire:click="saveExecution" />
                    <x-button label="Approve & Complete" class="btn-success text-white" icon="o-check"
                        wire:click="approveSchedule" />
                @endif
            </x-slot:actions>
        @endif
    </x-modal>

    {{-- Delete Modal --}}
    <x-modal wire:model="deleteModal" title="Hapus Jadwal" separator>
        <div class="py-4">Apakah Anda yakin ingin menghapus jadwal ini?</div>
        <x-slot:actions>
            <x-button label="Batal" @click="$wire.deleteModal = false" />
            <x-button label="Ya, Hapus" class="btn-error text-white" wire:click="deleteSchedule" />
        </x-slot:actions>
    </x-modal>
</div>