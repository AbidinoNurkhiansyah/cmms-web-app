<?php

namespace App\Repositories\Contracts;

interface UserRepositoryInterface
{
    public function getAllPaginated(int $perPage = 25, string $search = '');
    public function findById(int $id);
    public function findByUsername(string $username);
    public function create(array $data);
    public function update(int $id, array $data);
    public function updateStatus(int $id, string $status);
}
