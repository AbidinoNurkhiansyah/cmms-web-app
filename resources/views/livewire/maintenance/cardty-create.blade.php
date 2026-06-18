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

    public string $Date = '';
    public string $groupline = '';
    public string $LineName = '';
    public string $MachineNo = '';
    public string $MachineName = '';
    public string $DownTime = '0';
    public string $Problem = '';
    public string $Action = '';
    public string $Status = 'Temporary';
    public string $Shift = '1';
    public string $start_time = '';
    public string $finish_time = '';

    // Relational fields
    public ?int $asset_id = null;

    // Legacy Form Fields
    public string $typeofproblem = '';
    public string $sparepartName = '';
    public ?int $sparepartQty = null;
    public string $Cause = '';
    public string $worktime = '0';

    public array $pics = ['']; // Dynamic PIC array

    public $filebefore1;
    public $filebefore2;
    public $fileafter1;
    public $fileafter2;

    public array $lineNames = [];
    public Collection $machines;
    public Collection $spareparts;
    public Collection $users;

    public function mount(): void
    {
        abort_if(\Illuminate\Support\Facades\Gate::denies('wr.create'), 403, 'Unauthorized');

        $this->Date = date('Y-m-d');
        $this->Status = 'Temporary';
        $this->lineNames = Asset::whereNotNull('line_name')->distinct()->pluck('line_name')->toArray();
        $this->spareparts = SparePart::all();
        $this->machines = collect();
        $this->users = User::all();
    }

    public function searchLine(string $value = '')
    {
        if (empty($value)) {
            $this->lineNames = Asset::whereNotNull('line_name')->distinct()->pluck('line_name')->toArray();
        } else {
            $this->lineNames = Asset::whereNotNull('line_name')
                ->where([['line_name', 'like', "%{$value}%"]])
                ->distinct()
                ->pluck('line_name')
                ->toArray();
        }
    }

    public function searchMachine(string $value = '')
    {
        if ($this->LineName) {
            $query = Asset::where(['line_name' => $this->LineName]);
            if (!empty($value)) {
                $query->where([['machine_name', 'like', "%{$value}%"]]);
            }
            $this->machines = $query->get();
        } else {
            $this->machines = collect();
        }
    }

    public function searchSparepart(string $value = '')
    {
        if (empty($value)) {
            $this->spareparts = SparePart::all();
        } else {
            $this->spareparts = SparePart::where([['part_name', 'like', "%{$value}%"]])->get();
        }
    }

    public function searchUser(string $value = '')
    {
        if (empty($value)) {
            $this->users = User::all();
        } else {
            $this->users = User::where([['name', 'like', "%{$value}%"]])->get();
        }
    }

    public function updatedLineName($value)
    {
        $this->asset_id = null;
        $this->MachineNo = '';
        $this->MachineName = '';
        
        if ($value) {
            $this->machines = Asset::where(['line_name' => $value])->get();
        } else {
            $this->machines = collect();
        }
    }

    public function updatedAssetId($value)
    {
        if ($value) {
            $asset = Asset::find($value);
            if ($asset) {
                $this->MachineNo = $asset->asset_no ?? '';
                $this->MachineName = $asset->machine_name ?? '';
            }
        } else {
            $this->MachineNo = '';
            $this->MachineName = '';
        }
    }



    public function addPic()
    {
        $this->pics[] = '';
    }

    public function removePic($index)
    {
        unset($this->pics[$index]);
        $this->pics = array_values($this->pics);
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