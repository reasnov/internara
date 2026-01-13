# Development Conventions

This document outlines the coding and development conventions for the Internara project. Adhering to these guidelines ensures consistency, maintainability, and high code quality across the application. These conventions supplement the broader guidelines found in the root `GEMINI.md` file and align with the principles detailed in the [Architecture Guide](architecture-guide.md).

---

**Table of Contents**

-   [Internara - Development Conventions](#internara---development-conventions)
    -   [1. General Conventions](#1-general-conventions)
    -   [2. PHP Conventions](#2-php-conventions)
    -   [3. Laravel Conventions](#3-laravel-conventions)
    -   [4. Modular Architecture (Laravel Modules) Conventions](#4-modular-architecture-laravel-modules-conventions)
        -   [4.1 Service Provider Conventions](#41-service-provider-conventions)
        -   [4.3 Repository \& Entity Conventions (Optional)](#43-repository--entity-conventions-optional)
    -   [5. Livewire \& Volt Conventions](#5-livewire--volt-conventions)
    -   [6. View \& Component Conventions](#6-view--component-conventions)
        -   [6.1 UI Module Component Conventions](#61-ui-module-component-conventions)
    -   [7. Testing (Pest) Conventions](#7-testing-pest-conventions)
    -   [8. Code Formatting (Pint)](#8-code-formatting-pint)
    -   [9. Tailwind CSS Conventions](#9-tailwind-css-conventions)

---

## 1. General Conventions

-   **Language:** All code, comments, and documentation **must be written in English.**
-   **Naming:** Use descriptive names for variables, methods, classes, and modules (e.g., `isRegisteredForDiscounts` instead of `discount()`).
-   **DRY Principle:** Reuse existing components, classes, and functions before creating new ones. Avoid unnecessary duplication.
-   **Directory Structure:** Adhere strictly to the existing directory structure. **Do not create new base folders without explicit approval.**
-   **Comments & PHPDoc:** Use comprehensive PHPDoc for every method and class. Focus comments on _why_ a piece of code exists or its complex logic, rather than _what_ it does.

## 2. PHP Conventions

-   **Control Structures:** Always use curly braces for control structures (`if`, `for`, `while`, etc.), even for single-line statements, to prevent potential errors and improve readability.
-   **Constructor Property Promotion:** Utilize PHP 8's constructor property promotion for dependency injection and class properties. **Avoid empty constructors.**
-   **Type Declarations:** Always use explicit return type declarations and appropriate parameter type hints for all methods and properties.
-   **PHPDoc:** Prefer comprehensive PHPDoc blocks. Use [array shapes](https://docs.phpdoc.org/latest/guides/types.html#array-shapes) in PHPDoc for complex arrays where appropriate.
-   **Enums:** Enum keys should typically be `TitleCase`.
-   **Interface Naming:** Interface names should clearly describe their functionality and **should not be suffixed** with `Interface` or `Contract`. For example, use `UserRepository` instead of `UserRepositoryInterface` or `UserRepositoryContract`.

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

-   **Artisan Commands:**
    -   Always use `php artisan make:` commands to generate new files (models, controllers, services, etc.) to ensure correct scaffolding and namespace adherence.
    -   Pass `--no-interaction` and necessary options to `make` commands to automate creation.
    -   Use `php artisan make:class` for generic PHP classes not covered by more specific `make` commands.
-   **Database (Eloquent):**
    -   Prefer Eloquent relationship methods with explicit return type hints.
    -   Use Eloquent models (`Model::query()`) for database interactions over raw SQL queries (`DB::`).
    -   Employ eager loading (e.g., `with()`) to prevent N+1 query problems.
    -   When modifying a column in a migration, ensure all previously defined attributes for that column are explicitly included to prevent unintended changes.
    -   For Laravel 12, utilize native eager load limiting (e.g., `$query->latest()->limit(10);`).
-   **UUID Usage Convention:**
    To enhance security and prevent resource enumeration attacks (e.g., guessing IDs from URLs like `/users/1`, `/users/2`), UUIDs should be used as the primary key for models whose IDs are or might be exposed publicly.
    -   **Use UUID for**: Public-facing models like `User`, `Internship`, `Post`, etc.
    -   **Use standard `bigIncrements` for**: Internal models that are not exposed, such as `jobs`, `cache`, `sessions`, pivot tables, or models used only for internal bookkeeping.
        This practice provides a good balance between security and performance/simplicity.
-   **Models:**
    -   Create [factories](https://laravel.com/docs/12.x/eloquent-factories) and [seeders](https://laravel.com/docs/12.x/seeding) for new models to facilitate testing and development setup.
    -   Prefer the `casts()` method over the deprecated `$casts` property for model attribute casting.
-   **Controllers & Validation:**
    -   Always use [Form Request classes](https://laravel.com/docs/12.x/validation#form-request-validation) for validation logic, never inline validation within controllers.
-   **Queues:** Use [queued jobs](https://laravel.com/docs/12.x/queues) (`ShouldQueue`) for all time-consuming operations to improve application responsiveness.
-   **Authentication/Authorization:** Utilize Laravel's built-in authentication and authorization features (Gates, Policies, Sanctum).
-   **URLs/Routing:** Use [named routes](https://laravel.com/docs/12.x/routing#named-routes) and the `route()` helper function for generating URLs to provide flexibility and maintainability.
-   **Configuration:** Access configuration values via `config('app.name')`. **Never use `env('APP_NAME')` directly outside of configuration files**, as `env()` values are not cached.

## 4. Modular Architecture (Laravel Modules) Conventions

Adhere strictly to the modular monolith architecture principles detailed in the **[Architecture Guide](architecture.md)** and the definitions of module types (Core, Shared, Support, UI, Domain) in the **[Foundational Module Philosophy Guide](foundational-module-philosophy.md)**. For a step-by-step feature implementation process, refer to the **[Workflow Developer Guide](modular-monolith-workflow.md)**.

Key conventions for modular development include:

### Module Directory Structure for Contracts and Concerns

To ensure a consistent and logical structure for related code within each module, `Contracts` and `Concerns` (or other "meta" code types) should be nested within their respective domain directories. This promotes clarity and makes it easier to locate related files.

**Preferred Structure:**
-   `modules/{ModuleName}/src/{DomainName}/Contracts/{ContractName}.php`
-   `modules/{ModuleName}/src/{DomainName}/Concerns/{ConcernName}.php`

**Examples:**
-   `modules/User/src/Services/Contracts/UserService.php`
-   `modules/User/src/Services/Concerns/ManagesUserRoles.php`
-   `modules/Post/src/Repositories/Contracts/PostRepository.php`

**Top-Level `src/Contracts` and `src/Concerns`:**
The top-level `modules/{ModuleName}/src/Contracts/` and `modules/{ModuleName}/src/Concerns/` directories should **only** be used for contracts or concerns that are truly generic and apply across multiple domains *within that specific module*. If a contract or concern is specific to a `Service`, `Repository`, `Controller`, etc., it *must* be nested within that domain's directory.


### 4.1 Service Provider Conventions

All module service providers (`Modules\{ModuleName}\Providers\YourServiceProvider.php`) **must** utilize the `Modules\Shared\Providers\Concerns\ManagesModuleProvider` trait to manage their lifecycle methods and service bindings. This ensures a consistent, structured, and lean implementation across all modules.

**Understanding Module Bindings vs. Global Auto-Discovery:**
Internara employs a robust service binding and auto-discovery system (detailed in the [Service Binding & Auto-Discovery Guide](service-binding-auto-discovery.md)). While the global `BindServiceProvider` automatically resolves interfaces to implementations based on conventions, **explicit bindings declared within a module's `bindings()` method (via the `ManagesModuleProvider` trait) always take precedence.** This allows modules to define precise, module-specific implementations or override global defaults as needed, ensuring control and isolation.

**Key Responsibilities Handled by `ManagesModuleProvider`:**

-   Automatic handling of module-specific commands, translations, configuration loading, views, and migrations.
-   Structured management of service bindings via the `bindings()` method.

**How to Implement:**

1.  **Use the Trait:** Add `use Modules\Shared\Providers\Concerns\ManagesModuleProvider;` and `use ManagesModuleProvider;` to your service provider class.
2.  **Define Module Metadata:** Ensure `protected string $name = 'YourModuleName';` and `protected string $nameLower = 'your_module_name';` are set.
3.  **Call Trait Methods in `boot()`:**
    ```php
    public function boot(): void
    {
        $this->registerCommands();
        $this->registerCommandSchedules();
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        // The loadMigrationsFrom call can be directly in boot() as it uses $this->name
        $this->loadMigrationsFrom(module_path($this->name, 'database/migrations'));
        // Any other module-specific boot logic
    }
    ```
4.  **Call Trait Methods in `register()`:**
    ```php
    public function register(): void
    {
        $this->registerBindings(); // For explicit bindings defined in bindings()
        // Any other module-specific registrations (e.g., registering other service providers)
        $this->app->register(EventServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);
    }
    ```
5.  **Define Service Bindings:** Implement the `bindings()` method to declare specific interface-to-implementation bindings for your module.
    `php
    protected function bindings(): array
    {
        return [
            // YourModuleContract::class => YourModuleImplementation::class,
            // Modules\Permission\Contracts\PermissionManager::class => Modules\Permission\Services\PermissionManager::class, // Example from Permission module
        ];
    }
    `
    This approach makes module service providers highly readable and focused on their unique logic.

### Module Naming

Prefer singular names for modules over plural names to represent a domain entity (e.g., `User` instead of `Users`, `Department` instead of `Departments`). This convention aligns with treating a module as a representation of a single domain concept. For a comprehensive understanding of module types and portability, refer to the **[Foundational Module Philosophy Guide](foundational-module-philosophy.md)**.

### Namespace Convention

For module files located in `modules/{ModuleName}/src/{Subdirectory}/{FileName}.php`, the namespace **must omit the `src` segment**. For detailed architectural principles, refer to the **[Architecture Guide](architecture.md)**.
    -   **Example:** `Modules\{ModuleName}\{Subdirectory}`. This applies to core components such as `Livewire` components, `Services`, `Repositories`, and `Entities`.

### Module Isolation & Portability

Modules are designed for isolation and portability. For detailed definitions of module portability and roles, refer to the **[Foundational Module Philosophy Guide](foundational-module-philosophy.md)**. Key aspects include:
    -   **Shared (Mandatory Portable):** Must contain only universal code, strictly forbidding business-specific logic.
    -   **Core & Support (Non-Portable):** Used for architecture and infrastructure specific to this project's business model.
    -   **Self-Containment:** A module should contain everything it needs to function (routes, views, config, logic, database).
    -   **Runtime Configuration:** Modules depending on third-party packages should use their Service Provider to override configuration at runtime, avoiding manual changes to root `config/` directory.
    -   **Independence (No Inter-Module Hard Coupling):** Modules should not directly reference concrete classes or assume the existence of other modules. Interaction must occur via events, interfaces, or standard Laravel features (like `Gate`). For a comprehensive explanation of inter-module communication, refer to the **[Architecture Guide](architecture.md)**.
    -   **Controlled External Dependencies:** Modules may depend on the Laravel framework or external packages, with clear documentation and restriction to module-specific needs.
    -   **Minimal Cross-Module Leaks:** Avoid leaking module-specific logic into the `app/` or `config/` directories of the main application.

### Module Separation

Each module is a self-contained unit representing a specific business domain. For foundational principles on modularity, refer to the **[Architecture Guide](architecture.md)**.

### No Direct Model Access

Modules **must not** directly access Eloquent Models (e.g., `Modules\User\Models\User`) from other modules. Interaction should always be via interfaces. For detailed inter-module communication patterns, refer to the **[Architecture Guide](architecture.md)**.

### Interface-First Communication

All inter-module communication **must** occur through shared interfaces (contracts) defined in the respective module's `Contracts` directory. For a comprehensive explanation of inter-module communication patterns, refer to the **[Architecture Guide](architecture.md)**.
    -   **Service-to-Service Calls:** For synchronous actions requiring immediate results from another module.
    -   **Events & Listeners:** For decoupled actions and side effects where one module doesn't need to know who reacts to its actions.

### Established Layers

Adhere to the defined layers within each module. For a comprehensive overview of the layered architecture, refer to the **[Architecture Guide](architecture.md)**.
    -   **UI Layer:** Livewire Components (`modules/ModuleName/Livewire/`) – handles user input, displays data, calls Service methods.
    -   **Business Logic Layer:** Services (`modules/ModuleName/Services/`) – orchestrates business logic, operates on Models or Entities.
    -   **Data Layer:** Models (`modules/ModuleName/Models/`) – Eloquent models for database interaction.

### Module Resource Access

Use the `::` syntax with the module's `kebab-case` name to access its resources.
    -   **Views:** `view('user::profile')`
    -   **Translations:** `__('user::messages.welcome')`
    -   **Configuration:** `config('user.default_role')`

### Localization Best Practices for Exceptions

To maximize reusability and maintain a clear structure, follow this two-tiered approach for exception translations. For a comprehensive guide on exception handling and localization, refer to the **[Exception Handling Guide](exception-handling-guidelines.md)**.
    1.  **Prioritize Shared Translations:** Before creating a new translation, always check if a generic message already exists that can be reused.
    2.  **Use Module-Specific Translations as a Last Resort:** Only create a new translation key within your specific module's `lang/` directory if the error message is truly unique to its business logic and cannot be covered by an existing shared translation.

### 4.2 Eloquent-based Services (`EloquentQuery`)

For services that primarily perform standard database operations on a single Eloquent model, the `Modules\Shared\Services\EloquentQuery` abstract class provides a standardized, boilerplate-free implementation. This is the preferred convention for creating resource-centric services (e.g., managing users, posts, or departments).

**Key Principles:**

1.  **Extend the Base Class:** Your service implementation **must** extend `Modules\Shared\Services\EloquentQuery`.
2.  **Extend the Contract:** Your service's contract (interface) **must** extend the `Modules\Shared\Services\Contracts\EloquentQuery` contract. This ensures your service inherits all the standard methods like `find()`, `paginate()`, `create()`, etc.
3.  **Initialize the Model:** In your service's constructor, you **must** set the associated Eloquent model by calling `$this->setModel(new YourModel())`.

This approach provides a full suite of CRUD methods out-of-the-box, while still allowing you to add custom business logic methods to your service.

**Example:**

**1. Service Contract (`modules/User/src/Contracts/Services/UserService.php`)**

```php
<?php

namespace Modules\User\Contracts\Services;

use Modules\Shared\Services\Contracts\EloquentQuery;

/**
 * @extends EloquentQuery<\Modules\User\Models\User>
 */
interface UserService extends EloquentQuery
{
    // Define custom business logic methods here
    public function findByEmail(string $email): ?\Modules\User\Models\User;
}
```

**2. Service Implementation (`modules/User/src/Services/UserService.php`)**

```php
<?php

namespace Modules\User\Services;

use Modules\Shared\Services\EloquentQuery;
use Modules\User\Contracts\Services\UserService as UserServiceContract;
use Modules\User\Models\User;

/**
 * @implements UserServiceContract
 */
class UserService extends EloquentQuery implements UserServiceContract
{
    public function __construct()
    {
        $this->setModel(new User());
    }

    // Implement custom business logic
    public function findByEmail(string $email): ?User
    {
        return $this->first(['email' => $email]);
    }

    // You can also override base methods to add logic
    public function create(array $data): User
    {
        // Custom logic before creation...
        return parent::create($data);
    }
}
```

### 4.3 Repository & Entity Conventions (Optional)

The Repository and Entity layers are **optional** and should be used only when justified by specific architectural needs.

-   **When to Use:**
    -   When swapping storage backends (e.g., DB to API).
    -   When strict test isolation is required (mocking data access).
    -   When dealing with complex, non-Eloquent data structures.
-   **Source-Agnostic Principle:** If used, Repositories **must be entirely unaware of the underlying data source**.
-   **Interaction with Entities:** Repositories receive Entities for persistence operations (create, update) and return Entities for retrieval operations (find, get).
-   **Clear Interfaces:** Every Repository **must** define a clear interface (contract) in `modules/ModuleName/Contracts/Repositories/`.
-   **No Business Logic:** Repositories are solely for data access. They **must not** contain any business logic.

## 5. Livewire & Volt Conventions

Adhere to these conventions when developing Livewire and Volt components:

-   **Thin Components (No Business Logic):** Livewire components **MUST NOT** contain business logic. Their role is strictly limited to handling UI events, managing component state, and orchestrating interactions with the Service layer. All business operations **MUST** be delegated to the appropriate Service methods. For more on the layered architecture, refer to the **[Architecture Guide](architecture.md)**.
-   **Modular Component Naming for Embedding:** When embedding Livewire components within Blade views, especially in a modular monolith context, use the `@livewire` directive with the `module-alias::component-dot-notation-name` format (e.g., `@livewire('user::users.delete-user')`). Avoid `<x-livewire-... />` for interactive component embedding. For more details on Livewire's integration, refer to the **[Livewire Integration Guide](packages/laravel-livewire.md)**.
-   **Component Creation:** Use `php artisan make:livewire [ComponentName] [ModuleName]` or `php artisan make:volt [ComponentName]` to ensure proper scaffolding.
-   **State Management:** Livewire component state lives on the server. Always validate and authorize all actions initiated from the frontend.
-   **Root Element:** Livewire components must render with a single root HTML element in their view.
-   **Directives:** Utilize `wire:loading`, `wire:dirty`, and `wire:key` in loops for improved user experience and performance.
-   **Lifecycle Hooks:** Employ [Livewire lifecycle hooks](https://livewire.laravel.com/docs/lifecycle-hooks) like `mount()`, `boot()`, and `updatedFoo()` for side effects and component initialization.
-   **Dependency Injection:** All dependency injection **must** be performed in the `boot()` method, not the constructor. For a detailed explanation of why, refer to the **[Livewire Integration Guide](packages/livewire.md)**.
-   **Events:** Use `$this->dispatch()` for emitting events from Livewire components. For cross-module communication via events, refer to the **[Architecture Guide](architecture.md)**.
-   **Embeddable Components:** Livewire components **must** be designed as embeddable units. This means they should function correctly when included within any parent Blade view or other Livewire component without making assumptions about being a standalone page.
-   **Volt:** Follow existing project examples for determining whether to use the functional or class-based API for new Volt components.

## 6. View & Component Conventions

To maintain a clean and organized `resources/views/components` directory in the main application, and to adhere to the project's overall UI/UX strategy, all Blade components should follow these structural guidelines. For a comprehensive overview of UI/UX principles, design standards, and component strategy, refer to the **[UI/UX Development Guide](ui-ux-development-guide.md)**.

-   **`views/components/layouts/`**
    -   **Purpose:** For major layout components that define the primary structure of a page (e.g., `app.blade.php`, `guest.blade.php`).
-   **`views/components/ui/`**
    -   **Purpose:** For small, generic, and highly reusable UI components that are application-agnostic.
    -   **Examples:** `button.blade.php`, `input.blade.php`, `card.blade.php`.
-   **`views/components/partials/{pageName}/`**
    -   **Purpose:** For composite components that are specific to a certain page or feature section.

### 6.1 UI Module Component Conventions

The `UI` module serves as the central library for all global, application-wide UI components, following a flatter structure for simplified usage. For the full component strategy and interactivity guidelines, refer to the **[UI/UX Development Guide](ui-ux-development-guide.md)**.

-   **Location:** All generic, reusable components (like navbars, buttons, cards, etc.) should be placed directly in:
    `modules/UI/resources/views/components/`
-   **Usage:** Since these components are registered under the `ui` namespace, they can be called directly without any subdirectory prefix.
    ```blade
    {{-- CORRECT --}}
    <x-ui::navbar />
    <x-ui::button />

        {{-- INCORRECT --}}
        <x-ui::ui.navbar />
    ```
This convention keeps component tags clean and acknowledges that the `UI` module itself is the designated "ui" library for the project.

## 7. Testing (Pest) Conventions

Adhere to these conventions for testing. For a comprehensive guide on the project's testing philosophy, framework usage, directory structure, and detailed examples, refer to the **[Testing Guide](testing.md)**.

-   **Pest Only:** All tests **must be written using Pest**. Use `php artisan make:test --pest {name}` to generate test files.
-   **Comprehensive Testing:** Write tests for happy paths, failure paths, and edge cases to ensure robust functionality.
-   **No Test Deletion:** Existing tests are core to the application's stability and **must not be removed**.
-   **Assertions:** Prefer specific assertion methods (e.g., `assertForbidden()`) over generic status code checks (`assertStatus(403)`).
-   **Mocking:** Use `Pest\Laravel\mock` or `$this->mock()` for mocking dependencies.
-   **Browser Tests:** Store browser tests in the `tests/Browser/` directory. Leverage Laravel features like `Event::fake()` and model factories within browser tests.

## 8. Code Formatting (Pint)

-   **Automated Formatting:** Before finalizing any changes, always run `vendor/bin/pint --dirty` to format your code according to the project's PHP coding standards.

## 9. Tailwind CSS Conventions

Adhere to these conventions when using Tailwind CSS. For a comprehensive guide on UI/UX principles, theming, component strategy, and frontend development standards, refer to the **[UI/UX Development Guide](ui-ux-development-guide.md)**.

-   **Utility-First:** Prioritize using Tailwind CSS utility classes for styling.
-   **Consistency:** Follow existing Tailwind usage patterns in sibling files and components.
-   **Responsive Design:** Use responsive prefixes (e.g., `sm:`, `md:`, `lg:`) for adapting styles to different screen sizes.
-   **Dark Mode:** Ensure components support dark mode using `dark:` variants for consistent theming.
-   **Component Extraction:** For repeated utility patterns, consider extracting them into Blade or Livewire components to promote reusability.
-   **DaisyUI Components:** Prefer using pre-built **DaisyUI** components over composing similar elements from scratch.
-   **Iconography:** Use the **Iconify for Tailwind CSS** plugin for all icons.
-   **Spacing:** Use Tailwind's [gap utilities](https://tailwindcss.com/docs/gap) for spacing items in lists or grids instead of applying individual margins.
-   **Tailwind 4:** Adhere to Tailwind 4's CSS-first configuration using the `@theme` directive and **avoid deprecated utilities**.
