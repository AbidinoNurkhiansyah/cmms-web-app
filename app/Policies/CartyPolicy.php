<?php

namespace App\Policies;

use App\Models\Carty;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CartyPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('wr.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Carty $carty): bool
    {
        return $user->hasPermissionTo('wr.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('wr.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Carty $carty): bool
    {
        return $user->hasPermissionTo('wr.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Carty $carty): bool
    {
        return $user->hasPermissionTo('wr.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Carty $carty): bool
    {
        return $user->hasPermissionTo('wr.delete');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Carty $carty): bool
    {
        return $user->hasPermissionTo('wr.delete');
    }
}
