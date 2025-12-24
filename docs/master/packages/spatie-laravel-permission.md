# spatie/laravel-permission Integration

We have integrated Spatie's permission package into a dedicated `Permission` module. This implementation is highly customized for security, flexibility, and portability.

---

## Architectural Enhancements

### 1. Configurable ID Types
The module supports both **UUID** and **Integer** IDs, controllable via `modules/Permission/config/config.php`.
- **Default:** `UUID` (to match our `User` model).
- **Behavior:** The migration and models (`Role`, `Permission`) automatically adapt their primary and foreign keys based on the `model_key_type` setting.

### 2. Module Ownership
We added a nullable `module` column to the `roles` and `permissions` tables.
- **Purpose:** Identifies which module "owns" or created a specific permission.
- **Usage:** Filters permissions in the UI by business domain.

### 3. Runtime Configuration (Portability)
To ensure the `Permission` module is plug-and-play, it overrides Spatie's root configuration at runtime via `PermissionServiceProvider`:

```php
protected function overrideSpatieConfig(): void
{
    config([
        'permission.models.role' => \Modules\Permission\Models\Role::class,
        'permission.models.permission' => \Modules\Permission\Models\Permission::class,
    ]);
}
```

---

## Usage

### Service Layer
Do not use Spatie's traits directly for complex management. Use the provided services:
- `Modules\Permission\Contracts\Services\RoleService`
- `Modules\Permission\Contracts\Services\PermissionService`

### Assignment
Assign roles to users as usual, but remember that the `User` model uses UUIDs:

```php
$user->assignRole('admin');
```

---

## Testing
Always use the provided factories for testing:
```php
$role = \Modules\Permission\Models\Role::factory()->create(['module' => 'User']);
```
