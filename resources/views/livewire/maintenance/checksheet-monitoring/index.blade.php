<?php

use Livewire\Volt\Component;
use Mary\Traits\Toast;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

new class extends Component {
    use Toast;

    public string $selectedLine = '';
    public string $selectedMonth = '';
    public string $selectedYear = '';
    
    // Modal
    public bool $noteModal = false;
    public string $modalMachine = '';
    public string $modalDate = '';
    public string $modalNotes = '';
    public string $modalInspector = '';
    public string $modalAssetNo = '';
    
    public string $leaderFollowUp = '';

    public function mount()
    {
        $this->selectedMonth = date('m');
        $this->selectedYear = date('Y');
    }

    public function with(): array
    {
        $lines = DB::table('assets')
            ->select('line_name')
            ->whereNotNull('line_name')
            ->distinct()
            ->orderBy('line_name')
            ->pluck('line_name');

        $daysInMonth = Carbon::create($this->selectedYear, $this->selectedMonth)->daysInMonth;
        
        $machines = [];
        $resultMap = [];
        $notesMap = [];
        $qtyMap = [];

        // Fetch Machines
        $query = DB::table('assets')
            ->select('asset_no', 'machine_name', 'line_name')
            ->orderBy('line_name');
            
        if ($this->selectedLine) {
            $query->where('line_name', $this->selectedLine);
        }
        
        $machines = $query->get();

        if ($machines->count() > 0) {
            $startDate = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->toDateString();
            $endDate = Carbon::create($this->selectedYear, $this->selectedMonth, $daysInMonth)->toDateString();
            $assetNos = $machines->pluck('asset_no')->toArray();
            
            // Get Checksheet Qty per asset
            $qtyResult = DB::table('cmms_cs_items')
                ->whereIn('asset_no', $assetNos)
                ->select('asset_no', DB::raw('COUNT(*) as qty'))
                ->groupBy('asset_no')
                ->get();
                
            foreach($qtyResult as $row) {
                $qtyMap[$row->asset_no] = $row->qty;
            }

            // Get Transactions (Avoid N+1, single query)
            $trxData = DB::table('cmms_cs_trx')
                ->whereIn('asset_no', $assetNos)
                ->whereBetween('date', [$startDate, $endDate])
                ->select('asset_no', 'date', 'keterangan', 'pic_sl')
                ->get();

            // Grouping data in PHP (Avoids complex DB logic, scalable for UI)
            foreach ($trxData as $trx) {
                $d = Carbon::parse($trx->date)->format('Y-m-d');
                $asset = $trx->asset_no;
                
                if (!isset($resultMap[$asset][$d])) {
                    $resultMap[$asset][$d] = 0;
                    $notesMap[$asset][$d] = [
                        'notes' => [],
                        'unresolved' => 0,
                        'inspector' => null
                    ];
                }
                
                $resultMap[$asset][$d]++;
                
                // Evaluasi Keterangan
                $rawKet = $trx->keterangan ?? '';
                // Cek apakah ada teks yg valid (bukan sekedar titik/dash/enter)
                $cleanKet = trim(str_replace(['.', '-', "\r", "\n"], '', $rawKet));
                
                if ($cleanKet !== '') {
                    $notesMap[$asset][$d]['notes'][] = trim($rawKet);
                    
                    // Cek jika belum di-resolve
                    if (strpos(strtoupper($rawKet), '[DONE:') === false) {
                        $notesMap[$asset][$d]['unresolved']++;
                    }
                    $notesMap[$asset][$d]['inspector'] = $trx->pic_sl;
                }
            }
        }

        return [
            'lines'       => $lines,
            'machines'    => $machines,
            'daysInMonth' => $daysInMonth,
            'resultMap'   => $resultMap,
            'notesMap'    => $notesMap,
            'qtyMap'      => $qtyMap,
            'baseDate'    => Carbon::create($this->selectedYear, $this->selectedMonth, 1)
        ];
    }
    
    public function openNoteModal($assetNo, $machineName, $date, $inspector)
    {
        $this->modalAssetNo = $assetNo;
        $this->modalMachine = $machineName;
        $this->modalDate = Carbon::parse($date)->format('d F Y');
        $this->modalInspector = $inspector;
        $this->leaderFollowUp = '';
        
        // Ambil notes terbaru dari DB
        $records = DB::table('cmms_cs_trx')
            ->where('asset_no', $assetNo)
            ->where('date', $date)
            ->whereNotNull('keterangan')
            ->where('keterangan', '!=', '')
            ->get();
            
        $notes = [];
        foreach($records as $rec) {
            $cleanKet = trim(str_replace(['.', '-', "\r", "\n"], '', $rec->keterangan));
            if ($cleanKet !== '') {
                $notes[] = trim($rec->keterangan);
            }
        }
        
        // Filter duplikat
        $this->modalNotes = implode("\n\n---\n\n", array_unique($notes));
        $this->noteModal = true;
    }
    
    public function saveFollowUp()
    {
        $this->validate([
            'leaderFollowUp' => 'required|string|min:3'
        ]);
        
        $date = Carbon::parse($this->modalDate)->format('Y-m-d');
        
        // Cari baris trx yg punya keterangan tapi belum DONE
        $records = DB::table('cmms_cs_trx')
            ->where('asset_no', $this->modalAssetNo)
            ->where('date', $date)
            ->whereNotNull('keterangan')
            ->where('keterangan', 'NOT LIKE', '%[DONE:%')
            ->get();
            
        if($records->isEmpty()) {
            $this->error('Tidak ada note yang butuh follow up.');
            return;
        }
        
        $appendStr = "\n\n[DONE: " . $this->leaderFollowUp . " by Leader]";
        
        foreach($records as $rec) {
            DB::table('cmms_cs_trx')
                ->where('id', $rec->id)
                ->update([
                    'keterangan' => $rec->keterangan . $appendStr
                ]);
        }
        
        $this->noteModal = false;
        $this->success('Follow up berhasil disimpan!');
    }
};
?>

<div>
    <x-header title="Monitoring Checksheet Matrix" separator progress-indicator>
        <x-slot:actions class="flex items-center gap-2">
            <x-select 
                wire:model.live="selectedLine" 
                :options="$lines->map(fn($l) => ['id' => $l, 'name' => $l])"
                placeholder="-- All Lines --"
                class="select-sm w-40" />

            <x-select 
                wire:model.live="selectedMonth" 
                :options="collect(range(1, 12))->map(fn($m) => ['id' => str_pad($m, 2, '0', STR_PAD_LEFT), 'name' => date('F', mktime(0, 0, 0, $m, 1))])"
                class="select-sm w-36" />

            <x-select 
                wire:model.live="selectedYear" 
                :options="collect(range(date('Y') - 5, date('Y')))->map(fn($y) => ['id' => $y, 'name' => $y])"
                class="select-sm w-28" />
        </x-slot:actions>
    </x-header>

    <x-card class="mb-6 shadow-sm p-0">
        @include('livewire.maintenance.checksheet-monitoring.partials.table')
    </x-card>

    @include('livewire.maintenance.checksheet-monitoring.partials.modal')
</div>
