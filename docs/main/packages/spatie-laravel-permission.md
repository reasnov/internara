# Spatie Laravel Permission Integration

This document details the specific technical customizations made to the `spatie/laravel-permission`
package within Internara's dedicated `Permission` module. For a complete guide on how to create,
manage, and use roles and permissions according to project conventions, please refer to the
**[Role & Permission Management Guide](../role-permission-management.md)**.

---

## Architectural Enhancements for `spatie/laravel-permission`

Our integration of this package is highly customized for portability and to fit Internara's modular
architecture.

### 1. Configurable ID Types

The `Permission` module supports both **UUID** and **Integer** primary keys for its models. This is
controlled via the `model_key_type` setting in `modules/Permission/config/config.php` and is
designed to match the primary key type of the `User` model, aligning with the project's
**[Development Conventions Guide](../development-conventions.md#uuid-usage-convention)**.

### 2. Module Ownership

We have added a nullable `module` column to the `roles` and `permissions` tables. This allows us to
identify which module "owns" a specific role or permission, which is useful for filtering and
organization.

### 3. Runtime Configuration

To ensure the `Permission` module is portable ("plug-and-play"), it does not require manual changes
to the global `config/permission.php` file. Instead, it overrides the necessary configurations at
runtime via its own `PermissionServiceProvider`:

```php
protected function overrideSpatieConfig(): void
{
    config([
        'permission.models.role' => \Modules\Permission\Models\Role::class,
        'permission.models.permission' => \Modules\Permission\Models\Permission::class,
    ]);
}
```

This ensures our custom `Role` and `Permission` models are always used.

---

**Navigation**

[← Previous: Laravel Modules Livewire Integration](mhmiton-laravel-modules-livewire.md) |
[Next: Spatie Activity Log Integration →](spatie-laravel-activitylog.md)
