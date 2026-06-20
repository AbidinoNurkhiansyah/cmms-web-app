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

    public int $formId;
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

    public function mount(int $id, CartyService $service): void
    {
        abort_if(\Illuminate\Support\Facades\Gate::denies('wr.update'), 403, 'Unauthorized');

        $record = $service->getById($id);
        if (!$record) {
            abort(404);
        }

        $this->formId = $id;
        $this->Date = $record->Date ? $record->Date->format('Y-m-d') : '';
        $this->groupline = $record->groupline ?? '';
        $this->LineName = $record->LineName ?? '';
        $this->MachineNo = $record->MachineNo ?? '';
        $this->MachineName = $record->MachineName ?? '';
        $this->DownTime = (string) ($record->DownTime ?? 0);
        $this->Problem = $record->Problem ?? '';
        $this->Action = $record->Action ?? '';
        $this->Status = $record->Status ?? 'Temporary';
        $this->Shift = (string) ($record->Shift ?? 1);
        $this->start_time = $record->start_time ?? '';
        $this->finish_time = $record->finish_time ?? '';

        $this->typeofproblem = $record->typeofproblem ?? '';
        $this->sparepartName = $record->sparepartName ?? '';
        $this->sparepartQty = $record->sparepartQty ?? null;
        $this->Cause = $record->Cause ?? '';
        $this->worktime = (string) ($record->worktime ?? 0);
        
        $this->pics = is_array($record->pics) && count($record->pics) > 0 ? $record->pics : [''];
        
        // Load Pivot Spareparts
        if ($record->spareParts && $record->spareParts->count() > 0) {
            foreach ($record->spareParts as $sp) {
                $this->usedSpareparts[] = [
                    'spare_part_id' => $sp->id,
                    'qty' => $sp->pivot->qty
                ];
            }
        } else {
            $this->usedSpareparts = [['spare_part_id' => '', 'qty' => 1]];
        }
        
        $this->filebefore1 = $record->filebefore1 ?? null;
        $this->filebefore2 = $record->filebefore2 ?? null;
        $this->fileafter1 = $record->fileafter1 ?? null;
        $this->fileafter2 = $record->fileafter2 ?? null;
        
        $this->mountWithAssetSelection();
        $this->mountWithSpareparts();
        $this->mountWithPersonnel();
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
            'pics' => array_values(array_filter($this->pics)),
            'start_time' => $this->start_time,
            'finish_time' => $this->finish_time,

            // Legacy Fields
            'typeofproblem' => $this->typeofproblem,
            'sparepartName' => $this->sparepartName,
            'sparepartQty' => (int) $this->sparepartQty,
            'Cause' => $this->Cause,
            'worktime' => (int) $this->worktime,

            // Pivot Data
            'usedSpareparts' => $this->usedSpareparts,
        ];

        // Only update files if new ones are uploaded
        if ($this->filebefore1)
            $data['filebefore1'] = $this->filebefore1->store('carty_images', 'public');
        if ($this->filebefore2)
            $data['filebefore2'] = $this->filebefore2->store('carty_images', 'public');
        if ($this->fileafter1)
            $data['fileafter1'] = $this->fileafter1->store('carty_images', 'public');
        if ($this->fileafter2)
            $data['fileafter2'] = $this->fileafter2->store('carty_images', 'public');

        $service->update($this->formId, $data);

        $this->success('Carty Record Updated.');
        return redirect()->route('maintenance.cardty');
    }
};
?>

<div>
    <!-- Header -->
    <x-maintenance.carty-header title="Edit Maintenance Record" class="mb-2" />

    <!-- Form Layout -->
    @include('components.maintenance.carty-form')

    <!-- Actions -->
    <x-maintenance.carty-actions submit-label="Update Record" />
</div>