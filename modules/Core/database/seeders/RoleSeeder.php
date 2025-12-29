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
            'teacher' => 'Supervising and assessing students',
            'student' => 'Internship participants',
        ];

        foreach ($roles as $name => $description) {
            Role::updateOrCreate(
                ['name' => $name, 'guard_name' => 'web'],
                [
                    'description' => $description,
                    'module' => 'Core',
                ]
            );
        }
    }
}