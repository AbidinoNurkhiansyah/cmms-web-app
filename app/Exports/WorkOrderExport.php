<?php

namespace App\Exports;

use App\Models\WorkOrder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class WorkOrderExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    use Exportable;

    protected array $filters;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = WorkOrder::query();

        if (!empty($this->filters['start_date'])) {
            $query->whereDate('date', '>=', $this->filters['start_date']);
        }
        
        if (!empty($this->filters['end_date'])) {
            $query->whereDate('date', '<=', $this->filters['end_date']);
        }
        
        if (!empty($this->filters['team'])) {
            $query->where('pic', $this->filters['team']);
        }
        
        if (!empty($this->filters['order_type'])) {
            $query->where('order_type', $this->filters['order_type']);
        }
        
        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        return $query->with('spareparts.sparepart')->orderBy('id', 'desc');
    }

    public function headings(): array
    {
        return [
            'Order Date',
            'Target Date',
            'Actual Date',
            'Order Type',
            'Priority',
            'Requester',
            'Department',
            'Line Name',
            'Machine No',
            'Machine Name',
            'Problem Description',
            'Status',
            'Team / PIC',
            'Assigned Technicians',
            'Confirmation Note',
            'Spareparts Used',
        ];
    }

    public function map($workOrder): array
    {
        return [
            $workOrder->date ? $workOrder->date->format('Y-m-d') : '',
            $workOrder->target_date ? $workOrder->target_date->format('Y-m-d') : '',
            $workOrder->actual_date ? \Carbon\Carbon::parse($workOrder->actual_date)->format('Y-m-d') : '',
            $workOrder->order_type,
            $workOrder->priority,
            $workOrder->requester,
            $workOrder->department,
            $workOrder->LineName,
            $workOrder->MachineNo,
            $workOrder->MachineName,
            $workOrder->problem_description,
            $workOrder->status,
            $workOrder->pic,
            implode(', ', array_filter([$workOrder->pic1, $workOrder->pic2, $workOrder->pic3])),
            $workOrder->confirmation_note,
            $workOrder->spareparts->map(function ($sp) {
                return ($sp->sparepart->part_name ?? 'Unknown Item') . ' (' . $sp->qty . 'x)';
            })->implode(', '),
        ];
    }
}
