<?php

namespace App\Repositories\Eloquent;

use App\Models\JobDescription;
use App\Repositories\Contracts\JobDescriptionRepositoryInterface;

class JobDescriptionRepository implements JobDescriptionRepositoryInterface
{
    public function getAllPaginated($perPage = 10, $search = '')
    {
        return JobDescription::when($search, function ($query) use ($search) {
            $query->where('team', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%')
                  ->orWhereJsonContains('units', $search);
        })->latest()->paginate($perPage);
    }

    public function create(array $data)
    {
        return JobDescription::create($data);
    }

    public function findById($id)
    {
        return JobDescription::findOrFail($id);
    }

    public function update($id, array $data)
    {
        $jobdesc = $this->findById($id);
        $jobdesc->update($data);
        return $jobdesc;
    }

    public function delete($id)
    {
        $jobdesc = $this->findById($id);
        return $jobdesc->delete();
    }

    public function getAllUniqueUnits()
    {
        // Pluck all units arrays, flatten them, and get unique values
        $unitsArrays = JobDescription::pluck('units')->filter();
        $allUnits = [];
        foreach ($unitsArrays as $units) {
            if (is_array($units)) {
                $allUnits = array_merge($allUnits, $units);
            }
        }
        return array_values(array_unique($allUnits));
    }
}
