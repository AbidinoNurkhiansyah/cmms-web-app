<?php

namespace App\Services;

use App\Models\Sky;
use Illuminate\Support\Facades\Storage;

class SkyService
{
    public function getPaginated(int $perPage = 15, string $search = '')
    {
        return Sky::query()
            ->when($search, fn($q) => $q->where('lokasi', 'like', "%{$search}%")
                                        ->orWhere('bahaya', 'like', "%{$search}%")
                                        ->orWhere('userId', 'like', "%{$search}%"))
            ->orderByDesc('date')
            ->orderByDesc('no')
            ->paginate($perPage);
    }

    public function create(array $data, $image = null)
    {
        if ($image) {
            $data['img'] = $image->store('kyt', 'public');
        }
        return Sky::create($data);
    }

    public function update(int $no, array $data, $image = null)
    {
        $record = Sky::findOrFail($no);
        
        if ($image) {
            if ($record->img) {
                Storage::disk('public')->delete($record->img);
            }
            $data['img'] = $image->store('kyt', 'public');
        }
        
        $record->update($data);
        return $record;
    }

    public function delete(int $no): bool
    {
        $record = Sky::findOrFail($no);
        if ($record->img) {
            Storage::disk('public')->delete($record->img);
        }
        return $record->delete();
    }
}
