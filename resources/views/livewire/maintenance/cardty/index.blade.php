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
    public int $perPage = 10;
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

    public function updatedPerPage(): void
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
        $records = $service->getPaginated($this->perPage, $this->search, $this->statusFilter, $this->startDateFilter, $this->endDateFilter);
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
            <!-- Desktop Actions -->
            <div class="hidden sm:flex flex-row items-center gap-2">
                <div class="w-60">
                    <x-input placeholder="Search problem, line..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
                </div>
                <div class="w-20">
                    <x-select wire:model.live="perPage" :options="[['id'=>10,'name'=>'10'],['id'=>25,'name'=>'25'],['id'=>50,'name'=>'50'],['id'=>100,'name'=>'100']]" option-value="id" option-label="name" />
                </div>
                <x-button icon="o-funnel" @click="$wire.filterDrawer = ! $wire.filterDrawer" class="btn-ghost" tooltip="Filters" />
                <x-button icon="o-arrow-down-tray" @click="$wire.exportModal = true" class="btn-success text-white" tooltip="Export Data">
                    <span class="inline">Export</span>
                </x-button>
                @can('wr.create')
                    <x-button icon="o-plus" class="btn-primary" link="{{ route('maintenance.cardty.create') }}">
                        <span class="inline">Add</span>
                    </x-button>
                @endcan
            </div>
        </x-slot:actions>
    </x-header>

    <!-- Mobile Actions -->
    <div class="grid grid-cols-4 gap-2 mb-4 sm:hidden">
        <div class="col-span-3">
            <x-input id="search-mobile" placeholder="Search problem, line..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </div>
        <div class="col-span-1">
            <x-select id="perPage-mobile" wire:model.live="perPage" :options="[['id'=>10,'name'=>'10'],['id'=>25,'name'=>'25'],['id'=>50,'name'=>'50'],['id'=>100,'name'=>'100']]" option-value="id" option-label="name" class="w-full" />
        </div>
        <div class="col-span-1">
            <x-button icon="o-funnel" @click="$wire.filterDrawer = ! $wire.filterDrawer" class="btn-ghost w-full" tooltip="Filters" />
        </div>
        <div class="col-span-2">
            <x-button icon="o-arrow-down-tray" @click="$wire.exportModal = true" class="btn-success text-white w-full" tooltip="Export Data">
                <span class="inline">Export</span>
            </x-button>
        </div>
        @can('wr.create')
            <div class="col-span-1">
                <x-button icon="o-plus" class="btn-primary w-full" link="{{ route('maintenance.cardty.create') }}">
                    <span class="inline">Add</span>
                </x-button>
            </div>
        @endcan
    </div>
    @include('livewire.maintenance.cardty.partials.index-filters')
    <x-card>
        <div class="overflow-x-auto w-full">
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
        </div>
    </x-card>

    <!-- Delete Confirmation Modal -->
    @include('livewire.maintenance.cardty.partials.index-delete-modal')

    <!-- Export Options Modal -->
    @include('livewire.maintenance.cardty.partials.index-export-modal')
</div>
