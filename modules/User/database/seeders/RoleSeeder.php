<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Permission\Contracts\PermissionManager;

class RoleSeeder extends Seeder
{
    public function __construct(protected PermissionManager $permissionManager)
    {
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define admin roles and their descriptions
        $roles = [
            'owner' => 'Full system access and ownership',
            'admin' => 'Administrative management',
        ];

        // Define all permissions for these admin-level roles
        $adminPermissions = [
            // Core permissions
            'core.manage',
            'core.view-dashboard',
            // User permissions
            'user.view',
            'user.create',
            'user.update',
            'user.delete',
            'user.manage',
        ];

        foreach ($roles as $name => $description) {
            // Create the role and assign it to the 'User' module
            $role = $this->permissionManager->createRole($name, $description, 'User');

            // Assign all relevant permissions to the role
            $this->permissionManager->givePermissionToRole($role->name, $adminPermissions);
        }
    }
}

