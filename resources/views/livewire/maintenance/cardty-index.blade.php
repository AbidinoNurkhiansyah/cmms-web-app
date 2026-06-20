<?php

use App\Services\CartyService;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CartyExport;
use Barryvdh\DomPDF\Facade\Pdf;

new class extends Component {
    use WithPagination, Toast;

    public string $search = '';
    public string $statusFilter = '';
    public string $startDateFilter = '';
    public string $endDateFilter = '';
    public bool $filterDrawer = false;
    public bool $deleteModal = false;
    public ?int $deleteId = null;

    // Export properties
    public bool $exportModal = false;
    public string $exportStartDate = '';
    public string $exportEndDate = '';
    public string $exportFormat = 'excel';
    
    public string $exportLineName = '';
    public string $exportMachineName = '';
    public string $exportTotalStopLine = '';
    public string $exportStatus = '';
    
    public array $exportLineNames = [];
    public \Illuminate\Support\Collection $exportMachines;

    public function mount(): void
    {
        $lines = \App\Models\Asset::whereNotNull('line_name')->distinct()->pluck('line_name')->toArray();
        $this->exportLineNames = collect($lines)->map(fn($line) => ['id' => $line, 'name' => $line])->toArray();
        $this->exportMachines = collect();
    }

    public function updatedExportLineName($value): void
    {
        $this->exportMachineName = '';
        if ($value) {
            $this->exportMachines = \App\Models\Asset::where('line_name', $value)->get();
        } else {
            $this->exportMachines = collect();
        }
    }

    public function processExport()
    {
        $fileName = 'maintenance_records_' . now()->format('Y_m_d_His');

        $export = new CartyExport(
            $this->search, 
            $this->exportStatus, 
            $this->exportStartDate, 
            $this->exportEndDate,
            $this->exportLineName,
            $this->exportMachineName,
            $this->exportTotalStopLine
        );

        if ($this->exportFormat === 'pdf') {
            // Gunakan output buffer untuk menangkap warning/notice dari DOMPDF
            // Warning ini sering membuat Livewire mengira response kotor & memicu auto-reload
            ob_start();

            $records = $export->collection();
            $pdf = Pdf::loadView('exports.carty-pdf', [
                'records' => $records,
                'startDate' => $this->exportStartDate,
                'endDate' => $this->exportEndDate,
            ])->setPaper('a4', 'landscape');

            $tempPath = sys_get_temp_dir() . '/' . $fileName . '.pdf';
            $pdf->save($tempPath);

            // Buang semua warning yang terekam agar tidak mengotori response Livewire
            ob_end_clean();

            // Tutup modal di frontend
            $this->exportModal = false;

            return response()->download($tempPath, $fileName . '.pdf')->deleteFileAfterSend(true);
        }

        return Excel::download($export, $fileName . '.xlsx');
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedStartDateFilter($value): void
    {
        if ($value && empty($this->endDateFilter)) {
            $this->endDateFilter = $value;
        }
        $this->resetPage();
    }

    public function updatedEndDateFilter($value): void
    {
        if ($value && empty($this->startDateFilter)) {
            $this->startDateFilter = $value;
        }
        $this->resetPage();
    }

    public function with(CartyService $service): array
    {
        $records = $service->getPaginated(15, $this->search, $this->statusFilter, $this->startDateFilter, $this->endDateFilter);
        $records->getCollection()->transform(function ($item, $key) use ($records) {
            $item->index = $records->firstItem() + $key;
            return $item;
        });

        return [
            'records' => $records,
        ];
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId = $id;
        $this->deleteModal = true;
    }

    public function deleteRecord(CartyService $service): void
    {
        if ($this->deleteId) {
            $service->delete($this->deleteId);
            $this->success('Carty Record deleted.');
        }
        $this->deleteModal = false;
        $this->deleteId = null;
    }
};
?>

<div>
    


<x-header title="Carty / Maintenance" separator>
        <x-slot:actions>
            <div class="flex flex-col sm:flex-row w-full sm:w-auto gap-2">
                <!-- Search -->
                <div class="flex-1 sm:w-60">
                    <x-input placeholder="Search problem, line..." wire:model.live.debounce="search" clearable
                        icon="o-magnifying-glass" />
                </div>

                <!-- Filter Toggle -->
                <div class="flex-none">
                    <x-button icon="o-funnel" @click="$wire.filterDrawer = ! $wire.filterDrawer" class="btn-ghost"
                        tooltip="Filters" />
                </div>

                <!-- Export -->
                <div class="flex-none">
                    <x-button icon="o-arrow-down-tray" @click="$wire.exportModal = true" class="btn-success text-white"
                        tooltip="Export Data">
                        <span class="hidden sm:inline">Export</span>
                    </x-button>
                </div>

                <!-- Add -->
                @can('wr.create')
                    <div class="flex-none">
                        <x-button icon="o-plus" class="btn-primary" link="{{ route('maintenance.cardty.create') }}">
                            <span class="hidden sm:inline">Add</span>
                        </x-button>
                    </div>
                @endcan
            </div>
        </x-slot:actions>
    </x-header>
    <!-- Accordion Filters -->
    <div x-show="$wire.filterDrawer" x-collapse>
        <div class="mb-4 p-4 rounded-xl bg-base-100 border border-base-200 shadow-sm">
            <div class="flex flex-col sm:flex-row gap-4 items-end">
                <div class="flex-1">
                    <x-input label="Start Date" type="date" wire:model.live="startDateFilter" />
                </div>
                <div class="flex-1">
                    <x-input label="End Date" type="date" wire:model.live="endDateFilter" />
                </div>
                <div class="flex-1">
                    <x-select label="Status" wire:model.live="statusFilter" :options="[['id' => '', 'name' => 'All Status'], ['id' => 'Permanent', 'name' => 'Permanent'], ['id' => 'Temporary', 'name' => 'Temporary']]"
                        option-value="id" option-label="name" />
                </div>
                <div class="flex-none">
                    <x-button label="Clear Filters" icon="o-x-mark"
                        wire:click="$set('startDateFilter', ''); $set('endDateFilter', ''); $set('statusFilter', ''); $set('search', '')"
                        class="btn-ghost" />
                </div>
            </div>
        </div>
    </div>
    <x-card>
        <x-table striped :headers="[
                ['key' => 'index',        'label' => '#'],
                ['key' => 'Date',         'label' => 'Date'],
                ['key' => 'LineName',     'label' => 'Line'],
                ['key' => 'MachineName',  'label' => 'Machine'],
                ['key' => 'Problem',      'label' => 'Problem'],
                ['key' => 'Status',       'label' => 'Status', 'class' => 'text-center'],
                ['key' => 'DownTime',     'label' => 'DownTime (m)', 'class' => 'text-center'],
                ['key' => 'PIC',          'label' => 'PIC'],
                ['key' => 'action',       'label' => 'Action', 'class' => 'text-center'],
            ]" :rows="$records" with-pagination link="/maintenance/cardty/{id}">
            @scope('cell_Date', $r)
            {{ $r->Date ? $r->Date->format('Y-m-d') : '-' }}
            @endscope

            @scope('cell_Status', $r)
            @php
                $status = strtolower($r->Status);

                [$bgColor, $textColor, $dotColor] = match ($status) {
                    'permanent' => ['bg-blue-100 dark:bg-blue-900/30', 'text-blue-600 dark:text-blue-400', 'bg-blue-500'],
                    'temporary' => ['bg-orange-100 dark:bg-orange-900/30', 'text-orange-600 dark:text-orange-400', 'bg-orange-500'],
                    default => ['bg-gray-100 dark:bg-gray-800', 'text-gray-600 dark:text-gray-400', 'bg-gray-500'],
                };
            @endphp
            <div class="text-center">
                <div
                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium {{ $bgColor }} {{ $textColor }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $dotColor }}"></span>
                    {{ $r->Status }}
                </div>
            </div>
            @endscope

            @scope('cell_PIC', $r)
            @if(is_array($r->pics) && count($r->pics) > 0)
                {{ implode(', ', $r->pics) }}
            @else
                {{ $r->PIC ?? '-' }}
            @endif
            @endscope

            @scope('cell_DownTime', $r)
            <div class="text-center">{{ $r->DownTime }}</div>
            @endscope

            @scope('cell_action', $r)
            <div class="text-center whitespace-nowrap">
                @can('wr.update')
                    <x-button icon="o-pencil-square" class="btn-ghost btn-xs"
                        link="{{ route('maintenance.cardty.edit', $r->id) }}" @click.stop="" />
                @endcan
                @can('wr.delete')
                    <x-button icon="o-trash" class="btn-ghost btn-xs text-error"
                        wire:click.stop="confirmDelete({{ $r->id }})" />
                @endcan
            </div>
            @endscope
        </x-table>
    </x-card>

    <!-- Delete Confirmation Modal -->
    <x-modal wire:model="deleteModal" class="backdrop-blur">
        <div class="flex flex-col items-center justify-center text-center gap-4 py-4">
            <x-icon name="o-exclamation-triangle" class="w-16 h-16 text-error" />
            <div>
                <h3 class="font-bold text-lg">Hapus Record Ini?</h3>
                <p class="text-base-content/70 mt-2">Data yang sudah dihapus tidak dapat dikembalikan lagi. Anda yakin?
                </p>
            </div>
        </div>

        <x-slot:actions>
            <x-button label="Batal" wire:click="$set('deleteModal', false)" class="btn-ghost" />
            <x-button label="Ya, Hapus" wire:click="deleteRecord" class="btn-error text-white" spinner="deleteRecord" />
        </x-slot:actions>
    </x-modal>

    <!-- Export Options Modal -->
    <x-modal wire:model="exportModal" title="Export Options" separator>
        <div class="space-y-4 py-4">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 w-full items-end">
                <x-select label="Line Name" wire:model.live="exportLineName" :options="$exportLineNames" option-value="id" option-label="name" placeholder="Semua Line" />
                <x-select label="Machine Name" wire:model="exportMachineName" :options="$exportMachines" option-value="machine_name" option-label="machine_name" placeholder="Semua Mesin" :disabled="!$exportLineName" />
                <x-select label="Total Stop Line" wire:model="exportTotalStopLine" :options="[['id' => '', 'name' => 'Semua'], ['id' => '30', 'name' => '>= 30 Menit'], ['id' => '60', 'name' => '>= 60 Menit']]" option-value="id" option-label="name" />
                <x-select label="Status" wire:model="exportStatus" :options="[['id' => '', 'name' => 'Semua Status'], ['id' => 'Permanent', 'name' => 'Permanent'], ['id' => 'Temporary', 'name' => 'Temporary']]" option-value="id" option-label="name" />
                <x-input label="Start Date" type="date" wire:model="exportStartDate" />
                <x-input label="End Date" type="date" wire:model="exportEndDate" />
            </div>

            <x-radio label="Export Format" wire:model="exportFormat" :options="[['id' => 'excel', 'name' => 'Excel (.xlsx)'], ['id' => 'pdf', 'name' => 'PDF Document (.pdf)']]" option-value="id" option-label="name" />
        </div>

        <x-slot:actions>
            <x-button label="Cancel" @click="$wire.exportModal = false" class="btn-ghost" />
            <x-button label="Download" wire:click="processExport" icon="o-arrow-down-tray"
                class="btn-success text-white" spinner="processExport" />
        </x-slot:actions>
    </x-modal>
</div>