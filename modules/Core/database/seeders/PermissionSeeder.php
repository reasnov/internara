<?php

namespace Modules\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Permission\Contracts\PermissionManager;

class PermissionSeeder extends Seeder
{
    /**
     * Create a new seeder instance.
     */
    public function __construct(protected PermissionManager $permissionManager) {}

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'core.manage' => 'Manage core application settings',
            'core.view-dashboard' => 'View the admin dashboard',
            // Add other business-specific permissions here
        ];

        foreach ($permissions as $name => $description) {
            $this->permissionManager->createPermission($name, $description, 'Core');
        }
    }
}
