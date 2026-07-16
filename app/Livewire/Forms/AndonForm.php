<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use Livewire\Attributes\Validate;
use App\Services\AndonService;

class AndonForm extends Form
{
    public ?int $formId = null;

    #[Validate('required|date')]
    public string $date_shift = '';

    #[Validate('required|date')]
    public string $date_in = '';

    #[Validate('required')]
    public string $time_in = '';

    #[Validate('required|string')]
    public string $line_name = '';

    #[Validate('required|string')]
    public string $machine = '';

    public string $shift = '1';
    public string $status = 'CALL';
    public string $stop_info = '';

    #[Validate('required|string', as: 'PIC Name')]
    public string $name_pic = '';

    public string $finish_time = '';
    public bool $mechanic = false;
    public bool $electric = false;
    public string $cause_actual = '';
    public string $preventive = '';
    public string $hasil_repair = '';

    public function setAndon(?object $record): void
    {
        if (!$record) return;

        $this->formId       = $record->id;
        $this->date_shift   = $record->date_shift ? $record->date_shift->format('Y-m-d') : '';
        $this->date_in      = $record->date_in ? $record->date_in->format('Y-m-d') : '';
        $this->time_in      = $record->time_in ? $record->time_in->format('H:i') : '';
        $this->line_name    = $record->line_name ?? '';
        $this->machine      = $record->machine ?? '';
        $this->shift        = $record->shift ?? '1';
        $this->status       = $record->status ?? 'CALL';
        $this->stop_info    = $record->stop_info ?? '';
        $this->name_pic     = $record->name_pic ?? '';
        
        $this->finish_time  = $record->finish_time ? $record->finish_time->format('H:i') : '';
        $this->mechanic     = $record->mechanic ?? false;
        $this->electric     = $record->electric ?? false;
        $this->cause_actual = $record->cause_actual ?? '';
        $this->preventive   = $record->preventive ?? '';
        $this->hasil_repair = $record->hasil_repair ?? '';
    }

    public function store(AndonService $andonService): void
    {
        $this->validate();

        $andonService->create([
            'date_shift'   => $this->date_shift,
            'date_in'      => $this->date_in,
            'time_in'      => $this->date_in . ' ' . $this->time_in . ':00',
            'line_name'    => $this->line_name,
            'machine'      => $this->machine,
            'shift'        => $this->shift,
            'status'       => $this->status,
            'stop_info'    => $this->stop_info,
            'name_pic'     => $this->name_pic,
        ]);

        $this->reset();
    }

    public function update(AndonService $andonService): void
    {
        $this->validate();

        $data = [
            'date_shift'   => $this->date_shift,
            'date_in'      => $this->date_in,
            'time_in'      => $this->date_in . ' ' . $this->time_in . ($this->time_in && strlen($this->time_in) == 5 ? ':00' : ''),
            'line_name'    => $this->line_name,
            'machine'      => $this->machine,
            'shift'        => $this->shift,
            'status'       => $this->status,
            'stop_info'    => $this->stop_info,
            'name_pic'     => $this->name_pic,
            'mechanic'     => $this->mechanic,
            'electric'     => $this->electric,
            'cause_actual' => $this->cause_actual,
            'preventive'   => $this->preventive,
            'hasil_repair' => $this->hasil_repair,
        ];

        if ($this->finish_time) {
            $data['finish_time'] = $this->date_in . ' ' . $this->finish_time . ($this->finish_time && strlen($this->finish_time) == 5 ? ':00' : '');
        }

        $andonService->update($this->formId, $data);
        
        $this->reset();
    }
}
