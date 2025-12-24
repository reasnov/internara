# Internara - Developer Architecture Guide

Welcome, developer! This guide provides a comprehensive understanding of Internara's modular architecture, helping you build features efficiently and maintain a robust codebase.

---

**Table of Contents**

1.  [Core Architectural Principles](#1-core-architectural-principles)
2.  [The TALL Stack](#2-the-tall-stack)
3.  [The Layered Architecture](#3-the-layered-architecture)
    *   [3.1 UI Layer: Livewire Components](#31-ui-layer-livewire-components)
    *   [3.2 Business Logic Layer: Services](#32-business-logic-layer-services)
    *   [3.3 Data Layer: Eloquent Models](#33-data-layer-eloquent-models)
    *   [3.4 Optional Layers: Repositories & Entities](#34-optional-layers-repositories--entities)
4.  [Pragmatic Layered Architecture & Communication Rules](#4-pragmatic-layered-architecture--communication-rules)
    *   [4.1 The Golden Rule of Layered Communication](#41-the-golden-rule-of-layered-communication)
    *   [4.2 Interface-First & Knowledge Hiding](#42-interface-first--knowledge-hiding)
5.  [Inter-Module Communication: The Golden Rule](#5-inter-module-communication-the-golden-rule)
    *   [5.1 Pattern 1: Service-to-Service Calls (Synchronous)](#51-pattern-1-service-to-service-calls-synchronous)
    *   [5.2 Pattern 2: Events & Listeners (Decoupled Actions)](#52-pattern-2-events--listeners-decoupled-actions)
6.  [Practical Guide: Module Usage](#6-practical-guide-module-usage)
    *   [6.1 Accessing Module Resources](#61-accessing-module-resources)
    *   [6.2 Essential Module Artisan Commands](#62-essential-module-artisan-commands)

---

## 1. Core Architectural Principles

Internara is built as a **Modular Monolith**, meaning it's a collection of self-contained mini-applications (called **Modules**) residing within a single Laravel project. Each module (e.g., `User`, `Internship`) manages a specific business domain, enhancing organization, maintainability, and scalability.

**Key Concepts:**

*   **Modular Monolith:** Leveraging `nwidart/laravel-modules` to encapsulate business domains.
*   **Isolation & Portability:** Each module is designed to be as self-contained as possible. This means minimizing external dependencies, using runtime configuration overrides, and ensuring that a module could, in theory, be extracted and used in another project with minimal friction.
*   **UI Framework:** All user interfaces and interactivity are built using **Livewire** components, powered by the TALL Stack.
*   **Backend Structure:** Logic is strictly organized into distinct layers:
    *   **Services:** Orchestrate business logic ("what to do").
    *   **Models:** Represent persisted domain data and contain limited, domain-relevant behavior.
    *   **Optional Layers (Repositories/Entities):** Abstractions for complex data access or integrations, **not included by default**.
*   **Namespace Convention:** For module files located in `modules/{ModuleName}/src/{Subdirectory}/{FileName}.php`, the namespace **must omit the `src` segment**.
    *   **Example:** `Modules\{ModuleName}\{Subdirectory}`. This applies to `Livewire`, `Services`, `Models`, and other subdirectories within a module's `src` folder.

For a complete, step-by-step example of creating a new feature within this architecture, refer to the **[Modular Monolith Developer Guide](modular-monolith.md)**.

## 2. The TALL Stack

The project is built on the TALL stack, a full-stack development solution that allows for building robust, modern web applications primarily using PHP.

*   **Tailwind CSS (v4):** A utility-first CSS framework that provides low-level utility classes to build custom designs directly in your markup. This approach avoids premature abstraction and allows for highly customizable and maintainable styling. To ensure consistency and rapid development, we use the **DaisyUI** component library.
*   **Alpine.js:** A rugged, minimal JavaScript framework for adding client-side interactivity to your templates. It's often used for small interactions like dropdowns or toggles. In this project, Alpine.js is automatically included with Livewire 3, making it readily available within Livewire components.
*   **Laravel (v12):** The core backend framework providing the application's foundation. It handles routing, database management through its Eloquent ORM, authentication, and the overall structure that enables the modular monolith architecture.
*   **Livewire (v3):** A full-stack framework for Laravel that allows you to build dynamic, reactive interfaces using PHP classes and Blade templates. It eliminates the need for a separate frontend JavaScript framework like React or Vue for most use cases, significantly simplifying the development workflow. This project also uses **Volt**, which allows for creating single-file Livewire components.

## 3. The Layered Architecture

Each module adheres to a strict layered architecture to enforce separation of concerns. The flow of data and control generally moves from the UI layer downwards.

**Important Note on Namespaces:** Consistent with the project's namespace convention, all classes within these layers will use a namespace that omits the `app` segment. For instance, a Service in the `User` module would be namespaced `Modules\User\Services`, not `Modules\User\app\Services`.

### 3.1 UI Layer: Livewire Components

The entry point for user interaction with the application. Livewire components serve as the **Presentation Layer**, responsible solely for rendering the UI and handling user input.

*   **Purpose:** To capture user input, manage component state, and display data.
*   **Responsibilities:**
    *   **UI Interaction Logic ONLY:** Livewire components **MUST NOT** contain business logic. Their role is strictly limited to handling UI events, managing component state, and orchestrating interactions with the Service layer.
    *   **Delegation to Services:** All business operations (e.g., data creation, updates, deletions, complex validations, authorization checks beyond simple rendering conditions) **MUST** be delegated to the appropriate Service methods.
    *   Displays data retrieved from the business logic layer.
*   **Location:** `modules/ModuleName/Livewire/`
*   **Creation Command:**
    ```bash
    php artisan module:make-livewire CreateUser User --view
    ```

#### Livewire Component Naming in Modular Monolith

When embedding Livewire components within Blade views in a modular monolith (using `nwidart/laravel-modules`), it is crucial to use the correct naming convention to ensure auto-discovery and proper rendering.

*   **Standard Embedding Directive:** Always use the `@livewire` Blade directive for embedding interactive Livewire components.
*   **Module-Aliased Component Name:** Components residing within a module (`Modules\{ModuleName}\src\Livewire\Subdirectory\ComponentName.php`) should be referenced using the module's alias followed by a double colon (`::`), then the dot-notation path relative to the module's `src/Livewire` directory. The module alias typically matches the module's lowercase name (e.g., `user` for the `User` module).

    **Syntax:** `@livewire('{module-alias}::{component-dot-notation-name}', [...$parameters])`

    **Example:**
    For a Livewire component class: `Modules\User\src\Livewire\Users\DeleteUser.php`
    Its corresponding component name would be: `users.delete-user`
    The correct embedding in a Blade view would be:
    ```blade
    @livewire('user::users.delete-user')
    ```
    or with parameters:
    ```blade
    @livewire('user::users.delete-user', ['userId' => $user->id])
    ```
*   **Avoid `<x-livewire-... />` for interactive embedding:** While `<x-livewire-... />` syntax works as a Blade Component alias, the `@livewire` directive is the recommended and clearer way to embed interactive Livewire components, especially when passing parameters or dealing with specific component lifecycle events.

### 3.2 Business Logic Layer: Services

The "brain" of your feature, orchestrating specific business operations.

*   **Purpose:** To implement and coordinate the core business logic.
*   **Responsibilities:**
    *   Receives data (often as validated arrays or simple value objects) from the UI layer.
    *   Performs domain-specific validation and business rules.
    *   Interacts directly with **Eloquent Models** to save or retrieve data (in the default workflow).
    *   Dispatches events to notify other parts of the application about changes.
    *   Should be thin and focused on a single business concern.
*   **Location:** `modules/ModuleName/Services/`
*   **Creation Command:**
    ```bash
    php artisan module:make-service UserService User
    ```

### 3.3 Data Layer: Eloquent Models

The primary layer for interacting with the database.

*   **Purpose:** To represent persisted domain data and provide an interface to the database via Eloquent ORM.
*   **Responsibilities:**
    *   Defining database relationships, scopes, and accessors.
    *   Handling data persistence and retrieval.
    *   May contain limited, domain-relevant behavior (e.g., helper methods like `isActive()`).
*   **Location:** `modules/ModuleName/Models/`
*   **Creation Command:**
    ```bash
    php artisan module:make-model User User --migration
    ```

### 3.4 Optional Layers: Repositories & Entities

These layers are **not included by default** and should only be introduced when strictly justified by architectural needs (e.g., swapping storage backends, complex external integrations, or strict decoupling requirements).

#### Repositories
*   **Purpose:** To abstract data persistence details, completely decoupling business logic from Eloquent.
*   **When to Use:** When you need to support multiple data sources (e.g., Database AND API) or when testing requires strict mocking of the data layer without touching the database.
*   **Location:** `modules/ModuleName/Repositories/`

#### Entities (DTOs)
*   **Purpose:** Plain PHP objects to transfer data between layers, independent of Eloquent.
*   **When to Use:** When data structures are complex, come from external sources, or need to be strictly decoupled from the database schema.
*   **Location:** `modules/ModuleName/Entities/`

## 4. Pragmatic Layered Architecture & Communication Rules

To ensure maintainability while avoiding over-engineering, Internara adopts a **Pragmatic Layered Architecture**.

### 4.1 The Golden Rule of Layered Communication

Communication flows downwards.

*   **Presentation Layer (Livewire)**: Communicates only with the **Service Layer**.
*   **Service Layer**: Communicates with the **Model Layer** (or optionally, the **Repository Layer**).
*   **Model Layer**: Represents the persistence mechanism and should not initiate communication upwards.

### 4.2 Interface-First & Knowledge Hiding

When communicating *between modules*, strictly adhere to the **Interface-First Principle**.

*   **Example:** If Module A needs to call a service in Module B, it should depend on an interface (e.g., `Modules\User\Contracts\Services\UserService`), not the concrete class.

## 5. Inter-Module Communication: The Golden Rule

The most critical rule for maintaining a loosely coupled and maintainable modular monolith is the **Interface-First Principle**.

> **Golden Rule:** One module **MUST NOT** directly reference a concrete class (e.g., a `Service` or `Repository` implementation) from another module. All communication between modules **must** happen through shared interfaces (contracts).

This rule is non-negotiable. It is the foundation for a scalable and maintainable application.

### 5.1 Pattern 1: Service-to-Service Calls (Synchronous)

Use this pattern when an action in `Module A` **immediately** requires a result or a direct interaction with `Module B`.

*   **Usage:** For direct, synchronous dependencies where one module needs to invoke specific logic from another and get an immediate response.
*   **How it Works:**
    1.  `Module B` defines a `Service Interface` (e.g., `Modules\User\Contracts\Services\UserService`) and provides a concrete implementation.
    2.  `Module A` (e.g., `InternshipService`) *type-hints* `Module B`'s `Service Interface` in its constructor.
    3.  Laravel's Service Container automatically injects the concrete `UserService` implementation without `InternshipService` knowing the specific class.

    ```php
    // In Modules/Internship/Services/InternshipService.php
    namespace Modules\Internship\Services;

    use Modules\User\Contracts\Services\UserService; // Type-hinting the interface

    class InternshipService
    {
        public function __construct(
            private readonly UserService $userService // Dependency injected via interface
        ) {}

        public function registerStudent(int $userId, int $placementId): void
        {
            $user = $this->userService->findUserById($userId); // Calling logic from User module

            if (!$user || !$user->isActive()) {
                throw new \Exception('User is not active or not found and cannot register.');
            }
            // ... continue with registration logic for internship
        }
    }
    ```

### 5.2 Pattern 2: Events & Listeners (Decoupled Actions)

This is the preferred approach for handling side effects or when one action should trigger several independent outcomes across different modules without tight coupling.

*   **Usage:** When an action occurs in `Module A`, and other modules might need to react to it without `Module A` needing to know or care who those reactors are.
*   **How it Works:**
    1.  `Module A` (e.g., `InternshipService`) dispatches an `Event` (e.g., `InternshipApproved`). This event should carry necessary data (e.g., `internshipId`, `userId`).
    2.  Other modules (e.g., `User`, `Notification`) define `Listeners` that are configured to react to this specific `Event`.
    3.  The `Internship` module remains completely unaware of which other modules react to its event, ensuring strong decoupling.

    **Example:** When an `Internship` is approved, the `User` module might update the student's status, and a `Notification` module might queue an email. The `Internship` module only dispatches `InternshipApproved`.

## 6. Practical Guide: Module Usage

### 6.1 Accessing Module Resources

Use the `::` syntax with the module's `kebab-case` name (e.g., `user`, `internship`) to access its resources:

*   **Views:** `view('user::profile')`
*   **Translations:** `__('user::messages.welcome')`
*   **Configuration:** `config('user.default_role')`

### 6.2 Essential Module Artisan Commands

These commands simplify the creation of module-specific files, ensuring they follow the correct structure and namespace conventions.

*   `php artisan module:list`: List all registered modules.
*   `php artisan module:make YourModuleName`: Create a new module.
*   `php artisan module:migrate YourModuleName`: Run database migrations for a specific module.
*   `php artisan module:seed YourModuleName`: Seed the database for a specific module.
*   `php artisan module:make-model ModelName YourModuleName --migration`: Create an Eloquent Model and its corresponding migration within a module.
*   `php artisan module:make-service ServiceName YourModuleName`: Create a Service class within a module.
*   `php artisan module:make-livewire ComponentName YourModuleName --view`: Create a Livewire component and its associated Blade view within a module.

This robust and maintainable architecture ensures the long-term health of the codebase. By consistently following these patterns, you contribute to a scalable and understandable application.
