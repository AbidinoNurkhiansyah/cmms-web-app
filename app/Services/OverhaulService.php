<?php

namespace App\Services;

use App\Models\Overhaul;
use Illuminate\Support\Facades\Storage;

class OverhaulService
{
    public function getPaginated(int $perPage = 15, string $search = '')
    {
        return Overhaul::with(['steps', 'spareparts'])
            ->when($search, fn($q) => $q->where(function ($q) use ($search) {
                $q->where('LineName', 'like', "%{$search}%")
                  ->orWhere('MachineName', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('problem', 'like', "%{$search}%");
            }))
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function create(array $data, array $relations = [], array $photos = [])
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($data, $relations, $photos) {
            foreach (['photo_before_1', 'photo_after_1', 'photo_before_2', 'photo_after_2'] as $photoKey) {
                if (isset($photos[$photoKey]) && $photos[$photoKey]) {
                    $data[$photoKey] = $photos[$photoKey]->store('overhaul', 'public');
                }
            }

            $overhaul = Overhaul::create($data);

            if (!empty($relations['steps'])) {
                $overhaul->steps()->createMany($relations['steps']);
            }
            if (!empty($relations['spareparts'])) {
                $overhaul->spareparts()->createMany($relations['spareparts']);
            }

            return $overhaul;
        });
    }

    public function update(int $id, array $data, array $relations = [], array $photos = [])
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($id, $data, $relations, $photos) {
            $record = Overhaul::findOrFail($id);
            
            foreach (['photo_before_1', 'photo_after_1', 'photo_before_2', 'photo_after_2'] as $photoKey) {
                if (isset($photos[$photoKey]) && $photos[$photoKey]) {
                    if ($record->$photoKey) Storage::disk('public')->delete($record->$photoKey);
                    $data[$photoKey] = $photos[$photoKey]->store('overhaul', 'public');
                }
            }
            
            $record->update($data);

            if (isset($relations['steps'])) {
                $record->steps()->delete();
                if (!empty($relations['steps'])) {
                    $record->steps()->createMany($relations['steps']);
                }
            }

            if (isset($relations['spareparts'])) {
                $record->spareparts()->delete();
                if (!empty($relations['spareparts'])) {
                    $record->spareparts()->createMany($relations['spareparts']);
                }
            }

            return $record;
        });
    }

    public function delete(int $id): bool
    {
        $record = Overhaul::findOrFail($id);
        foreach (['photo_before_1', 'photo_after_1', 'photo_before_2', 'photo_after_2'] as $photoKey) {
            if ($record->$photoKey) Storage::disk('public')->delete($record->$photoKey);
        }
        return $record->delete();
    }
}
