# nwidart/laravel-modules Integration

This package is the foundation of Internara's **Modular Monolith**. It manages the lifecycle and structure of our modules.

---

## Custom Configuration

We have modified the default behavior of this package in `config/modules.php` to align with our architectural standards:

### 1. Source Directory (`src/`)
Instead of the default `app/` folder, our domain logic resides in `src/`.
- **Config:** `'app_folder' => 'src/'`
- **Result:** `modules/User/src/Models/User.php`

### 2. Namespace Omission
The `src` segment is intentionally omitted from the namespace.
- **Convention:** `Modules\User\Models\User`
- **Path:** `modules/User/src/Models/User.php`

### 3. Custom Generator
The module generator is configured to pre-create our preferred layers:
- `Contracts/` (Interfaces)
- `Services/` (Business Logic)
- `Models/` (Persistence)

---

## Core Commands

Always use the module-specific commands to ensure correct namespace and path generation:

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
Directly referencing models from other modules is **strictly forbidden**. You must use the Service layer and type-hint the interfaces defined in the module's `Contracts/` directory.
