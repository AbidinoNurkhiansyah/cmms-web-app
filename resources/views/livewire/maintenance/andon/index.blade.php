<?php

use App\Services\AndonService;
use App\Livewire\Forms\AndonForm;
use App\Livewire\Traits\WithPersonnel;
use App\Livewire\Traits\WithAssetSelection;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

new class extends Component {
    use WithPagination, Toast, WithPersonnel, WithAssetSelection;

    public string $search = '';
    public string $statusFilter = '';
    public string $selectedTab = 'dashboard';

    public AndonForm $form;

    // Chart Data
    public array $chartToday = [];
    public array $chartTop10 = [];
    public array $chartDaily = [];
    public array $chartMonthly = [];
    public array $chartLine = [];

    // Modals
    public bool   $addModal             = false;
    public bool   $editModal            = false;
    public bool   $deleteModal          = false;
    public ?int   $deleteId             = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }
    
    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function mount(AndonService $andonService): void
    {
        $this->loadCharts($andonService);
        $this->mountWithPersonnel();
        $this->mountWithAssetSelection();
    }

    public function loadCharts(AndonService $andonService): void
    {
        $stats = $andonService->getDashboardStats();

        $this->chartToday = [
            'type' => 'bar',
            'data' => [
                'labels' => $stats['today']['labels'],
                'datasets' => [[
                    'label' => 'Hari Ini',
                    'data' => $stats['today']['data'],
                    'backgroundColor' => '#61b4f8ff'
                ]]
            ],
            'options' => ['responsive' => true, 'maintainAspectRatio' => false, 'plugins' => ['legend' => ['display' => false]]]
        ];

        $this->chartTop10 = [
            'type' => 'bar',
            'data' => [
                'labels' => $stats['top10Month']['labels'],
                'datasets' => [[
                    'label' => 'Top 10 Bulan Ini',
                    'data' => $stats['top10Month']['data'],
                    'backgroundColor' => '#0ac4c4ff'
                ]]
            ],
            'options' => ['responsive' => true, 'maintainAspectRatio' => false, 'plugins' => ['legend' => ['display' => false]]]
        ];

        $this->chartDaily = [
            'type' => 'bar',
            'data' => [
                'labels' => $stats['daily']['labels'],
                'datasets' => [[
                    'label' => 'Jumlah',
                    'data' => $stats['daily']['data'],
                    'backgroundColor' => '#3d97e0ff'
                ]]
            ],
            'options' => ['responsive' => true, 'maintainAspectRatio' => false, 'plugins' => ['legend' => ['display' => false]]]
        ];

        $this->chartMonthly = [
            'type' => 'bar',
            'data' => [
                'labels' => $stats['monthly']['labels'],
                'datasets' => [[
                    'label' => 'Jumlah',
                    'data' => $stats['monthly']['data'],
                    'backgroundColor' => '#4682b4'
                ]]
            ],
            'options' => ['responsive' => true, 'maintainAspectRatio' => false, 'plugins' => ['legend' => ['display' => false]]]
        ];

        $this->chartLine = [
            'type' => 'bar',
            'data' => [
                'labels' => $stats['line6Months']['labels'],
                'datasets' => [[
                    'label' => 'Jumlah',
                    'data' => $stats['line6Months']['data'],
                    'backgroundColor' => '#008080'
                ]]
            ],
            'options' => ['responsive' => true, 'maintainAspectRatio' => false, 'plugins' => ['legend' => ['display' => false]]]
        ];
    }

    public function with(AndonService $andonService): array
    {
        return [
            'records' => $andonService->getPaginated(15, $this->search, $this->statusFilter),
            'outstandingAndons' => $andonService->getOutstandingToday(),
        ];
    }

    public function openAdd(): void
    {
        $this->form->reset();
        $this->form->status = 'CALL';
        $this->form->date_shift = date('Y-m-d');
        $this->form->date_in = date('Y-m-d');
        $this->form->time_in = date('H:i');
        
        $this->LineName = '';
        $this->MachineName = '';
        
        $this->addModal = true;
    }

    public function saveAdd(AndonService $andonService): void
    {
        $this->form->line_name = $this->LineName;
        $this->form->machine = $this->MachineName;
        $this->form->store($andonService);
        $this->addModal = false;
        $this->loadCharts($andonService);
        $this->success('Andon call created.');
    }

    public function openEdit(int $id, AndonService $andonService): void
    {
        $record = $andonService->getPaginated()->getCollection()->firstWhere('id', $id);
        if(!$record) {
            $this->error('Record not found.');
            return;
        }

        $this->form->setAndon($record);
        
        $this->LineName = $this->form->line_name;
        $this->updatedLineName($this->LineName);
        $this->MachineName = $this->form->machine;

        $this->editModal = true;
    }

    public function saveEdit(AndonService $andonService): void
    {
        $this->form->line_name = $this->LineName;
        $this->form->machine = $this->MachineName;
        $this->form->update($andonService);
        $this->editModal = false;
        $this->loadCharts($andonService);
        $this->success('Andon record updated.');
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId = $id;
        $this->deleteModal = true;
    }

    public function deleteAndon(AndonService $andonService): void
    {
        if ($this->deleteId) {
            $andonService->delete($this->deleteId);
            $this->loadCharts($andonService);
            $this->success('Andon record deleted.');
        }
        $this->deleteModal = false;
        $this->deleteId = null;
    }
};
?>

<div>
    @include('livewire.maintenance.andon.partials.header')
    @include('livewire.maintenance.andon.partials.tabs')
    @include('livewire.maintenance.andon.partials.modals')
</div>
