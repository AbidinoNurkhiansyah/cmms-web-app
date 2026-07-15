<?php

use Livewire\Volt\Component;
use App\Models\Asset;
use App\Models\ChecksheetItem;
use App\Models\Checksheet;
use Illuminate\Support\Carbon;
use Mary\Traits\Toast;

new class extends Component {
    use Toast;

    public string $assetNo;
    public string $lineName = '';
    public string $machineName = '';
    public string $picSL = '';
    
    public array $results = [];
    public bool $apvProd = false;
    public bool $apvWeek = false;

    public $items;
    public bool $isFriday = false;
    public bool $hasExistingData = false;

    public bool $photoModal = false;
    public string $currentPhotoTitle = '';
    public string $currentPhotoPath = '';

    public string $filterMonth;

    public function mount($assetNo)
    {
        $this->filterMonth = Carbon::now()->format('Y-m');

        $this->assetNo = $assetNo;
        $asset = Asset::where('asset_no', $assetNo)->firstOrFail();
        $this->lineName = $asset->line_name;
        $this->machineName = $asset->machine_name;
        $this->picSL = auth()->user()->name ?? 'Unknown';

        $this->items = ChecksheetItem::where('asset_no', $assetNo)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $this->isFriday = Carbon::now()->isFriday();
        $today = Carbon::today()->toDateString();

        $transactions = Checksheet::where('asset_no', $assetNo)
            ->where('date', $today)
            ->get();

        $this->hasExistingData = $transactions->isNotEmpty();

        foreach ($transactions as $trx) {
            $this->results[$trx->cs_item_id] = $trx->result;
        }

        if ($this->hasExistingData) {
            $first = $transactions->first();
            $this->apvProd = $first->apv_prod;
            $this->apvWeek = $first->apv_week;
        }
    }

    public function save()
    {
        $rules = [];
        foreach ($this->items as $item) {
            $rules['results.' . $item->id] = 'required';
        }
        
        $this->validate($rules, [
            'results.*.required' => 'Semua poin checksheet harus diisi.'
        ]);

        $today = Carbon::today()->toDateString();

        foreach ($this->items as $item) {
            Checksheet::updateOrCreate(
                [
                    'asset_no' => $this->assetNo,
                    'date' => $today,
                    'cs_item_id' => $item->id,
                ],
                [
                    'pic_sl' => $this->picSL,
                    'result' => $this->results[$item->id] ?? null,
                    'apv_prod' => $this->apvProd,
                    'apv_week' => $this->apvWeek,
                ]
            );
        }

        $this->hasExistingData = true;
        $this->success('Checksheet berhasil disimpan!');
    }

    public function viewPhoto($title, $path)
    {
        $this->currentPhotoTitle = $title;
        $this->currentPhotoPath = $path;
        $this->photoModal = true;
    }

    /**
     * Computed property: ambil history per bulan dengan satu query
     * Hasilnya adalah Map: [cs_item_id => [day => result]]
     * Tidak pernah disimpan sebagai public property sehingga tidak membebani Livewire state.
     */
    public function getHistoryProperty()
    {
        $month = Carbon::createFromFormat('Y-m', $this->filterMonth);
        $daysInMonth = $month->daysInMonth;

        // Satu query saja untuk seluruh bulan, tanpa N+1
        $rows = Checksheet::where('asset_no', $this->assetNo)
            ->whereBetween('date', [
                $month->startOfMonth()->toDateString(),
                $month->copy()->endOfMonth()->toDateString(),
            ])
            ->select('cs_item_id', 'date', 'result')
            ->get();

        // Kelompokkan: [item_id][day] = result
        $mapped = [];
        foreach ($rows as $row) {
            $day = Carbon::parse($row->date)->day;
            $mapped[$row->cs_item_id][$day] = $row->result;
        }

        return [
            'daysInMonth' => $daysInMonth,
            'month'       => $month,
            'data'        => $mapped,
        ];
    }

    /**
     * Evaluasi apakah nilai aktual sesuai dengan standard.
     * Returns: true = OK (hijau), false = NG (merah), null = tidak bisa dievaluasi (biru)
     */
    public function evaluateResult(string $val, ?string $std): ?bool
    {
        if (empty($std)) return null;

        // Normalisasi input: ganti koma ke titik, hapus semua whitespace (spasi, enter, tab)
        $val = preg_replace('/\s+/', '', str_replace(',', '.', $val));
        if (!is_numeric($val)) return null;
        $v = (float) $val;

        // Normalisasi standar: lowercase, ganti koma ke titik, hapus semua whitespace
        $std = strtolower(preg_replace('/\s+/', '', str_replace(',', '.', $std)));

        // Hapus satuan di akhir: mpa, mm, sec, kg, %, rpm, dll
        $std = preg_replace('/[a-z%]+[a-z0-9\/\°]*$/i', '', $std);
        $std = rtrim($std, '.-');

        // Exact numeric
        if (is_numeric($std)) {
            return abs($v - (float)$std) < 0.0001;
        }

        // Max: "max10", "<=10", "<10"
        if (preg_match('/^(max|<=|<)([\d\.]+)$/', $std, $m)) {
            $limit = (float) $m[2];
            return ($m[1] === '<') ? $v < $limit : $v <= $limit;
        }

        // Min: "min10", ">=10", ">10"
        if (preg_match('/^(min|>=|>)([\d\.]+)$/', $std, $m)) {
            $limit = (float) $m[2];
            return ($m[1] === '>') ? $v > $limit : $v >= $limit;
        }

        // Range: "0.30-0.50" or "10~20"
        if (preg_match('/^([\d\.]+)[-~]([\d\.]+)$/', $std, $m)) {
            return $v >= (float)$m[1] && $v <= (float)$m[2];
        }

        // Toleransi plus-minus: "5.0±0.5" or "5.0+-0.5"
        if (preg_match('/^([\d\.]+)(?:\+\-|±|\+\/\-)([\d\.]+)$/u', $std, $m)) {
            $base = (float) $m[1];
            $tol  = (float) $m[2];
            return $v >= ($base - $tol) && $v <= ($base + $tol);
        }

        return null;
    }
};
?>

<div>
    @include('livewire.maintenance.checksheet.partials.detail-header')

    <div x-data="{ activeTab: 'form' }">
        <div class="tabs tabs-boxed mb-6 bg-base-200 p-1">
            <a class="tab font-bold flex-1" 
               :class="{ 'tab-active bg-primary text-primary-content': activeTab === 'form' }" 
               @click.prevent="activeTab = 'form'"
               href="#">
               <x-icon name="o-clipboard-document-check" class="w-4 h-4 mr-2" />
               Form Checksheet
            </a>
            <a class="tab font-bold flex-1" 
               :class="{ 'tab-active bg-primary text-primary-content': activeTab === 'history' }" 
               @click.prevent="activeTab = 'history'"
               href="#">
               <x-icon name="o-calendar-days" class="w-4 h-4 mr-2" />
               Monthly History
            </a>
        </div>

        <div x-show="activeTab === 'form'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
            @include('livewire.maintenance.checksheet.partials.detail-form')
        </div>

        <div x-show="activeTab === 'history'" style="display: none;" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
            @include('livewire.maintenance.checksheet.partials.detail-history')
        </div>
    </div>

    @include('livewire.maintenance.checksheet.partials.detail-modal')
</div>