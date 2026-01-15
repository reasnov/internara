# Permission Seeders

The `Permission` module provides foundational seeders to establish the initial security structure of Internara.

## Core Seeders

### 1. `Modules\Permission\Database\Seeders\PermissionSeeder`
Registers granular permissions for all core modules. Permissions follow the `{module}.{action}` pattern.

**Included Permissions:**
-   **Core:** `core.manage`, `core.view-dashboard`
-   **User:** `user.view`, `user.create`, `user.update`, `user.delete`, `user.manage`
-   **School:** `school.view`, `school.update`, `school.manage`
-   **Department:** `department.view`, `department.create`, `department.update`, `department.delete`
-   **Internship:** `internship.view`, `internship.create`, `internship.update`, `internship.approve`

---

### 2. `Modules\Permission\Database\Seeders\RoleSeeder`
Defines standard system roles and assigns the appropriate permissions to them.

**Available Roles:**
-   **`super-admin`**: Has all permissions (via global bypass and explicit seeding).
-   **`admin`**: General administrative management. Can manage users, school profile, and departments.
-   **`teacher`**: Responsible for supervising and approving internships.
-   **`student`**: Can view and apply for internships.

---

## Usage

To reset and seed the permissions system, run:
```bash
php artisan db:seed --class="Modules\Permission\Database\Seeders\PermissionDatabaseSeeder"
```

## Best Practices for Domain Modules

When creating a new domain module, you should:
1.  **Add Permissions:** Update `PermissionSeeder` with your module's specific actions.
2.  **Update Roles:** Assign those permissions to existing roles in `RoleSeeder` if applicable.
3.  **Use Policies:** Enforce these permissions using Laravel Policies in your module.
