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

    public function layout()
    {
        return auth()->check() ? 'layouts.app' : 'layouts.guest';
    }

    public function mount(): void
    {
        $this->Date = date('Y-m-d');
        $this->Status = 'Temporary';

        $asset_id = request()->query('asset_id');
        if ($asset_id) {
            $asset = Asset::find($asset_id);
            if ($asset) {
                $this->LineName = $asset->line_name ?? '';
                $this->MachineNo = $asset->asset_no ?? '';
            }
        }

        $this->mountWithAssetSelection();
        $this->mountWithSpareparts();
        $this->mountWithPersonnel();

        if ($asset_id) {
            $this->asset_id = $asset_id;
            $this->updatedAssetId($asset_id);
        }

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
        
        if (auth()->check()) {
            return redirect()->route('maintenance.cardty');
        } else {
            // Jika discan via QR oleh operator guest
            $this->reset(['Problem', 'Action', 'DownTime', 'start_time', 'finish_time', 'pics']);
            $this->pics = [''];
            return;
        }
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
