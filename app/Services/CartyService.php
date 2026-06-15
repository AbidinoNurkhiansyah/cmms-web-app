<?php

namespace App\Services;

use App\Repositories\Contracts\CartyRepositoryInterface;

class CartyService
{
    public function __construct(private CartyRepositoryInterface $repo) {}

    public function getPaginated(int $perPage = 15, string $search = '', string $status = '')
    {
        return $this->repo->getAllPaginated($perPage, $search, $status);
    }

    public function getById(int $id)
    {
        return $this->repo->findById($id);
    }

    public function create(array $data)
    {
        $data['Status'] = $data['Status'] ?? 'Open';
        return $this->repo->create($data);
    }

    public function update(int $id, array $data)
    {
        return $this->repo->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->repo->delete($id);
    }

    public function getLines(): array
    {
        return $this->repo->getDistinctLines();
    }
}
