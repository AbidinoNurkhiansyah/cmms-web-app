<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Auth\Access\Response;

class WorkOrderPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('wo.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, WorkOrder $workOrder): bool
    {
        return $user->hasPermissionTo('wo.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('wo.plan');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, WorkOrder $workOrder): bool
    {
        return $user->hasPermissionTo('wo.plan') || $user->hasPermissionTo('wo.execute');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, WorkOrder $workOrder): bool
    {
        return $user->hasPermissionTo('wo.plan');
    }

    public function execute(User $user, WorkOrder $workOrder): bool
    {
        return $user->hasPermissionTo('wo.execute');
    }

    public function plan(User $user, WorkOrder $workOrder): bool
    {
        return $user->hasPermissionTo('wo.plan');
    }

    public function disposition(User $user, WorkOrder $workOrder): bool
    {
        return $user->hasPermissionTo('wo.disposition');
    }

    public function approveCost(User $user, WorkOrder $workOrder): bool
    {
        return $user->hasPermissionTo('wo.approve_cost');
    }
}
