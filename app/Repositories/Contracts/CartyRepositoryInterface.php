<?php

namespace App\Repositories\Contracts;

interface CartyRepositoryInterface
{
    public function getAllPaginated(int $perPage = 15, string $search = '', string $status = '', string $startDate = '', string $endDate = '');
    public function findById(int $id);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id): bool;
    public function getDistinctLines(): array;
}
