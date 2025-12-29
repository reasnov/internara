<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Permission\Models\Role;

class RoleSeeder extends Seeder
{
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
            $role = Role::updateOrCreate(
                ['name' => $name, 'guard_name' => 'web'],
                [
                    'description' => $description,
                    'module' => 'User', // Ownership is now with the User module
                ]
            );

            // Assign all relevant permissions to the role
            $role->givePermissionTo($adminPermissions);
        }
    }
}
