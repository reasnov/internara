# Policy Patterns

Internara follows a standardized pattern for creating and using Laravel Policies to ensure
consistent authorization logic across all modules.

## Principles

1.  **Permission-Based:** Policies should primarily check for **Permissions** (e.g., `user.create`)
    rather than hard-coding **Roles** (e.g., `admin`).
2.  **Module Isolation:** Policies for a specific module's models must reside within that module's
    `src/Policies` directory.
3.  **Naming Convention:** Permissions checked within a Policy must follow the `{module}.{action}`
    format.
4.  **Implicit Manage Check:** Most policies should check for a general `.manage` permission as a
    fallback for full module control.

---

## Technical Pattern

### 1. Permission Naming

- `{module}.view`
- `{module}.create`
- `{module}.update`
- `{module}.delete`
- `{module}.manage` (Super permission for the module)

### 2. Policy Implementation Example

```php
namespace Modules\User\Policies;

use Modules\User\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('user.view') || $user->can('user.manage');
    }

    public function view(User $user, User $model): bool
    {
        // Allow if it's the owner or has permission
        return $user->id === $model->id || $user->can('user.view') || $user->can('user.manage');
    }

    public function create(User $user): bool
    {
        return $user->can('user.create') || $user->can('user.manage');
    }

    public function update(User $user, User $model): bool
    {
        return $user->id === $model->id || $user->can('user.update') || $user->can('user.manage');
    }

    public function delete(User $user, User $model): bool
    {
        return $user->can('user.delete') || $user->can('user.manage');
    }
}
```

---

## Registration

Policies are automatically discovered by Laravel if they follow the standard naming convention
(`Model` -> `ModelPolicy`). However, in a modular context, it is best practice to explicitly
register them in the module's `AuthServiceProvider` or a dedicated `registerPolicies` method in the
main `ServiceProvider`.

---

## Usage in Controllers and Livewire

### Using the `authorize` helper:

```php
$this->authorize('update', $userModel);
```

### Using Blade directives:

```blade
@can('update', $user)
    <button wire:click="edit">Edit</button>
@endcan
```

### Using Service Layer (Recommended)

While UI layers check for authorization to show/hide elements, the **Service Layer** must also
enforce these rules for data integrity.

```php
public function update(mixed $id, array $data): Model
{
    $model = $this->find($id);

    if (Gate::denies('update', $model)) {
        throw new AuthorizationException();
    }

    // ... logic
}
```

---

**Navigation**

[← Previous: Permission Seeders](permission-seeders.md) |
[Next: Permission UI Components →](permission-ui-components.md)
