<?php

namespace App\Services;

use App\Models\WorkOrder;
use Illuminate\Support\Facades\Storage;

class WorkOrderService
{
    public function getPaginated(int $perPage = 15, string $search = '', string $status = '')
    {
        return WorkOrder::query()
            ->when($search, fn($q) => $q->where(function ($q) use ($search) {
                $q->where('LineName', 'like', "%{$search}%")
                  ->orWhere('MachineName', 'like', "%{$search}%")
                  ->orWhere('problem_description', 'like', "%{$search}%");
            }))
            ->when($status, fn($q) => $q->where('status', $status))
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->paginate($perPage);
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
        return WorkOrder::findOrFail($id)->delete();
    }
}
