<?php

namespace Modules\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'core.manage' => 'Manage core application settings',
            'core.view-dashboard' => 'View the admin dashboard',
            'user.view' => 'View user list and details',
            'user.create' => 'Create new users',
            'user.update' => 'Update existing users',
            'user.delete' => 'Delete users',
            'user.manage' => 'Full user management access',
        ];

        foreach ($permissions as $name => $description) {
            Permission::updateOrCreate(
                ['name' => $name, 'guard_name' => 'web'],
                [
                    'description' => $description,
                    'module' => 'Core',
                ]
            );
        }
    }
}
