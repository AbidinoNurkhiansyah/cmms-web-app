<?php

use App\Models\Carty;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public $selectedYear = '';
    public $selectedMonth = '';
    public $selectedBy = ''; // 'Machine' or 'Line'
    
    public $years = [];
    public $months = [];
    
    // Drill down modal
    public bool $detailModal = false;
    public string $detailCategory = '';

    public array $chartData = [];
    
    public function mount()
    {
        $this->years = Carty::selectRaw('DISTINCT YEAR(Date) as tahun')
            ->whereNotNull('Date')
            ->whereRaw('YEAR(Date) != 0')
            ->orderByDesc('tahun')
            ->pluck('tahun')
            ->toArray();
    }

    public function updatedSelectedYear()
    {
        $this->selectedMonth = '';
        $this->months = $this->selectedYear 
            ? Carty::selectRaw('DISTINCT MONTH(Date) as bulan')
                ->whereYear('Date', $this->selectedYear)
                ->orderBy('bulan')
                ->pluck('bulan')
                ->toArray() 
            : [];
            
        $this->refreshChart();
    }
    
    public function updatedSelectedMonth()
    {
        $this->refreshChart();
    }
    
    public function updatedSelectedBy()
    {
        $this->refreshChart();
    }
    
    private function refreshChart()
    {
        $this->resetPage();
        $this->chartData = $this->getChartData();
    }
    
    public function resetFilters()
    {
        $this->selectedYear = '';
        $this->selectedMonth = '';
        $this->selectedBy = '';
        $this->months = [];
        $this->chartData = [];
        $this->resetPage();
    }

    public function getChartData()
    {
        if (!$this->selectedYear || !$this->selectedMonth || !$this->selectedBy) {
            return [];
        }

        $groupColumn = $this->selectedBy === 'Line' ? 'LineName' : 'MachineName';

        $data = Carty::query()
            ->select($groupColumn . ' as category', DB::raw('COUNT(*) as total'))
            ->whereYear('Date', $this->selectedYear)
            ->whereMonth('Date', $this->selectedMonth)
            ->whereNotNull($groupColumn)
            ->where($groupColumn, '!=', '')
            ->groupBy($groupColumn)
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        return $data->toArray();
    }

    public function showDetail($category)
    {
        $this->detailCategory = $category;
        $this->detailModal = true;
        $this->resetPage();
    }

    public function with()
    {
        $details = null;
        if ($this->detailModal && $this->detailCategory && $this->selectedYear && $this->selectedMonth && $this->selectedBy) {
            $groupColumn = $this->selectedBy === 'Line' ? 'LineName' : 'MachineName';
            
            $details = Carty::query()
                ->select('Date', 'groupline', 'worktime', 'DownTime', 'Problem', 'Cause', 'MachineName', 'LineName')
                ->whereYear('Date', $this->selectedYear)
                ->whereMonth('Date', $this->selectedMonth)
                ->where($groupColumn, $this->detailCategory)
                ->orderByDesc('Date')
                ->paginate(10);
        }

        // Map months to names
        $monthNames = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
        ];

        return [
            'details' => $details,
            'monthNames' => $monthNames,
        ];
    }
};
?>

<div>
    <x-header title="Problem Analysis Dashboard" separator>
    </x-header>

    @include('livewire.problem-analysis.partials.filter')
    @include('livewire.problem-analysis.partials.chart')
    @include('livewire.problem-analysis.partials.modal')

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
    @endpush
</div>
