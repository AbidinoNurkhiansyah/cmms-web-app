<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use App\Models\TpmChecksheet;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Mary\Traits\Toast;
use App\Livewire\DeepCleaning\Checksheet\Traits\WithChecksheetModals;

new #[Layout('layouts.app')] #[Title('TPM Checksheet')] class extends Component {
    use Toast, WithChecksheetModals;

    // Filters
    public $selectedType = '';
    public $selectedYear = '';
    public $selectedMachine = '';
    
    public int $chartKey = 0;

    // Auth permissions
    public $canEdit = false;

    // Chart configurations
    public array $myChart = [];
    public array $myChartKelurusan = [];
    public array $myChartPutaran = [];

    public function mount()
    {
        // Simple auth check for edit/input like reference
        $user = Auth::user();
        if ($user) {
            // Adjust to your actual roles/permissions
            $this->canEdit = in_array($user->jid_no ?? '', ['JID01497', 'JID02589']) || $user->hasRole('Admin') || $user->hasRole('Manager');
        }
        $this->selectedYear = date('Y');
    }

    // Lifecycle hooks to cascade dropdowns
    public function updatedSelectedType()
    {
        $this->selectedMachine = '';
        $this->updateCharts();
    }

    public function updatedSelectedYear()
    {
        $this->selectedMachine = '';
        $this->updateCharts();
    }

    public function updatedSelectedMachine()
    {
        $this->updateCharts();
    }

    private function updateCharts()
    {
        $this->chartKey++;
        $data = $this->chartData();
        if (!$data) {
            $this->myChart = [];
            $this->myChartKelurusan = [];
            $this->myChartPutaran = [];
            return;
        }

        if ($this->selectedType === 'RUN OUT') {
            $this->myChartKelurusan = [
                'type' => 'line',
                'data' => [
                    'labels' => $data['labels'],
                    'datasets' => [
                        [
                            'label' => 'Act Kelurusan',
                            'data' => $data['actuals'],
                            'borderColor' => '#2196f3',
                            'backgroundColor' => 'rgba(33, 150, 243, 0.2)',
                            'tension' => 0.3, 'fill' => false
                        ],
                        [
                            'label' => 'Max Kelurusan (10μ)',
                            'data' => array_fill(0, 12, 10),
                            'borderColor' => '#e91e63',
                            'borderDash' => [5, 5], 'fill' => false
                        ]
                    ]
                ],
                'options' => ['responsive' => true, 'maintainAspectRatio' => false, 'scales' => ['y' => ['beginAtZero' => true, 'max' => 40]]]
            ];

            $this->myChartPutaran = [
                'type' => 'line',
                'data' => [
                    'labels' => $data['labels'],
                    'datasets' => [
                        [
                            'label' => 'Act Putaran',
                            'data' => $data['actualsPutaran'],
                            'borderColor' => 'green',
                            'backgroundColor' => 'rgba(0, 255, 0, 0.2)',
                            'tension' => 0.3, 'fill' => false
                        ],
                        [
                            'label' => 'Max Putaran (5μ)',
                            'data' => array_fill(0, 12, 5),
                            'borderColor' => '#ff9800',
                            'borderDash' => [5, 5], 'fill' => false
                        ]
                    ]
                ],
                'options' => ['responsive' => true, 'maintainAspectRatio' => false, 'scales' => ['y' => ['beginAtZero' => true, 'max' => 40]]]
            ];
        } else {
            $datasets = [];
            if ($this->selectedType === 'CLAMP ARBOR') {
                $datasets[] = [
                    'label' => 'Actual kN',
                    'data' => $data['actuals'],
                    'borderColor' => '#00bcd4',
                    'tension' => 0.3, 'fill' => false
                ];
                $datasets[] = [
                    'label' => $data['isBt30'] ? 'Lower Limit kN (2)' : 'Lower Limit kN (5)',
                    'data' => $data['isBt30'] ? array_fill(0, 12, 2) : array_fill(0, 12, 5),
                    'borderColor' => '#ff5722',
                    'borderDash' => [5, 5], 'fill' => false
                ];

                if (!$data['isBt30']) {
                    $datasets[] = [
                        'label' => 'Warning kN (7)',
                        'data' => array_fill(0, 12, 7),
                        'borderColor' => '#ff9800',
                        'borderDash' => [5, 5], 'fill' => false
                    ];
                }
            } elseif ($this->selectedType === 'GATA-GATA') {
                $datasets[] = [
                    'label' => 'Actual mm',
                    'data' => $data['actuals'],
                    'borderColor' => '#4CAF50',
                    'tension' => 0.3, 'fill' => false
                ];
                $datasets[] = [
                    'label' => 'Warning 2mm (1)',
                    'data' => array_fill(0, 12, 1),
                    'borderColor' => '#ff9800',
                    'borderDash' => [5, 5], 'fill' => false
                ];
                $datasets[] = [
                    'label' => 'Standard 2mm',
                    'data' => array_fill(0, 12, 2),
                    'borderColor' => '#9c27b0',
                    'borderDash' => [5, 5], 'fill' => false
                ];
            }

            $this->myChart = [
                'type' => 'line',
                'data' => [
                    'labels' => $data['labels'],
                    'datasets' => $datasets
                ],
                'options' => ['responsive' => true, 'maintainAspectRatio' => false, 'scales' => ['y' => ['beginAtZero' => true, 'max' => 20]]]
            ];
        }
    }

    // --- Computed Properties ---

    #[Computed]
    public function availableTypes()
    {
        return [
            ['id' => 'GATA-GATA', 'name' => 'GATA-GATA'],
            ['id' => 'CLAMP ARBOR', 'name' => 'CLAMP ARBOR'],
            ['id' => 'RUN OUT', 'name' => 'RUN OUT'],
        ];
    }

    #[Computed]
    public function availableYears()
    {
        if ($this->selectedType) {
            return TpmChecksheet::selectRaw('YEAR(checked_date) as year')
                ->where('type', $this->selectedType)
                ->distinct()
                ->orderBy('year', 'desc')
                ->pluck('year')
                ->map(fn($year) => ['id' => $year, 'name' => (string)$year]);
        }
        return [];
    }

    #[Computed]
    public function availableMachines()
    {
        if ($this->selectedType && $this->selectedYear) {
            return TpmChecksheet::with('asset:id,asset_no,machine_name,line_name')
                ->where('type', $this->selectedType)
                ->whereYear('checked_date', $this->selectedYear)
                ->select('machineNo')
                ->distinct()
                ->get()
                ->map(function($record) {
                    $asset = $record->asset;
                    $name = $asset ? $asset->line_name . ' - ' . $asset->machine_name . ' (' . $record->machineNo . ')' : $record->machineNo;
                    return ['id' => $record->machineNo, 'name' => $name];
                });
        }
        return [];
    }

    public function chartData()
    {
        if (!$this->selectedType || !$this->selectedYear || !$this->selectedMachine) {
            return null;
        }

        $records = TpmChecksheet::where('type', $this->selectedType)
            ->where('machineNo', $this->selectedMachine)
            ->whereYear('checked_date', $this->selectedYear)
            ->orderBy('checked_date', 'asc')
            ->get()
            ->keyBy(function($item) {
                return Carbon::parse($item->checked_date)->format('n');
            });

        $labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $actuals = [];
        $actualsPutaran = [];
        $remarks = [];

        for ($i = 1; $i <= 12; $i++) {
            $record = $records->get($i);
            $remarks[] = $record ? $record->remark : null;

            if ($this->selectedType === 'GATA-GATA') {
                $actuals[] = $record ? (float) $record->gata_mm : null;
            } elseif ($this->selectedType === 'CLAMP ARBOR') {
                $actuals[] = $record ? (float) $record->clamp_kN : null;
            } elseif ($this->selectedType === 'RUN OUT') {
                $actuals[] = $record ? (float) $record->run_out_kelurusan : null;
                $actualsPutaran[] = $record ? (float) $record->run_out_putaran : null;
            }
        }

        $lowerLimit2Machines = ['24OMID022', '24OMID021', '20OMID020', '20OMID019', '11OMID004', '11OMID003'];
        $isBt30 = in_array($this->selectedMachine, $lowerLimit2Machines);

        return [
            'labels' => $labels,
            'actuals' => $actuals,
            'actualsPutaran' => $actualsPutaran,
            'remarks' => $remarks,
            'isBt30' => $isBt30,
        ];
    }

}; ?>

<div>
    @include('livewire.deep-cleaning.checksheet.partials.header')
    @include('livewire.deep-cleaning.checksheet.partials.filters')
    @include('livewire.deep-cleaning.checksheet.partials.charts')
    
    <!-- MODALS -->
    @include('livewire.deep-cleaning.checksheet.partials.generate-modal')
    @include('livewire.deep-cleaning.checksheet.partials.input-modal')
</div>
