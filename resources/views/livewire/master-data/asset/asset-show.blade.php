<?php

use App\Services\AssetService;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

new class extends Component {
    use WithPagination;

    public int $assetId;
    public string $assetNo;
    public string $machineName;

    // Modals
    public bool $showSparepartModal = false;
    public bool $showTpmModal = false;
    public bool $showProblemModal = false;
    public bool $showOverhaulModal = false;
    public bool $showWorkOrderModal = false;
    public bool $showOneHourModal = false;
    public bool $showAssignSparepartModal = false;

    // Assignment State
    public array $selectedSpareParts = [];
    public array $allSparePartsForAssignment = [];

    // Filters & Chart Data
    public int $chartYear;
    public int $timeChartYear;
    public array $trendData = [];
    public array $timeTrendData = [];

    public function mount(int $id, AssetService $assetService): void
    {
        $this->assetId = $id;
        $asset = $assetService->getAssetById($id);
        $this->assetNo = $asset->asset_no;
        $this->machineName = $asset->machine_name;

        $years = $assetService->getAvailableTrendYears($this->assetNo);
        $this->chartYear = $years[0] ?? (int) date('Y');
        $this->timeChartYear = $years[0] ?? (int) date('Y');

        $this->trendData = $assetService->getTrendData($this->assetNo, $this->chartYear);
        $this->timeTrendData = $assetService->getTrendData($this->assetNo, $this->timeChartYear);
    }

    public function updatedChartYear()
    {
        $assetService = app(AssetService::class);
        $this->trendData = $assetService->getTrendData($this->assetNo, $this->chartYear);
    }

    public function updatedTimeChartYear()
    {
        $assetService = app(AssetService::class);
        $this->timeTrendData = $assetService->getTrendData($this->assetNo, $this->timeChartYear);
    }

    public function openAssignSparepartModal()
    {
        $this->selectedSpareParts = DB::table('machine_spare_parts')
            ->where('asset_no', 'LIKE', "%{$this->assetNo}%")
            ->pluck('spare_part_id')
            ->map(fn($id) => (string)$id)
            ->toArray();
        $this->searchSpareParts('');
        $this->showAssignSparepartModal = true;
    }

    public function searchSpareParts(string $value = '')
    {
        $query = \App\Models\SparePart::select('id', 'part_name', 'part_number');
        if (!empty($value)) {
            $query->where(function($q) use ($value) {
                $q->where('part_name', 'like', "%{$value}%")
                  ->orWhere('part_number', 'like', "%{$value}%");
            });
        }
        
        $results = $query->orderBy('part_name')->take(50)->get();
        
        if (!empty($this->selectedSpareParts) && is_array($this->selectedSpareParts)) {
            $selected = \App\Models\SparePart::select('id', 'part_name', 'part_number')
                ->whereIn('id', $this->selectedSpareParts)
                ->get();
                
            $results = $results->merge($selected)->unique('id');
        }
        
        $this->allSparePartsForAssignment = $results->map(function($sp) {
                return [
                    'id' => (string)$sp->id,
                    'name' => $sp->part_name . ($sp->part_number ? " ({$sp->part_number})" : '')
                ];
            })->toArray();
    }

    public function syncSpareParts()
    {
        $existingPivot = DB::table('machine_spare_parts')
            ->where('asset_no', 'LIKE', "%{$this->assetNo}%")
            ->pluck('spare_part_id')
            ->toArray();
            
        $newSelection = array_map('intval', $this->selectedSpareParts);

        $toInsert = array_diff($newSelection, $existingPivot);
        $toDelete = array_diff($existingPivot, $newSelection);

        if (!empty($toDelete)) {
            DB::table('machine_spare_parts')
                ->where('asset_no', 'LIKE', "%{$this->assetNo}%")
                ->whereIn('spare_part_id', $toDelete)
                ->delete();
        }

        if (!empty($toInsert)) {
            $asset = app(AssetService::class)->getAssetById($this->assetId);
            $insertData = [];
            foreach ($toInsert as $spId) {
                $insertData[] = [
                    'spare_part_id' => $spId,
                    'line' => $asset->line_name ?? '-',
                    'asset_no' => $asset->asset_no,
                    'machine' => $asset->machine_name ?? '-',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            if (!empty($insertData)) {
                DB::table('machine_spare_parts')->insert($insertData);
            }
        }

        $this->showAssignSparepartModal = false;
        
        // Refresh chart data by re-fetching
        $assetService = app(AssetService::class);
        $this->trendData = $assetService->getTrendData($this->assetNo, $this->chartYear); // just triggering re-render if needed, actually it auto re-renders with()
    }

    public function with(AssetService $assetService): array
    {
        $asset = $assetService->getAssetById($this->assetId);
        $stats = $assetService->getAssetStats($this->assetNo, $this->machineName);
        $years = $assetService->getAvailableTrendYears($this->assetNo);

        // Fetch paginated data ONLY if modal is open to save memory and avoid N+1
        $spareparts = $this->showSparepartModal
            ? DB::table('machine_spare_parts')
                ->join('spare_parts', 'machine_spare_parts.spare_part_id', '=', 'spare_parts.id')
                ->select('spare_parts.part_name', 'spare_parts.group as part_type', 'spare_parts.use_qty as qty', 'spare_parts.rank as Rangking')
                ->where('machine_spare_parts.asset_no', 'LIKE', "%{$this->assetNo}%")
                ->orderBy('spare_parts.last_stock', 'DESC')
                ->paginate(10, ['*'], 'spPage')
            : null;

        $tpmRecords = $this->showTpmModal
            ? DB::table('cmms_tpm_checksheet')->where('machineNo', 'LIKE', "%{$this->assetNo}%")->orderBy('checked_date', 'DESC')->paginate(10, ['*'], 'tpmPage')
            : null;

        $problemRecords = $this->showProblemModal
            ? DB::table('carty')->where('MachineNo', 'LIKE', "%{$this->assetNo}%")->orderBy('date', 'DESC')->paginate(10, ['*'], 'probPage')
            : null;

        $overhaulRecords = $this->showOverhaulModal
            ? DB::table('cmms_oh_web')->where('MachineNo', 'LIKE', "%{$this->assetNo}%")->orderBy('date', 'DESC')->paginate(10, ['*'], 'ohPage')
            : null;

        $workOrders = $this->showWorkOrderModal
            ? DB::table('cmms_work_order_request')->where('MachineNo', 'LIKE', "%{$this->assetNo}%")->orderBy('date', 'DESC')->paginate(10, ['*'], 'woPage')
            : null;

        $oneHourOver = $this->showOneHourModal
            ? DB::table('one_hour_over')->where('machine', $this->machineName)->orderBy('date', 'DESC')->paginate(10, ['*'], 'ohoPage')
            : null;

        $sparePartsChartData = $assetService->getSparePartsChartData($this->assetNo);

        return compact(
            'asset',
            'stats',
            'years',
            'spareparts',
            'tpmRecords',
            'problemRecords',
            'overhaulRecords',
            'workOrders',
            'oneHourOver',
            'sparePartsChartData'
        );
    }
};
?>

<div>
    {{-- Load Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <x-header subtitle="Asset No: {{ $asset->asset_no }}" separator>
        <x-slot:title>
            <div class="flex items-center gap-3">
                <x-button icon="o-arrow-left" class="btn-circle btn-ghost btn-sm" link="/master/assets" wire:navigate
                    spinner />
                <span>{{ $asset->machine_name }}</span>
            </div>
        </x-slot:title>
    </x-header>

    {{-- Asset Detail & Spare Parts Pie Chart --}}
    @include('livewire.master-data.asset.partials.show.asset-info')

    {{-- Stats Cards --}}
    @include('livewire.master-data.asset.partials.show.stats-cards')

    {{-- Trend Charts --}}
    @include('livewire.master-data.asset.partials.show.trend-charts')

    {{-- Modals --}}
    @include('livewire.master-data.asset.partials.show.modals')

</div>