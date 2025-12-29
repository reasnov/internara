<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Permission\Contracts\PermissionManager;

class PermissionSeeder extends Seeder
{
    public function __construct(protected PermissionManager $permissionManager)
    {
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'user.view' => 'View user list and details',
            'user.create' => 'Create new users',
            'user.update' => 'Update existing users',
            'user.delete' => 'Delete users',
            'user.manage' => 'Full user management access',
        ];

        foreach ($permissions as $name => $description) {
            $this->permissionManager->createPermission($name, $description, 'User');
        }
    }
}
