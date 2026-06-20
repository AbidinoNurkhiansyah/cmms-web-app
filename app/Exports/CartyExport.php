<?php

namespace App\Exports;

use App\Models\Carty;
use App\Repositories\Contracts\CartyRepositoryInterface;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CartyExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function __construct(
        protected string $search = '',
        protected string $status = '',
        protected string $startDate = '',
        protected string $endDate = '',
        protected string $lineName = '',
        protected string $machineName = '',
        protected string $totalStopLine = ''
    ) {}

    public function collection()
    {
        // Instead of fetching from repo paginated, we build the query directly to get all matches.
        $query = Carty::with('spareParts')->orderByDesc('Date')->orderByDesc('id');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('LineName', 'like', "%{$this->search}%")
                  ->orWhere('MachineName', 'like', "%{$this->search}%")
                  ->orWhere('Problem', 'like', "%{$this->search}%")
                  ->orWhere('MachineNo', 'like', "%{$this->search}%");
            });
        }

        if ($this->status) {
            $query->where('Status', $this->status);
        }

        if ($this->startDate) {
            $query->whereDate('Date', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate('Date', '<=', $this->endDate);
        }

        if ($this->lineName) {
            $query->where('LineName', $this->lineName);
        }

        if ($this->machineName) {
            $query->where('MachineName', $this->machineName);
        }

        if ($this->totalStopLine) {
            if ($this->totalStopLine === '30') {
                $query->where('DownTime', '>=', 30);
            } elseif ($this->totalStopLine === '60') {
                $query->where('DownTime', '>=', 60);
            }
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Date',
            'Shift',
            'Group Line',
            'Line Name',
            'Machine No',
            'Machine Name',
            'Type Of Problem',
            'Sparepart Name',
            'Sparepart Qty',
            'Start Time',
            'Finish Time',
            'DownTime',
            'Work Time',
            'Problem',
            'Cause',
            'Action',
            'Status',
            'PIC'
        ];
    }

    public function map($carty): array
    {
        $pics = is_array($carty->pics) && count($carty->pics) > 0 
            ? implode(', ', $carty->pics) 
            : ($carty->PIC ?? '-');

        // Format spare parts
        $sparePartsNameStr = '-';
        $sparePartsQtyStr = '-';
        if ($carty->spareParts->isNotEmpty()) {
            $names = $carty->spareParts->map(function ($part) {
                return $part->part_name . ' (' . $part->pivot->qty . ')';
            })->toArray();
            $sparePartsNameStr = implode(', ', $names);
            $sparePartsQtyStr = $carty->spareParts->sum('pivot.qty');
        } elseif ($carty->sparepartName) {
            $sparePartsNameStr = $carty->sparepartName;
            $sparePartsQtyStr = $carty->sparepartQty ?? 0;
        }

        return [
            $carty->Date ? $carty->Date->format('Y-m-d') : '-',
            $carty->Shift,
            $carty->groupline,
            $carty->LineName,
            $carty->MachineNo,
            $carty->MachineName,
            $carty->typeofproblem,
            $sparePartsNameStr,
            $sparePartsQtyStr,
            $carty->start_time,
            $carty->finish_time,
            $carty->DownTime,
            $carty->worktime,
            $carty->Problem,
            $carty->Cause,
            $carty->Action,
            $carty->Status,
            $pics
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $styles = [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['argb' => 'FF4F46E5']],
            ],
        ];

        $centeredColumns = ['A', 'B', 'C', 'E', 'I', 'J', 'K', 'L', 'M', 'Q'];
        foreach ($centeredColumns as $col) {
            $styles[$col] = [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ]
            ];
        }

        return $styles;
    }
}
