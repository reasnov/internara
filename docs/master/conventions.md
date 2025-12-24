# Internara - Development Conventions

This document outlines the coding and development conventions for the Internara project. Adhering to these guidelines ensures consistency, maintainability, and high code quality across the application. These conventions supplement the broader guidelines found in the root `GEMINI.md` file and align with the principles detailed in the [Architecture Guide](architecture.md).

---

**Table of Contents**

1.  [General Conventions](#1-general-conventions)
2.  [PHP Conventions](#2-php-conventions)
3.  [Laravel Conventions](#3-laravel-conventions)
4.  [Modular Architecture (Laravel Modules) Conventions](#4-modular-architecture-laravel-modules-conventions)
5.  [Livewire & Volt Conventions](#5-livewire--volt-conventions)
6.  [Testing (Pest) Conventions](#6-testing-pest-conventions)
7.  [Code Formatting (Pint)](#7-code-formatting-pint)
8.  [Tailwind CSS Conventions](#8-tailwind-css-conventions)

---

## 1. General Conventions

*   **Language:** All code, comments, and documentation **must be written in English.**
*   **Naming:** Use descriptive names for variables, methods, classes, and modules (e.g., `isRegisteredForDiscounts` instead of `discount()`).
*   **DRY Principle:** Reuse existing components, classes, and functions before creating new ones. Avoid unnecessary duplication.
*   **Directory Structure:** Adhere strictly to the existing directory structure. **Do not create new base folders without explicit approval.**
*   **Comments & PHPDoc:** Use comprehensive PHPDoc for every method and class. Focus comments on *why* a piece of code exists or its complex logic, rather than *what* it does.

## 2. PHP Conventions

*   **Control Structures:** Always use curly braces for control structures (`if`, `for`, `while`, etc.), even for single-line statements, to prevent potential errors and improve readability.
*   **Constructor Property Promotion:** Utilize PHP 8's constructor property promotion for dependency injection and class properties. **Avoid empty constructors.**
*   **Type Declarations:** Always use explicit return type declarations and appropriate parameter type hints for all methods and properties.
*   **PHPDoc:** Prefer comprehensive PHPDoc blocks. Use [array shapes](https://docs.phpdoc.org/latest/guides/types.html#array-shapes) in PHPDoc for complex arrays where appropriate.
*   **Enums:** Enum keys should typically be `TitleCase`.
*   **Interface Naming:** Interface names should clearly describe their functionality and **should not be suffixed** with `Interface` or `Contract`. For example, use `UserRepository` instead of `UserRepositoryInterface` or `UserRepositoryContract`.

    ```php
    // Example: Type Declarations and Constructor Property Promotion
    class UserService
    {
        public function __construct(
            private readonly UserRepository $userRepository // Interface type-hint
        ) {}

        protected function isAccessible(User $user, ?string $path = null): bool
        {
            // ...
            return true;
        }
    }
    ```

## 3. Laravel Conventions

*   **Artisan Commands:**
    *   Always use `php artisan make:` commands to generate new files (models, controllers, services, etc.) to ensure correct scaffolding and namespace adherence.
    *   Pass `--no-interaction` and necessary options to `make` commands to automate creation.
    *   Use `php artisan make:class` for generic PHP classes not covered by more specific `make` commands.
*   **Database (Eloquent):**
    *   Prefer Eloquent relationship methods with explicit return type hints.
    *   Use Eloquent models (`Model::query()`) for database interactions over raw SQL queries (`DB::`).
    *   Employ eager loading (e.g., `with()`) to prevent N+1 query problems.
    *   When modifying a column in a migration, ensure all previously defined attributes for that column are explicitly included to prevent unintended changes.
    *   For Laravel 11+, utilize native eager load limiting (e.g., `$query->latest()->limit(10);`).
*   **Models:**
    *   Create [factories](https://laravel.com/docs/11.x/eloquent-factories) and [seeders](https://laravel.com/docs/11.x/seeding) for new models to facilitate testing and development setup.
    *   Prefer the `casts()` method over the deprecated `$casts` property for model attribute casting.
*   **Controllers & Validation:**
    *   Always use [Form Request classes](https://laravel.com/docs/11.x/validation#form-request-validation) for validation logic, never inline validation within controllers.
*   **Queues:** Use [queued jobs](https://laravel.com/docs/11.x/queues) (`ShouldQueue`) for all time-consuming operations to improve application responsiveness.
*   **Authentication/Authorization:** Utilize Laravel's built-in authentication and authorization features (Gates, Policies, Sanctum).
*   **URLs/Routing:** Use [named routes](https://laravel.com/docs/11.x/routing#named-routes) and the `route()` helper function for generating URLs to provide flexibility and maintainability.
*   **Configuration:** Access configuration values via `config('app.name')`. **Never use `env('APP_NAME')` directly outside of configuration files**, as `env()` values are not cached.

## 4. Modular Architecture (Laravel Modules) Conventions

Adhere strictly to the modular monolith architecture principles detailed in the [Architecture Guide](architecture.md) and [Modular Monolith Developer Guide](modular-monolith.md).

*   **Namespace Convention:** For module files located in `modules/{ModuleName}/src/{Subdirectory}/{FileName}.php`, the namespace **must omit the `src` segment**.
    *   **Example:** `Modules\{ModuleName}\{Subdirectory}`. This applies to `Livewire`, `Services`, `Repositories`, `Entities`, etc.
*   **Module Isolation & Portability:**
    *   **Shared (Mandatory Portable):** Must contain only universal code. It is strictly forbidden to put business-specific logic here.
    *   **Core & Support (Non-Portable):** Used for architecture and infrastructure specific to this project's business model.
    *   **Self-Containment:** A module should contain everything it needs to function (routes, views, config, logic, database).
    *   **Runtime Configuration:** If a module depends on a third-party package (e.g., Spatie Permission), it should use its Service Provider to override that package's configuration at runtime rather than relying on manual changes to the root `config/` directory.
    *   **Independence (No Inter-Module Hard Coupling):** Modules should not directly reference concrete classes or assume the existence of other modules. Use events, interfaces, or standard Laravel features (like `Gate`) to interact with system-wide services.
    *   **Controlled External Dependencies:** While modules should be independent of each other, they are permitted to depend on the Laravel framework or external packages. However, these dependencies should be clearly documented and restricted to the module's specific needs.
    *   **Minimal Cross-Module Leaks:** Avoid leaking module-specific logic into the `app/` or `config/` directories of the main application.
*   **Module Separation:** Each module is a self-contained unit representing a specific business domain.
*   **No Direct Model Access:** Modules **must not** directly access Eloquent Models (e.g., `Modules\User\Models\User`) from other modules. Interaction should always be via interfaces.
*   **Interface-First Communication:** All inter-module communication **must** occur through shared interfaces (contracts) defined in the respective module's `Contracts` directory.
    *   **Service-to-Service Calls:** For synchronous actions requiring immediate results from another module.
    *   **Events & Listeners:** For decoupled actions and side effects where one module doesn't need to know who reacts to its actions.
*   **Established Layers:** Adhere to the defined layers within each module:
    *   **UI Layer:** Livewire Components (`modules/ModuleName/Livewire/`) – handles user input, displays data, calls Service methods.
    *   **Business Logic Layer:** Services (`modules/ModuleName/Services/`) – orchestrates business logic, operates on Models or Entities.
    *   **Data Layer:** Models (`modules/ModuleName/Models/`) – Eloquent models for database interaction.
*   **Module Resource Access:** Use the `::` syntax with the module's `kebab-case` name to access its resources.
    *   **Views:** `view('user::profile')`
    *   **Translations:** `__('user::messages.welcome')`
    *   **Configuration:** `config('user.default_role')`

### 4.3 Repository & Entity Conventions (Optional)

The Repository and Entity layers are **optional** and should be used only when justified by specific architectural needs.

*   **When to Use:**
    *   When swapping storage backends (e.g., DB to API).
    *   When strict test isolation is required (mocking data access).
    *   When dealing with complex, non-Eloquent data structures.
*   **Source-Agnostic Principle:** If used, Repositories **must be entirely unaware of the underlying data source**.
*   **Interaction with Entities:** Repositories receive Entities for persistence operations (create, update) and return Entities for retrieval operations (find, get).
*   **Clear Interfaces:** Every Repository **must** define a clear interface (contract) in `modules/ModuleName/Contracts/Repositories/`.
*   **No Business Logic:** Repositories are solely for data access. They **must not** contain any business logic.

## 5. Livewire & Volt Conventions

*   **Thin Components (No Business Logic):** Livewire components **MUST NOT** contain business logic. Their role is strictly limited to handling UI events, managing component state, and orchestrating interactions with the Service layer. All business operations **MUST** be delegated to the appropriate Service methods.
*   **Modular Component Naming for Embedding:** When embedding Livewire components within Blade views, especially in a modular monolith context, use the `@livewire` directive with the `module-alias::component-dot-notation-name` format (e.g., `@livewire('user::users.delete-user')`). Avoid `<x-livewire-... />` for interactive component embedding.
*   **Component Creation:** Use `php artisan make:livewire [ComponentName] [ModuleName]` or `php artisan make:volt [ComponentName]` to ensure proper scaffolding.
*   **State Management:** Livewire component state lives on the server. Always validate and authorize all actions initiated from the frontend.
*   **Root Element:** Livewire components must render with a single root HTML element in their view.
*   **Directives:** Utilize `wire:loading`, `wire:dirty`, and `wire:key` in loops for improved user experience and performance.
*   **Lifecycle Hooks:** Employ [Livewire lifecycle hooks](https://livewire.laravel.com/docs/lifecycle-hooks) like `mount()`, `boot()`, and `updatedFoo()` for side effects and component initialization.
*   **Events:** Use `$this->dispatch()` for emitting events from Livewire components.
*   **Embeddable Components:** Livewire components **must** be designed as embeddable units. This means they should:
    *   Function correctly when included within any parent Blade view or other Livewire component without making assumptions about being a standalone page.
    *   Load all necessary data in their `mount()` or `boot()` methods.
    *   Avoid hardcoding layout structures directly within the component's `render()` method. Layouts should typically be managed by the parent view or explicitly set using `->layout()` if the component is intended as a root page component.
    *   **Rationale:** This promotes maximum reusability, simplifies testing, and maintains modularity across the application.
*   **Volt:** Follow existing project examples for determining whether to use the functional or class-based API for new Volt components.

## 6. Testing (Pest) Conventions

*   **Pest Only:** All tests **must be written using Pest**. Use `php artisan make:test --pest {name}` to generate test files.
*   **Comprehensive Testing:** Write tests for happy paths, failure paths, and edge cases to ensure robust functionality.
*   **No Test Deletion:** Existing tests are core to the application's stability and **must not be removed**.
*   **Assertions:** Prefer specific assertion methods (e.g., `assertForbidden()`) over generic status code checks (`assertStatus(403)`).
*   **Mocking:** Use `Pest\Laravel\mock` or `$this->mock()` for mocking dependencies.
*   **Browser Tests:** Store browser tests in the `tests/Browser/` directory. Leverage Laravel features like `Event::fake()` and model factories within browser tests.

## 7. Code Formatting (Pint)

*   **Automated Formatting:** Before finalizing any changes, always run `vendor/bin/pint --dirty` to format your code according to the project's PHP coding standards.

## 8. Tailwind CSS Conventions

*   **Utility-First:** Prioritize using Tailwind CSS utility classes for styling.
*   **Consistency:** Follow existing Tailwind usage patterns in sibling files and components.
*   **Responsive Design:** Use responsive prefixes (e.g., `sm:`, `md:`, `lg:`) for adapting styles to different screen sizes.
*   **Dark Mode:** If dark mode is supported (check existing components), ensure new components also include `dark:` variants for consistent theming.
*   **Component Extraction:** For repeated utility patterns, consider extracting them into Blade or Livewire components to promote reusability.
*   **DaisyUI Components:** Prefer using pre-built **DaisyUI** components (e.g., `btn`, `card`, `alert`) over composing similar elements from scratch with basic utilities. This ensures visual consistency and speeds up development.
*   **Iconography:** Use the **Iconify for Tailwind CSS** plugin for all icons, using the `icon-[collection--name]` class syntax as defined in the UI/UX Guidelines.
*   **Spacing:** Use Tailwind's [gap utilities](https://tailwindcss.com/docs/gap) for spacing items in lists or grids instead of applying individual margins.
*   **Tailwind 4:** Adhere to Tailwind 4's CSS-first configuration using the `@theme` directive and **avoid deprecated utilities**. Refer to the official Tailwind v4 migration guide for replacements.
