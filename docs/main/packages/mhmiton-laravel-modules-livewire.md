# Laravel Modules Livewire Integration

This document outlines the specific integration and usage patterns for the `mhmiton/laravel-modules-livewire` package within the Internara project. This essential package enables the seamless discovery and rendering of Livewire components directly from within Internara's modular structure, allowing for modular UI development. For a general understanding of Livewire integration and modular architecture principles, refer to the **[Livewire Integration Guide](../packages/laravel-livewire.md)** and the **[Architecture Guide](../architecture-guide.md)**.

---

## Usage Patterns Specific to `mhmiton/laravel-modules-livewire`

To maintain the modular boundary, always reference Livewire components using the **Module Alias** syntax as enabled by this package.

### 1. Embedding in Blade
Use the `@livewire` directive with the module prefix. This is the standard and required method for embedding interactive components.

```blade
{{-- CORRECT --}}
@livewire('user::profile-manager')

{{-- DEPRECATED for interactive components --}}
<livewire:user::profile-manager />
```
The tag-based syntax should be avoided for interactive components as per project conventions.

### 2. Component Discovery
Components enabled by this package must be located in `modules/{Module}/src/Livewire/`.
- **Class:** `Modules\User\Livewire\ProfileManager`
- **Path:** `modules/User/src/Livewire/ProfileManager.php`

### 3. Event Dispatching
When dispatching events between modules via Livewire using this package, ensure the event names are descriptive to avoid collisions:

```php
$this->dispatch('user::profile-updated', userId: $this->user->id);
```

---

## Configuration
This package is configured in `config/modules-livewire.php`. It automatically scans the `src/Livewire` directory of every enabled module for component discovery.

---

**Navigation**

[← Previous: Laravel Modules Integration](nwidart-laravel-modules.md) | [Next: Spatie Laravel Permission Integration →](spatie-laravel-permission.md)