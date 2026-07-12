<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use Livewire\Attributes\Validate;
use App\Services\WorkOrderService;

class WorkOrderAddForm extends Form
{
    #[Validate('required|date')]
    public string $date = '';

    #[Validate('required|date')]
    public string $target_date = '';

    #[Validate('required|string')]
    public string $order_type = '';

    #[Validate('required|string')]
    public string $requester = '';

    #[Validate('required|string')]
    public string $department = '';

    public string $line_name = '';

    #[Validate('required|string')]
    public string $machine_name = '';

    public string $machine_no = '';

    #[Validate('required|string')]
    public string $problem = '';

    #[Validate('required|string')]
    public string $priority = 'Medium';

    #[Validate('nullable|image|max:2048')]
    public $foto_req = null;

    public function initForm(): void
    {
        $this->reset();
        $this->priority = 'Medium';
        $this->date = date('Y-m-d');
    }

    public function store(WorkOrderService $woService): void
    {
        $this->validate();

        $data = [
            'date'                => $this->date,
            'target_date'         => $this->target_date,
            'order_type'          => $this->order_type,
            'requester'           => $this->requester,
            'department'          => $this->department,
            'LineName'            => $this->line_name,
            'MachineNo'           => $this->machine_no,
            'MachineName'         => $this->machine_name,
            'problem_description' => $this->problem,
            'priority'            => $this->priority,
            'status'              => 'Open',
        ];

        if ($this->foto_req) {
            $data['foto_req'] = $this->foto_req->store('work-orders', 'public');
        }

        $woService->create($data);
    }
}
