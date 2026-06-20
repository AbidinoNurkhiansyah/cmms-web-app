<?php

use App\Services\CartyService;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;
use App\Models\Asset;
use App\Models\SparePart;
use App\Models\User;
use Illuminate\Support\Collection;

new class extends Component {
    use WithFileUploads, Toast;
    use \App\Livewire\Traits\WithAssetSelection;
    use \App\Livewire\Traits\WithSpareparts;
    use \App\Livewire\Traits\WithPersonnel;

    public string $Date = '';
    public string $groupline = '';
    public string $DownTime = '0';
    public string $Problem = '';
    public string $Action = '';
    public string $Status = 'Temporary';
    public string $Shift = '1';
    public string $start_time = '';
    public string $finish_time = '';

    // Legacy Form Fields
    public string $typeofproblem = '';
    public string $Cause = '';
    public string $worktime = '0';

    public $filebefore1;
    public $filebefore2;
    public $fileafter1;
    public $fileafter2;

    public function mount(): void
    {
        abort_if(\Illuminate\Support\Facades\Gate::denies('wr.create'), 403, 'Unauthorized');

        $this->Date = date('Y-m-d');
        $this->Status = 'Temporary';

        $this->mountWithAssetSelection();
        $this->mountWithSpareparts();
        $this->mountWithPersonnel();

        // Initialize empty pics array if needed
        if (empty($this->pics)) {
            $this->pics = [''];
        }
    }

    public function save(CartyService $service)
    {
        $this->validate([
            'Date' => 'required|date',
            'LineName' => 'required|string',
            'MachineName' => 'required|string',
        ]);

        $data = [
            'Date' => $this->Date,
            'groupline' => $this->groupline,
            'LineName' => $this->LineName,
            'MachineNo' => $this->MachineNo,
            'MachineName' => $this->MachineName,
            'DownTime' => (int) $this->DownTime,
            'Problem' => $this->Problem,
            'Action' => $this->Action,
            'Status' => $this->Status,
            'Shift' => (int) $this->Shift,
            'start_time' => $this->start_time,
            'finish_time' => $this->finish_time,
            'pics' => array_values(array_filter($this->pics)),

            // Legacy Fields
            'typeofproblem' => $this->typeofproblem,
            'sparepartName' => $this->sparepartName,
            'sparepartQty' => (int) $this->sparepartQty,
            'Cause' => $this->Cause,
            'worktime' => (int) $this->worktime,

            // Pivot Data
            'usedSpareparts' => $this->usedSpareparts,
        ];

        // Handle File Uploads
        if ($this->filebefore1)
            $data['filebefore1'] = $this->filebefore1->store('carty_images', 'public');
        if ($this->filebefore2)
            $data['filebefore2'] = $this->filebefore2->store('carty_images', 'public');
        if ($this->fileafter1)
            $data['fileafter1'] = $this->fileafter1->store('carty_images', 'public');
        if ($this->fileafter2)
            $data['fileafter2'] = $this->fileafter2->store('carty_images', 'public');

        $service->create($data);

        $this->success('Carty Record Created.');
        return redirect()->route('maintenance.cardty');
    }
};
?>

<div>
    <!-- Header -->
    <x-maintenance.carty-header title="Create Maintenance Record" />

    <!-- Form Layout -->
    @include('components.maintenance.carty-form')

    <!-- Actions -->
    <x-maintenance.carty-actions submit-label="Save Record" />
</div>
