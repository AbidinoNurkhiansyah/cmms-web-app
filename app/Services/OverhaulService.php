<?php

namespace App\Services;

use App\Models\Overhaul;
use Illuminate\Support\Facades\Storage;

class OverhaulService
{
    public function getPaginated(int $perPage = 15, string $search = '', string $status = '')
    {
        return Overhaul::query()
            ->when($search, fn($q) => $q->where(function ($q) use ($search) {
                $q->where('LineName', 'like', "%{$search}%")
                  ->orWhere('MachineName', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            }))
            ->when($status, fn($q) => $q->where('status', $status))
            ->orderByDesc('date_request')
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function create(array $data, $photoBefore = null, $photoAfter = null)
    {
        if ($photoBefore) {
            $data['photo_before'] = $photoBefore->store('overhaul', 'public');
        }
        if ($photoAfter) {
            $data['photo_after'] = $photoAfter->store('overhaul', 'public');
        }
        $data['status'] = $data['status'] ?? 'Open';
        return Overhaul::create($data);
    }

    public function update(int $id, array $data, $photoBefore = null, $photoAfter = null)
    {
        $record = Overhaul::findOrFail($id);
        if ($photoBefore) {
            if ($record->photo_before) Storage::disk('public')->delete($record->photo_before);
            $data['photo_before'] = $photoBefore->store('overhaul', 'public');
        }
        if ($photoAfter) {
            if ($record->photo_after) Storage::disk('public')->delete($record->photo_after);
            $data['photo_after'] = $photoAfter->store('overhaul', 'public');
        }
        $record->update($data);
        return $record;
    }

    public function delete(int $id): bool
    {
        $record = Overhaul::findOrFail($id);
        if ($record->photo_before) Storage::disk('public')->delete($record->photo_before);
        if ($record->photo_after) Storage::disk('public')->delete($record->photo_after);
        return $record->delete();
    }
}
