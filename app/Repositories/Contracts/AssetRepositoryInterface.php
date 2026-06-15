<?php

namespace App\Repositories\Contracts;

interface AssetRepositoryInterface
{
    public function getAllPaginated(int $perPage = 10, string $search = '');
    public function findById(int $id);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
}
