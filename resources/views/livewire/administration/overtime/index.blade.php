<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Url;
use Livewire\Attributes\Computed;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

new class extends Component {
    #[Url]
    public string $mode = 'spl';

    #[Url]
    public int $periode = 0;

    public string $activeTab = 'MTC';

    public array $myChart = [];

    public function mount()
    {
        $this->buildChart();
    }

    public function setFilter($mode, $periode)
    {
        $this->mode = $mode;
        $this->periode = $periode;
        $this->buildChart();
    }
    
    #[Computed]
    public function dates()
    {
        $today = Carbon::today();
        if ($this->mode === 'spl') {
            if ($today->day >= 16) {
                $start = Carbon::create($today->year, $today->month, 16);
                $end = $start->copy()->addMonth()->subDay()->day(15);
            } else {
                $start = Carbon::create($today->year, $today->month, 16)->subMonth();
                $end = Carbon::create($today->year, $today->month, 15);
            }
            $start->subMonths($this->periode);
            $end->subMonths($this->periode);
            
            $label = "Periode SPL: " . $start->translatedFormat('d F Y') . " s/d " . $end->translatedFormat('d F Y');
        } else {
            $target = Carbon::today()->subMonths($this->periode);
            $start = $target->copy()->startOfMonth();
            $end = $target->copy()->endOfMonth();
            $label = "Kalender Bulan " . $target->translatedFormat('F Y');
        }
        
        return [
            'start' => $start->format('Y-m-d'),
            'end' => $end->format('Y-m-d'),
            'label' => $label
        ];
    }
    
    #[Computed]
    public function targetField()
    {
        if ($this->mode === 'spl') {
            return $this->periode == 1 ? 'target_last' : 'target_new';
        } else {
            return $this->periode == 1 ? 'target_month_last' : 'target_month_new';
        }
    }
    
    #[Computed]
    public function overtimeData()
    {
        $dates = $this->dates();
        $start = $dates['start'];
        $end = $dates['end'];
        
        $sections = ['MTC', 'PE', 'ME'];
        $data = [];
        
        foreach ($sections as $section) {
            $users = User::where('team', $section)
                ->whereHas('overtimes', function($q) use ($start, $end) {
                    $q->whereBetween('date', [$start, $end]);
                })
                ->withSum(['overtimes as total_jam1' => function($q) use ($start, $end) {
                    $q->whereBetween('date', [$start, $end]);
                }], 'hours_1')
                ->withSum(['overtimes as total_jam2' => function($q) use ($start, $end) {
                    $q->whereBetween('date', [$start, $end]);
                }], 'hours_2')
                ->get()
                ->sortByDesc('total_jam2')
                ->values();
                
            $data[$section] = $users;
        }
        
        return $data;
    }
    
    #[Computed]
    public function summaryData()
    {
        $dates = $this->dates();
        $start = $dates['start'];
        $end = $dates['end'];
        $targetField = $this->targetField();

        
        $summary = DB::table('overtimes')
            ->join('users', 'overtimes.user_id', '=', 'users.id')
            ->whereBetween('overtimes.date', [$start, $end])
            ->select('users.team as section', 'users.status as contract', 
                DB::raw('SUM(overtimes.hours_1) as sjam'), 
                DB::raw('SUM(overtimes.hours_2) as sjam2'))
            ->groupBy('users.team', 'users.status')
            ->get();
            
        $targetSums = User::select('team as section', 'status as contract', 
            DB::raw("SUM($targetField) as sum_target"))
            ->groupBy('team', 'status')
            ->get()
            ->keyBy(function($item) {
                return $item->section . '-' . $item->contract;
            });
            
        foreach ($summary as $row) {
            $key = $row->section . '-' . $row->contract;
            $row->sum_target = $targetSums->has($key) ? $targetSums->get($key)->sum_target : 0;
            $row->selisih = $row->sum_target - $row->sjam;
        }
        
        return $summary;
    }

    public function buildChart()
    {
        $summary = $this->summaryData();
        $labels = [];
        $dataJam = [];
        $dataTarget = [];

        foreach ($summary as $row) {
            $labels[] = $row->section;
            $dataJam[] = round($row->sjam, 1);
            $dataTarget[] = round($row->sum_target, 1);
        }

        $this->myChart = [
            'type' => 'bar',
            'data' => [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Jam Aktual',
                        'data' => $dataJam,
                        'backgroundColor' => '#f87171', // red-400
                        'borderRadius' => 4,
                    ],
                    [
                        'label' => 'Max Target',
                        'data' => $dataTarget,
                        'backgroundColor' => '#60a5fa', // blue-400
                        'borderRadius' => 4,
                    ],
                ],
            ],
            'options' => [
                'responsive' => true,
                'plugins' => [
                    'legend' => [
                        'position' => 'bottom',
                    ],
                ]
            ]
        ];
    }

    #[Computed]
    public function topOvertime()
    {
        $allUsers = collect();
        foreach ($this->overtimeData() as $section => $users) {
            $allUsers = $allUsers->merge($users);
        }
        
        return $allUsers->sortByDesc('total_jam2')->take(5);
    }
};
?>

<div>
    @include('livewire.administration.overtime.partials.index-header')
    @include('livewire.administration.overtime.partials.index-summary')
    @include('livewire.administration.overtime.partials.index-charts')
    @include('livewire.administration.overtime.partials.index-tabs')
</div>
