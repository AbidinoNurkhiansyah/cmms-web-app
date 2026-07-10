<?php

namespace App\Services;

use App\Repositories\Contracts\JobDescriptionRepositoryInterface;
use Illuminate\Support\Facades\Log;
use Exception;

class JobDescriptionService
{
    protected $jobDescriptionRepository;

    public function __construct(JobDescriptionRepositoryInterface $jobDescriptionRepository)
    {
        $this->jobDescriptionRepository = $jobDescriptionRepository;
    }

    public function getPaginatedJobDescriptions($perPage = 10, $search = '')
    {
        return $this->jobDescriptionRepository->getAllPaginated($perPage, $search);
    }

    public function getUniqueUnits()
    {
        return $this->jobDescriptionRepository->getAllUniqueUnits();
    }

    public function createJobDescription(array $data)
    {
        try {
            return $this->jobDescriptionRepository->create($data);
        } catch (Exception $e) {
            Log::error('Error creating JobDescription: ' . $e->getMessage());
            throw $e;
        }
    }

    public function updateJobDescription($id, array $data)
    {
        try {
            return $this->jobDescriptionRepository->update($id, $data);
        } catch (Exception $e) {
            Log::error('Error updating JobDescription: ' . $e->getMessage());
            throw $e;
        }
    }

    public function deleteJobDescription($id)
    {
        try {
            return $this->jobDescriptionRepository->delete($id);
        } catch (Exception $e) {
            Log::error('Error deleting JobDescription: ' . $e->getMessage());
            throw $e;
        }
    }
}
