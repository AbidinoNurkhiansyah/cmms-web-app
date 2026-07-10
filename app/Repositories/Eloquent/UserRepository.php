<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    public function getAllPaginated(int $perPage = 25, string $search = '')
    {
        if (!empty($search)) {
            return User::search($search)
                ->query(fn ($query) => $query->select('id', 'jid_no', 'name', 'username', 'position', 'team', 'jobdesc', 'photo', 'status', 'is_admin'))
                ->orderBy('jid_no', 'asc')
                ->paginate($perPage);
        }

        return User::query()
            ->select('id', 'jid_no', 'name', 'username', 'position', 'team', 'jobdesc', 'photo', 'status', 'is_admin')
            ->orderBy('jid_no')
            ->paginate($perPage);
    }

    public function findById(int $id)
    {
        return User::findOrFail($id);
    }

    public function findByUsername(string $username)
    {
        return User::where('username', $username)->first();
    }

    public function create(array $data)
    {
        return User::create($data);
    }

    public function update(int $id, array $data)
    {
        $user = User::findOrFail($id);
        $user->update($data);
        return $user;
    }

    public function updateStatus(int $id, string $status)
    {
        $user = User::findOrFail($id);
        $user->update(['status' => $status]);
        return $user;
    }

    public function delete(int $id)
    {
        $user = User::findOrFail($id);
        return $user->delete();
    }
}
