<?php

use App\Services\CartyService;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;

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
    public string $Status = 'Open';
    public string $Shift = '1';
    public string $PIC = '';
    public string $pic_repair = '';
    public string $start_time = '';
    public string $finish_time = '';

    // Legacy Form Fields
    public string $equipment = '';
    public string $classification = '';
    public string $typeofproblem = '';
    public string $sparepartName = '';
    public string $sparepartType = '';
    public string $Cause = '';
    public string $worktime = '0';
    public string $stopline = '0';
    public string $pic2 = '';
    public string $pic3 = '';

    public $filebefore1;
    public $filebefore2;
    public $fileafter1;
    public $fileafter2;

    public function mount(): void
    {
        $this->Date = date('Y-m-d');
        $this->Status = 'Open';
        $this->Shift = '1';
        $this->DownTime = '0';
        $this->worktime = '0';
        $this->stopline = '0';
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
            'PIC' => $this->PIC,
            'pic_repair' => $this->pic_repair,
            'start_time' => $this->start_time,
            'finish_time' => $this->finish_time,

            // Legacy Fields
            'equipment' => $this->equipment,
            'classification' => $this->classification,
            'typeofproblem' => $this->typeofproblem,
            'sparepartName' => $this->sparepartName,
            'sparepartType' => $this->sparepartType,
            'Cause' => $this->Cause,
            'worktime' => (int) $this->worktime,
            'stopline' => (int) $this->stopline,
            'pic2' => $this->pic2,
            'pic3' => $this->pic3,
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
    <x-header separator class="mb-4">
        <x-slot:title>
            <div class="flex items-center gap-2">
                <x-button icon="o-arrow-left" class="btn-circle btn-ghost btn-sm"
                    link="{{ route('maintenance.cardty') }}" tooltip-left="Back to List" />
                <span class="text-xl">Create Maintenance Record</span>
            </div>
        </x-slot:title>
    </x-header>

    <!-- Form Layout -->
    @include('components.maintenance.carty-form')

    <!-- Actions -->
    <div class="mt-4 flex justify-end gap-2">
        <x-button label="Cancel" link="{{ route('maintenance.cardty') }}"
            class="btn-ghost hover:bg-base-200 dark:hover:bg-gray-700" />
        <x-button label="Save Record" wire:click="save" icon="o-check" class="btn-primary" spinner="save" />
    </div>
</div>