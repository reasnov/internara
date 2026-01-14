<?php

namespace Modules\Permission\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $corePermissions = [
            'core.manage' => 'Manage core application settings',
            'core.view-dashboard' => 'View the admin dashboard',
            // Add other business-specific permissions here
        ];

        $userPermissions = [
            'user.view' => 'View user list and details',
            'user.create' => 'Create new users',
            'user.update' => 'Update existing users',
            'user.delete' => 'Delete users',
            'user.manage' => 'Full user management access',
        ];

        $allPermissions = array_merge($corePermissions, $userPermissions);

        foreach ($allPermissions as $name => $description) {
            $module = explode('.', $name)[0]; // Extract module name from permission name

            Permission::updateOrCreate(
                ['name' => $name, 'guard_name' => 'web'],
                [
                    'description' => $description,
                    'module' => ucfirst($module),
                ]
            );
        }
    }
}
