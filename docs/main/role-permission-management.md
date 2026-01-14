# Role and Permission Management

This document outlines the conventions and best practices for creating and managing roles and permissions within the Internara application. Adhering to these rules is crucial for maintaining a decoupled and scalable modular architecture.

---

## 1. Philosophy: Decoupled Authorization

Our authorization system is built on a simple but powerful principle: **Policies check for `Permissions`, not `Roles`**.

Even though for the MVP we manage access by assigning roles to users (e.g., 'Admin', 'Teacher'), the underlying code in our `Policy` classes exclusively checks for granular permissions (e.g., `$user->can('user.view')`).

**Why?**

-   **Decoupling:** `UserPolicy` does not need to know what an "Admin" is. It only cares if the user has the 'user.view' permission. This keeps the policy clean and reusable.
-   **Future-Proof:** This architecture makes it incredibly easy to introduce more granular permissions or new roles in the future without having to refactor any existing policies.
-   **Single Responsibility:** The responsibility of linking a `Role` to a set of `Permissions` is handled entirely by our database seeders, not by application logic.

---

## 2. Naming Convention for Permissions

All permissions **must** follow the `module.action` naming convention.

-   **Format:** `{module_name}.{action}`
-   **Examples:**
    -   `user.view`: Permission to view users.
    -   `user.create`: Permission to create a user.
    -   `post.publish`: Permission to publish a blog post.

### The `.manage` Permission

For convenience, it's recommended to create a "super-admin" permission for each module using the `.manage` suffix. This permission grants all capabilities for that module.

-   **Example:** `user.manage`
-   **Usage in a Policy:** `return $user->can('user.manage') || $user->can('user.view');`

---

## 3. Location of Definitions

The creation of `Roles` and `Permissions` is strictly separated to maintain module independence.

### Defining Permissions: In the `Permission` Module

All permissions across the application **must** be defined in the central `modules/Permission/database/seeders/PermissionSeeder.php` file. This ensures a single source of truth for all available permissions.

### Defining Roles: In the `Permission` Module

All application-wide roles that are shared across all modules **must** be defined in the central `modules/Permission/database/seeders/RoleSeeder.php` file. This centralizes role definitions.

---

## 4. Assigning Permissions to Roles

The logic for assigning permissions to roles is now centralized within the `modules/Permission/database/seeders/RoleSeeder.php` file. This seeder is responsible for defining all roles and then assigning the necessary permissions to them, drawing from the permissions defined in `modules/Permission/database/seeders/PermissionSeeder.php`.

---

## 5. The Seeding Process

To ensure everything works correctly, the `PermissionDatabaseSeeder` in `modules/Permission/database/seeders/` is now responsible for calling both `PermissionSeeder` and `RoleSeeder` in the correct order. The main `DatabaseSeeder` will then call `PermissionDatabaseSeeder` as part of its dynamic module seeding.

---

## 6. Usage in a Policy

The final step is to consume these permissions within a Laravel Policy class. Policies are the gatekeepers for actions on your models. Following our philosophy, a policy **must** check for permissions, not roles.

This connects the entire system:

1.  **A `Permission` is defined** (in `Permission/PermissionSeeder`).
2.  **A `Role` is given that `Permission`** (in `Permission/RoleSeeder`).
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
        // First, explicitly prevent the SuperAdmin account from being deleted.
        if ($model->hasRole('super-admin')) {
            return false;
        }

        // Then, check if the acting user has the required permission.
        return $user->can('user.delete') || $user->can('user.manage');
    }
}
```

By using `$user->can()`, the policy remains completely decoupled from the roles. You can freely change which roles get which permissions in your seeders without ever needing to touch the policy code.

---

## 7. SuperAdmin vs. Admin Responsibilities

To ensure security and a clear separation of duties, the SuperAdmin and admin roles have distinct responsibilities.

### SuperAdmin

The **SuperAdmin** role is a "root" or "super-admin" account. There should only be one SuperAdmin in the system.

-   **Responsibilities:**

    -   Manages the application lifecycle and critical configurations.
    -   Can manage other `admin` accounts (create, delete, update).
    -   Has ultimate authority and can perform irreversible, destructive actions.
      - The `super-admin` account itself is protected and **cannot be deleted or have its role changed**.

-   **Permissions:**
      - The `super-admin` role automatically receives **all permissions** in the system via a `Gate::before` check (see `User/Providers/AuthServiceProvider.php`). This happens implicitly and does not require manual permission assignment.

### Admin

The **Admin** role is for managing the day-to-day operations and data within the application, under the rules set by the SuperAdmin.

-   **Responsibilities:**

    -   Manages day-to-day business data (e.g., users, posts, categories).
    -   Operates within the permissions granted to them.
    -   Cannot manage other `admin` accounts or change fundamental application settings.

-   **Permissions:**
        - Receives a broad set of permissions for management tasks (e.g., `user.manage`), but does not have the implicit "grant all" authority of the SuperAdmin. Permissions must be explicitly assigned via seeders.
