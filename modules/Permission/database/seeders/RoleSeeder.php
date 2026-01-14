<?php

namespace Modules\Permission\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $coreRoles = [
            'Teacher' => 'Supervising and assessing students',
            'Student' => 'Internship participants',
        ];

        $userRoles = [
            'SuperAdmin' => 'Full system access and ownership',
            'Admin' => 'Administrative management',
        ];

        $allRoles = array_merge($coreRoles, $userRoles);

        // Define all permissions for admin-level roles
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

        foreach ($allRoles as $name => $description) {
            $module = in_array($name, array_keys($coreRoles)) ? 'Core' : 'User'; // Determine original module

            /** @var Role $role */
            $role = Role::updateOrCreate(
                ['name' => $name, 'guard_name' => 'web'],
                [
                    'description' => $description,
                    'module' => $module,
                ]
            );

            // Assign permissions if it's an admin-level role
            if (in_array($name, ['SuperAdmin', 'Admin'])) {
                $role->givePermissionTo($adminPermissions);
            }
        }
    }
}
