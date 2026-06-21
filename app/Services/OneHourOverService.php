<?php

namespace App\Services;

use App\Models\OneHourOver;
use Illuminate\Support\Facades\Storage;

class OneHourOverService
{
    public function getPaginated(int $perPage = 15, string $search = '')
    {
        return OneHourOver::query()
            ->when($search, fn($q) => $q->where(function ($q) use ($search) {
                $q->where('line', 'like', "%{$search}%")
                  ->orWhere('machine', 'like', "%{$search}%")
                  ->orWhere('problem', 'like', "%{$search}%");
            }))
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function create(array $data, $fileRsa = null, $fileRca = null)
    {
        if ($fileRsa) {
            $data['file_rsa'] = $fileRsa->store('one_hour_over', 'public');
        }
        if ($fileRca) {
            $data['file_rca'] = $fileRca->store('one_hour_over', 'public');
        }
        return OneHourOver::create($data);
    }

    public function update(int $id, array $data, $fileRsa = null, $fileRca = null)
    {
        $record = OneHourOver::findOrFail($id);
        if ($fileRsa) {
            if ($record->file_rsa) Storage::disk('public')->delete($record->file_rsa);
            $data['file_rsa'] = $fileRsa->store('one_hour_over', 'public');
        }
        if ($fileRca) {
            if ($record->file_rca) Storage::disk('public')->delete($record->file_rca);
            $data['file_rca'] = $fileRca->store('one_hour_over', 'public');
        }
        $record->update($data);
        return $record;
    }

    public function delete(int $id): bool
    {
        $record = OneHourOver::findOrFail($id);
        if ($record->file_rsa) Storage::disk('public')->delete($record->file_rsa);
        if ($record->file_rca) Storage::disk('public')->delete($record->file_rca);
        return $record->delete();
    }
}
