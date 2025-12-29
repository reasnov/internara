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
        // Define user-specific permissions that should be assigned
        $userPermissions = [
            'user.view',
            'user.create',
            'user.update',
            'user.delete',
            'user.manage',
        ];

        // Find the core roles
        $ownerRole = Role::findByName('owner', 'web');
        $adminRole = Role::findByName('admin', 'web');

        // Assign all user permissions to owner and admin roles
        if ($ownerRole) {
            $ownerRole->givePermissionTo($userPermissions);
        }

        if ($adminRole) {
            $adminRole->givePermissionTo($userPermissions);
        }
    }
}
