<?php

namespace Modules\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            'owner' => 'Full system access and ownership',
            'admin' => 'Administrative management',
            'teacher' => 'Supervising and assessing students',
            'student' => 'Internship participants',
        ];

        foreach ($roles as $name => $description) {
            $role = Role::updateOrCreate(
                ['name' => $name, 'guard_name' => 'web'],
                [
                    'description' => $description,
                    'module' => 'Core',
                ]
            );

            if ($name === 'owner' || $name === 'admin') {
                // Assign only Core permissions
                $role->givePermissionTo([
                    'core.manage',
                    'core.view-dashboard',
                ]);
            }
        }
    }
}