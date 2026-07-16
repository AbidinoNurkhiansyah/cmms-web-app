<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Create Permissions
        $permissions = [
            // Work Request
            'wr.create', 'wr.view', 'wr.update', 'wr.delete',
            // Work Order
            'wo.view', 'wo.execute', 'wo.plan', 'wo.disposition', 'wo.approve_cost',
            // Asset
            'asset.view', 'asset.manage', 'asset.modify',
            // PM
            'pm.view', 'pm.manage', 'pm.approve',
            // Sparepart
            'sparepart.view', 'sparepart.request', 'sparepart.manage', 'sparepart.approve_part', 'sparepart.approve_order',
            // KPI
            'kpi.technical', 'kpi.team', 'kpi.all',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        // 2. Create Roles and Assign Permissions
        // (Role Operator dihapus karena akses via public QR scan)

        $roleTechnician = Role::findOrCreate(User::ROLE_TECHNICIAN);
        $roleTechnician->syncPermissions([
            'wr.update', 'wr.view', 'wo.view', 'wo.execute', 
            'asset.view', 'pm.view', 'sparepart.view', 'sparepart.request'
        ]);

        $rolePlanner = Role::findOrCreate(User::ROLE_PLANNER);
        $rolePlanner->syncPermissions([
            'wr.create', 'wr.view', 'wr.update', 
            'wo.view', 'wo.plan', 
            'asset.view', 'asset.manage', 
            'pm.view', 'pm.manage', 
            'sparepart.view', 'sparepart.manage', 
            'kpi.technical'
        ]);

        $roleSupervisor = Role::findOrCreate(User::ROLE_SUPERVISOR);
        $roleSupervisor->syncPermissions([
            'wr.create', 'wr.view', 'wr.update', 'wr.delete',
            'wo.view', 'wo.disposition', 
            'asset.view', 'asset.modify', 
            'pm.view', 'pm.approve', 
            'sparepart.view', 'sparepart.approve_part', 
            'kpi.team'
        ]);

        $roleManager = Role::findOrCreate(User::ROLE_MANAGER);
        $roleManager->syncPermissions([
            'wr.view', 'wo.view', 'wo.approve_cost', 
            'asset.view', 'pm.view', 
            'sparepart.view', 'sparepart.approve_order', 
            'kpi.all'
        ]);

        // 3. Migrate Existing Users
        // Fetch users who have a role but haven't been assigned the spatie role
        $users = User::whereNotNull('role')->get();
        foreach ($users as $user) {
            if (Role::where('name', $user->role)->exists()) {
                $user->assignRole($user->role);
            }
        }
    }
}
