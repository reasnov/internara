<?php

namespace Modules\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Permission\Contracts\PermissionManager;

class RoleSeeder extends Seeder
{
    public function __construct(protected PermissionManager $permissionManager) {}

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
            $this->permissionManager->createRole($name, $description, 'Core');
        }
    }
}
