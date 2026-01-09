# mhmiton/laravel-modules-livewire Integration

This package enables Livewire components to be discovered and rendered from within our modular structure.

---

## Usage Patterns

To maintain the modular boundary, always reference Livewire components using the **Module Alias** syntax.

### 1. Embedding in Blade
Use the `@livewire` directive with the module prefix. This is the standard and required method for embedding interactive components to ensure consistency and clarity.

```blade
{{-- CORRECT --}}
@livewire('user::profile-manager')

{{-- DEPRECATED for interactive components --}}
<livewire:user::profile-manager />
```
The tag-based syntax should be avoided for interactive components as per the project's primary [Architecture Guide](../architecture.md).

### 2. Component Discovery
Components must be located in `modules/{Module}/src/Livewire/`.
- **Class:** `Modules\User\Livewire\ProfileManager`
- **Path:** `modules/User/src/Livewire/ProfileManager.php`

### 3. Event Dispatching
When dispatching events between modules via Livewire, ensure the event names are descriptive to avoid collisions:

```php
$this->dispatch('user::profile-updated', userId: $this->user->id);
```

---

## Configuration
The bridge is configured in `config/modules-livewire.php`. It automatically scans the `src/Livewire` directory of every enabled module.
