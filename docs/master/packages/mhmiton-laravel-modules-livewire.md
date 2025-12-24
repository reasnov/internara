# mhmiton/laravel-modules-livewire Integration

This package enables Livewire components to be discovered and rendered from within our modular structure.

---

## Usage Patterns

To maintain the modular boundary, always reference Livewire components using the **Module Alias** syntax.

### 1. Embedding in Blade
Use the `@livewire` directive or the `<livewire:` tag with the prefix:

```blade
{{-- Recommended --}}
@livewire('user::profile-manager')

{{-- Alternative --}}
<livewire:user::profile-manager />
```

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
