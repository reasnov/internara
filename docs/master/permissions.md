# Role & Permission Management Guide

This document outlines the conventions and best practices for creating and managing roles and permissions within the Internara application. Adhering to these rules is crucial for maintaining a decoupled and scalable modular architecture.

---

## 1. Philosophy: Decoupled Authorization

Our authorization system is built on a simple but powerful principle: **Policies check for `Permissions`, not `Roles`**.

Even though for the MVP we manage access by assigning roles to users (e.g., 'Admin', 'Teacher'), the underlying code in our `Policy` classes exclusively checks for granular permissions (e.g., `$user->can('user.view')`).

**Why?**
- **Decoupling:** `UserPolicy` does not need to know what an "Admin" is. It only cares if the user has the 'user.view' permission. This keeps the policy clean and reusable.
- **Future-Proof:** This architecture makes it incredibly easy to introduce more granular permissions or new roles in the future without having to refactor any existing policies.
- **Single Responsibility:** The responsibility of linking a `Role` to a set of `Permissions` is handled entirely by our database seeders, not by application logic.

---

## 2. Naming Convention for Permissions

All permissions **must** follow the `module.action` naming convention.

- **Format:** `{module_name}.{action}`
- **Examples:**
  - `user.view`: Permission to view users.
  - `user.create`: Permission to create a user.
  - `post.publish`: Permission to publish a blog post.

### The `.manage` Permission

For convenience, it's recommended to create a "super-admin" permission for each module using the `.manage` suffix. This permission grants all capabilities for that module.

- **Example:** `user.manage`
- **Usage in a Policy:** `return $user->can('user.manage') || $user->can('user.view');`

---

## 3. Location of Definitions

The creation of `Roles` and `Permissions` is strictly separated to maintain module independence.

### Defining Permissions: In the Module Itself

A module is responsible for defining the permissions it requires. This logic **must** be placed in the `database/seeders/PermissionSeeder.php` file within that module.

**Example: `modules/User/database/seeders/PermissionSeeder.php`**
```php
<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
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
            Permission::updateOrCreate(
                ['name' => $name, 'guard_name' => 'web'],
                [
                    'description' => $description,
                    'module' => 'User',
                ]
            );
        }
    }
}
```

### Defining Roles: In the `Core` Module

Application-wide roles that are shared across all modules **must** be defined in `modules/Core/database/seeders/RoleSeeder.php`.

**Example: `modules/Core/database/seeders/RoleSeeder.php`**
```php
<?php

namespace Modules\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'owner' => 'Full system access and ownership',
            'admin' => 'Administrative management',
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
        // ... permission assignment for CORE permissions only
    }
}
```

---

## 4. Assigning Permissions to Roles

This is the most critical convention for ensuring a decoupled system. A module is responsible for assigning its **own** permissions to the core roles.

This logic **must** be placed in the `database/seeders/RoleSeeder.php` file of that specific module. **Do not** assign a module's permissions from within the `Core` module.

### Example: `User` Module Assigning `user.*` Permissions

The `User` module's seeder finds the `admin` and `owner` roles (created by `Core`) and grants them the necessary `user.*` permissions.

**File: `modules/User/database/seeders/RoleSeeder.php`**
```php
<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $userPermissions = [
            'user.view',
            'user.create',
            'user.update',
            'user.delete',
            'user.manage',
        ];

        $ownerRole = Role::findByName('owner', 'web');
        $adminRole = Role::findByName('admin', 'web');

        if ($ownerRole) {
            $ownerRole->givePermissionTo($userPermissions);
        }

        if ($adminRole) {
            $adminRole->givePermissionTo($userPermissions);
        }
    }
}
```
*Note: We use `givePermissionTo` instead of `syncPermissions` to avoid conflicts where one module's seeder might remove permissions assigned by another module's seeder.*

---

## 5. The Seeding Process

To ensure everything works correctly, the module-level `DatabaseSeeder` files must call the `PermissionSeeder` **before** the `RoleSeeder`.

**Example: `modules/Core/database/seeders/CoreDatabaseSeeder.php`**
```php
<?php

namespace Modules\Core\Database\Seeders;

use Illuminate\Database\Seeder;

class CoreDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class, // Runs first
            RoleSeeder::class,       // Runs second
        ]);
    }
}
```
When you run `php artisan db:seed`, Laravel will trigger the main seeder, which in turn should call the `DatabaseSeeder` from each module, executing this logic in the correct order.

---

## 6. Usage in a Policy

The final step is to consume these permissions within a Laravel Policy class. Policies are the gatekeepers for actions on your models. Following our philosophy, a policy **must** check for permissions, not roles.

This connects the entire system:
1.  **A `Permission` is defined** (e.g., in `User/PermissionSeeder`).
2.  **A `Role` is given that `Permission`** (e.g., in `User/RoleSeeder`).
3.  **A `User` is assigned the `Role`**.
4.  **A `Policy` checks if the `User` has the `Permission`**.

### Example: `UserPolicy`

Here is how `modules/User/src/Policies/UserPolicy.php` checks the permissions we defined.

```php
<?php

namespace Modules\User\Policies;

use Modules\User\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Checks for the general 'user.view' or the master 'user.manage' permission.
        return $user->can('user.view') || $user->can('user.manage');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        // A user can always update their own profile.
        // Otherwise, check if they have 'user.update' or the master 'user.manage' permission.
        return $user->id === $model->id || $user->can('user.update') || $user->can('user.manage');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        // First, explicitly prevent the owner account from being deleted.
        if ($model->hasRole('owner')) {
            return false;
        }

        // Then, check if the acting user has the required permission.
        return $user->can('user.delete') || $user->can('user.manage');
    }
}
```

By using `$user->can()`, the policy remains completely decoupled from the roles. You can freely change which roles get which permissions in your seeders without ever needing to touch the policy code.

---

## 7. Owner vs. Admin Responsibilities

To ensure security and a clear separation of duties, the `owner` and `admin` roles have distinct responsibilities.

### Owner
The **Owner** role is a "root" or "super-admin" account. There should only be one owner in the system.

- **Responsibilities:**
  - Manages the application lifecycle and critical configurations.
  - Can manage other `admin` accounts (create, delete, update).
  - Has ultimate authority and can perform irreversible, destructive actions.
  - The `owner` account itself is protected and **cannot be deleted or have its role changed**.

- **Permissions:**
  - The `owner` role automatically receives **all permissions** in the system via a `Gate::before` check (see `User/Providers/AuthServiceProvider.php`). This happens implicitly and does not require manual permission assignment.

### Admin
The **Admin** role is for managing the day-to-day operations and data within the application, under the rules set by the owner.

- **Responsibilities:**
  - Manages day-to-day business data (e.g., users, posts, categories).
  - Operates within the permissions granted to them.
  - Cannot manage other `admin` accounts or change fundamental application settings.

- **Permissions:**
  - Receives a broad set of permissions for management tasks (e.g., `user.manage`), but does not have the implicit "grant all" authority of the `owner`. Permissions must be explicitly assigned via seeders.
