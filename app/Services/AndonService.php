<?php

namespace App\Services;

use App\Models\Andon;

class AndonService
{
    public function getPaginated(int $perPage = 15, string $search = '', string $status = '')
    {
        return Andon::query()
            ->when($search, fn($q) => $q->where(function ($q) use ($search) {
                $q->where('line_name', 'like', "%{$search}%")
                  ->orWhere('machine', 'like', "%{$search}%")
                  ->orWhere('stop_info', 'like', "%{$search}%");
            }))
            ->when($status, fn($q) => $q->where('status', $status))
            ->orderByDesc('date_in')
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function create(array $data)
    {
        $data['status'] = $data['status'] ?? 'CALL';
        return Andon::create($data);
    }

    public function update(int $id, array $data)
    {
        $record = Andon::findOrFail($id);
        $record->update($data);
        return $record;
    }

    public function delete(int $id): bool
    {
        return Andon::findOrFail($id)->delete();
    }
}
