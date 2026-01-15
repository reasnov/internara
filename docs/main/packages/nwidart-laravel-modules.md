# Laravel Modules Integration

This document details the integration and customization of the `nwidart/laravel-modules` package,
which serves as the foundation of Internara's **Modular Monolith** architecture. It is responsible
for managing the lifecycle, structure, and auto-discovery of all modules within the application. For
a comprehensive overview of the modular architecture, refer to the
**[Architecture Guide](../architecture-guide.md)**.

---

## Custom Configuration for `nwidart/laravel-modules`

We have modified the default behavior of this package in `config/modules.php` to align with
Internara's architectural standards and conventions.

### 1. Source Directory (`src/`)

Our domain logic resides in `src/` instead of the default `app/` folder.

- **Config:** `'paths.app_folder' => 'src/'`
- **Example:** `modules/User/src/Models/User.php`

### 2. Namespace Convention

The `src` segment is intentionally omitted from the module namespaces, as detailed in the
**[Development Conventions Guide](../development-conventions.md)**.

- **Convention:** `Modules\User\Models\User`
- **Path:** `modules/User/src/Models/User.php`

### 3. Custom Generator

The module generator is configured to pre-create Internara's preferred layers:

- `Contracts/` (Interfaces)
- `Services/` (Business Logic)
- `Models/` (Persistence) For advanced module generation, refer to the
  **[Custom Module Generator](../advanced/custom-module-generator.md)**.

---

## Core Commands

Always use the module-specific Artisan commands provided by this package to ensure correct namespace
and path generation. For a complete list of all Artisan commands, refer to the
**[Artisan Commands Reference Guide](../artisan-commands-reference.md)**.

```bash
# Generate a new module
php artisan module:make <ModuleName>

# Generate resources within a module
php artisan module:make-model <ModelName> <ModuleName>
php artisan module:make-service <ServiceName> <ModuleName>
php artisan module:make-test <TestName> <ModuleName>
```

---

## Inter-Module Communication

Directly referencing models or concrete classes from other modules is **strictly forbidden**. You
must use the Service layer and type-hint the interfaces defined in the module's `Contracts/`
directory. For a comprehensive explanation of inter-module communication principles, refer to the
**[Architecture Guide](../architecture-guide.md)**.

---

**Navigation**

[← Previous: Laravel Livewire Integration](laravel-livewire.md) |
[Next: Laravel Modules Livewire Integration →](mhmiton-laravel-modules-livewire.md)
