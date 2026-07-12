<?php

namespace App\Services;

use App\Models\WorkOrder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class WorkOrderService
{
    public function getPaginated(int $perPage = 15, string $search = '', string $status = '')
    {
        return WorkOrder::query()
            ->select(
                'id', 'date', 'target_date', 'order_type', 'requester', 'department',
                'LineName', 'MachineNo', 'MachineName',
                'problem_description', 'foto_req',
                'pic', 'pic1', 'pic2', 'pic3', 
                'status', 'priority',
                'confirmation_note', 'foto_confirm1', 'foto_confirm2', 'actual_date'
            )
            ->when($search, fn($q) => $q->where(function ($q) use ($search) {
                $q->where('LineName', 'like', "%{$search}%")
                  ->orWhere('MachineName', 'like', "%{$search}%")
                  ->orWhere('problem_description', 'like', "%{$search}%")
                  ->orWhere('requester', 'like', "%{$search}%");
            }))
            ->when($status, fn($q) => $q->where('status', $status))
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->paginate($perPage);
    }
    
    public function getProgressSummary()
    {
        return WorkOrder::query()
            ->select(
                'pic',
                DB::raw("SUM(IF(status='Open', 1, 0)) AS open_count"),
                DB::raw("SUM(IF(status='In Progress', 1, 0)) AS progress_count"),
                DB::raw("SUM(IF(status='Done', 1, 0)) AS closed_count")
            )
            ->groupBy('pic')
            ->orderBy('pic')
            ->get();
    }

    public function create(array $data)
    {
        $data['status'] = $data['status'] ?? 'Open';
        return WorkOrder::create($data);
    }

    public function update(int $id, array $data)
    {
        $wo = WorkOrder::findOrFail($id);
        $wo->update($data);
        return $wo;
    }

    public function delete(int $id): bool
    {
        $wo = WorkOrder::findOrFail($id);
        
        // Delete associated files
        if ($wo->foto_req) Storage::disk('public')->delete('work-orders/' . $wo->foto_req);
        if ($wo->foto_confirm1) Storage::disk('public')->delete('work-orders/' . $wo->foto_confirm1);
        if ($wo->foto_confirm2) Storage::disk('public')->delete('work-orders/' . $wo->foto_confirm2);
        
        return $wo->delete();
    }
}
