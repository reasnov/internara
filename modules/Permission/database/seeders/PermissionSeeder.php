<?php

declare(strict_types=1);

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
        $permissions = [
            // Core
            'core.manage' => ['Manage core settings', 'Core'],
            'core.view-dashboard' => ['View dashboard', 'Core'],

            // User
            'user.view' => ['View users', 'User'],
            'user.create' => ['Create users', 'User'],
            'user.update' => ['Update users', 'User'],
            'user.delete' => ['Delete users', 'User'],
            'user.manage' => ['Full user management', 'User'],

            // School
            'school.view' => ['View school profile', 'School'],
            'school.update' => ['Update school profile', 'School'],
            'school.manage' => ['Full school management', 'School'],

            // Department
            'department.view' => ['View departments', 'Department'],
            'department.create' => ['Create departments', 'Department'],
            'department.update' => ['Update departments', 'Department'],
            'department.delete' => ['Delete departments', 'Department'],

            // Internship
            'internship.view' => ['View internships', 'Internship'],
            'internship.create' => ['Create internships', 'Internship'],
            'internship.update' => ['Update internships', 'Internship'],
            'internship.approve' => ['Approve internships', 'Internship'],
        ];

        foreach ($permissions as $name => $data) {
            Permission::updateOrCreate(
                ['name' => $name, 'guard_name' => 'web'],
                [
                    'description' => $data[0],
                    'module'      => $data[1],
                ]
            );
        }
    }
}