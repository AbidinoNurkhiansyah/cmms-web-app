<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Work Request (WR) / Carty
        Gate::define('wr.create', fn (User $user) => $user->hasAnyRole([User::ROLE_OPERATOR, User::ROLE_PLANNER, User::ROLE_SUPERVISOR]));
        Gate::define('wr.view', fn (User $user) => true); // All can read WR
        Gate::define('wr.update', fn (User $user) => $user->hasAnyRole([User::ROLE_TECHNICIAN, User::ROLE_PLANNER, User::ROLE_SUPERVISOR]));
        Gate::define('wr.delete', fn (User $user) => $user->hasRole(User::ROLE_SUPERVISOR));

        // Work Order (WO)
        Gate::define('wo.view', fn (User $user) => true);
        Gate::define('wo.execute', fn (User $user) => $user->hasRole(User::ROLE_TECHNICIAN));
        Gate::define('wo.plan', fn (User $user) => $user->hasRole(User::ROLE_PLANNER));
        Gate::define('wo.disposition', fn (User $user) => $user->hasRole(User::ROLE_SUPERVISOR));
        Gate::define('wo.approve_cost', fn (User $user) => $user->hasRole(User::ROLE_MANAGER));

        // Asset Management
        Gate::define('asset.view', fn (User $user) => true);
        Gate::define('asset.manage', fn (User $user) => $user->hasRole(User::ROLE_PLANNER));
        Gate::define('asset.modify', fn (User $user) => $user->hasRole(User::ROLE_SUPERVISOR));

        // PM Scheduling
        Gate::define('pm.view', fn (User $user) => $user->role !== User::ROLE_OPERATOR);
        Gate::define('pm.manage', fn (User $user) => $user->hasRole(User::ROLE_PLANNER));
        Gate::define('pm.approve', fn (User $user) => $user->hasRole(User::ROLE_SUPERVISOR));

        // Spare Part / Inventory
        Gate::define('sparepart.view', fn (User $user) => $user->role !== User::ROLE_OPERATOR);
        Gate::define('sparepart.request', fn (User $user) => $user->hasRole(User::ROLE_TECHNICIAN));
        Gate::define('sparepart.manage', fn (User $user) => $user->hasRole(User::ROLE_PLANNER));
        Gate::define('sparepart.approve_part', fn (User $user) => $user->hasRole(User::ROLE_SUPERVISOR));
        Gate::define('sparepart.approve_order', fn (User $user) => $user->hasRole(User::ROLE_MANAGER));

        // KPI & Dashboard
        Gate::define('kpi.technical', fn (User $user) => $user->hasRole(User::ROLE_PLANNER));
        Gate::define('kpi.team', fn (User $user) => $user->hasRole(User::ROLE_SUPERVISOR));
        Gate::define('kpi.all', fn (User $user) => $user->hasRole(User::ROLE_MANAGER));
    }
}
