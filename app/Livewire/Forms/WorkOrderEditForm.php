<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use Livewire\Attributes\Validate;
use App\Services\WorkOrderService;
use App\Models\WorkOrder;
use Illuminate\Support\Facades\Storage;

class WorkOrderEditForm extends Form
{
    public ?int $wo_id = null;
    
    // Readonly fields
    public string $date = '';
    public string $target_date = '';
    public string $order_type = '';
    public string $requester = '';
    public string $department = '';
    public string $line_name = '';
    public string $machine_no = '';
    public string $machine_name = '';
    public string $problem = '';
    public string $priority = '';
    public ?string $existing_foto_req = null;

    // Editable fields
    public string $confirmation_note = '';
    
    #[Validate('required|string')]
    public string $status = '';
    
    public string $actual_date = '';
    public string $pic = '';
    public string $pic1 = '';
    public string $pic2 = '';
    public string $pic3 = '';

    #[Validate('nullable|image|max:2048')]
    public $foto_confirm1 = null;
    public ?string $existing_foto_confirm1 = null;
    
    #[Validate('nullable|image|max:2048')]
    public $foto_confirm2 = null;
    public ?string $existing_foto_confirm2 = null;

    #[Validate('nullable|image|max:2048')]
    public $foto_req = null;

    public function initForm(WorkOrder $wo): void
    {
        $this->reset();
        
        $this->wo_id = $wo->id;
        $this->date = $wo->date ? $wo->date->format('Y-m-d') : '';
        $this->target_date = $wo->target_date ? $wo->target_date->format('Y-m-d') : '';
        $this->order_type = $wo->order_type ?? '';
        $this->requester = $wo->requester ?? '';
        $this->department = $wo->department ?? '';
        $this->line_name = $wo->LineName ?? '';
        $this->machine_no = $wo->MachineNo ?? '';
        $this->machine_name = $wo->MachineName ?? '';
        $this->problem = $wo->problem_description ?? '';
        $this->priority = $wo->priority ?? 'Medium';
        $this->existing_foto_req = $wo->foto_req;

        $this->confirmation_note = $wo->confirmation_note ?? '';
        $this->existing_foto_confirm1 = $wo->foto_confirm1;
        $this->existing_foto_confirm2 = $wo->foto_confirm2;
        $this->status = $wo->status ?? 'Open';
        $this->actual_date = $wo->actual_date ? $wo->actual_date->format('Y-m-d') : '';
        $this->pic = $wo->pic ?? '';
        $this->pic1 = $wo->pic1 ?? '';
        $this->pic2 = $wo->pic2 ?? '';
        $this->pic3 = $wo->pic3 ?? '';
    }

    public function update(WorkOrderService $woService): void
    {
        $this->validate();

        $data = [
            'confirmation_note' => $this->confirmation_note,
            'status'            => $this->status,
            'actual_date'       => $this->actual_date ?: null,
            'pic'               => $this->pic,
            'pic1'              => $this->pic1,
            'pic2'              => $this->pic2,
            'pic3'              => $this->pic3,
        ];

        if ($this->foto_req) {
            if ($this->existing_foto_req) {
                Storage::disk('public')->delete($this->existing_foto_req);
            }
            $data['foto_req'] = $this->foto_req->store('work-orders', 'public');
        }

        if ($this->foto_confirm1) {
            if ($this->existing_foto_confirm1) {
                Storage::disk('public')->delete($this->existing_foto_confirm1);
            }
            $data['foto_confirm1'] = $this->foto_confirm1->store('work-orders', 'public');
        }

        if ($this->foto_confirm2) {
            if ($this->existing_foto_confirm2) {
                Storage::disk('public')->delete($this->existing_foto_confirm2);
            }
            $data['foto_confirm2'] = $this->foto_confirm2->store('work-orders', 'public');
        }

        $woService->update($this->wo_id, $data);
    }
}
