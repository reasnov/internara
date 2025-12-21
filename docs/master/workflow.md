# Internara - Modular Monolith Developer Guide

This document provides a practical, step-by-step guide for developers on how to implement new features within the modular architecture of the Internara application. It builds upon the foundational concepts introduced in the [Architecture Guide](architecture.md).

---

**Table of Contents**

-   [Internara - Modular Monolith Developer Guide](#internara---modular-monolith-developer-guide)
    -   [1. Core Philosophy](#1-core-philosophy)
    -   [2. Key Concepts: Eloquent Models vs. Services](#2-key-concepts-eloquent-models-vs-services)
    -   [3. Standard Workflow (Service-Model)](#3-standard-workflow-service-model)
        -   [Step 1: Create the Module](#step-1-create-the-module)
        -   [Step 2: Create the Eloquent Model and Migration](#step-2-create-the-eloquent-model-and-migration)
        -   [Step 3: Create the Service (Interface and Implementation)](#step-3-create-the-service-interface-and-implementation)
        -   [Step 4: Create the UI Component](#step-4-create-the-ui-component)
        -   [Step 5: Create the Route](#step-5-create-the-route)
    -   [4. Advanced Workflow (Optional: Repositories & Entities)](#4-advanced-workflow-optional-repositories--entities)
    -   [5. Data Flow Summary](#5-data-flow-summary)
    -   [6. Testing Your Module](#6-testing-your-module)

---

## 1. Core Philosophy

Our modular architecture aims to enforce a strict separation of concerns, where each module is a self-contained unit representing a specific business domain. This approach promotes:

-   **Low Coupling:** Modules should **not** directly access internal concrete classes (like Eloquent Models or specific Service implementations) of other modules.
-   **High Cohesion:** All components related to a specific domain (e.g., `User` management, `Internship` processes) should reside within that module.
-   **Interface-Driven:** Communication between modules **must** occur through well-defined PHP interfaces (contracts), ensuring loose coupling and clear boundaries.

**Namespace Convention:**
For all module files located in `modules/{ModuleName}/src/{Subdirectory}/{FileName}.php`, the namespace **must omit the `src` segment**.

-   **Example:** A model `User.php` in `modules/User/src/Models/` should have the namespace `Modules\User\Models`, not `Modules\User\src\Models`. This convention ensures a cleaner and more direct namespace structure.

## 2. Key Concepts: Eloquent Models vs. Services

-   **Eloquent Model (`Modules/User/Models/User.php`):**
    -   Represents the database table and handles persistence.
    -   Can contain basic domain behavior (e.g., accessors, scopes).
    -   **It is our data persistence object.**

-   **Service (`Modules/User/Services/UserService.php`):**
    -   Encapsulates the core business logic and rules.
    -   Orchestrates operations on Models.
    -   The entry point for UI components to interact with the domain.

## 3. Standard Workflow (Service-Model)

This section details the standard workflow for creating a new feature, using the `User` module as an example.

### Step 1: Create the Module

If your feature requires a new domain, start by creating a new module. For this example, we'll assume the `User` module already exists.

```bash
php artisan module:make User
```

### Step 2: Create the Eloquent Model and Migration

The **Eloquent Model** defines your direct interface to the database table.

```bash
php artisan module:make-model User User --migration
```

### Step 3: Create the Service (Interface and Implementation)

The service encapsulates the business logic.

**3.1. Create the Service Interface**
Generate the contract (interface) for your service:

```bash
php artisan module:make-interface Services/UserService User
```

**3.2. Create the Service Implementation**
Next, generate the concrete implementation for your service:

```bash
php artisan module:make-service UserService User
```

-   **File Generated:** `modules/User/Services/UserService.php`

    ```php
    <?php

    namespace Modules\User\Services;

    use Modules\User\Models\User;
    use Modules\User\Contracts\Services\UserService as UserServiceContract;

    class UserService implements UserServiceContract
    {
        public function createUser(array $data): User
        {
            // Business logic (validation, events, etc.)
            // ...

            return User::create($data);
        }

        public function getUser(int $id): ?User
        {
            return User::find($id);
        }
    }
    ```

### Step 4: Create the UI Component

For user interaction, create a Livewire component. This component will interact with your **Service**.

```bash
php artisan module:make-livewire CreateUser User --view
```

### Step 5: Create the Route

Finally, expose your Livewire component via a route in your module's `web.php`.

```php
// Modules/User/routes/web.php
use Illuminate\Support\Facades\Route;
use Modules\User\Livewire\CreateUser;

Route::get('/register', CreateUser::class)->name('user.register');
```

---

## 4. Advanced Workflow (Optional: Repositories & Entities)

This workflow is reserved for complex scenarios where strict decoupling from Eloquent is required.

1.  **Create Entity:** `php artisan module:make-entity UserEntity User`
2.  **Create Repository Interface:** `php artisan module:make-interface Repositories/UserRepository User`
3.  **Create Repository Implementation:** `php artisan module:make-repository UserRepository User`
4.  **Inject Repository into Service:** The Service will now interact with the Repository using Entities, instead of the Model directly.

## 5. Data Flow Summary

The standard flow for a request is:

1.  **Route** (`web.php`) maps to a **Livewire Component**.
2.  The **Livewire Component** calls a method on a **Service interface**.
3.  The **Service** executes business logic and interacts with the **Eloquent Model**.
4.  The **Model** persists/retrieves data from the database.
5.  Data returns up the chain to the **Livewire Component** for rendering.

## 6. Testing Your Module

Testing is integral to modular development.

-   **Location**: Tests for a specific module reside in `modules/{ModuleName}/tests/`.
-   **Creation**: Use the Artisan command:
    ```bash
    php artisan module:make-test <TestName> <ModuleName> [--feature]
    ```
-   **Execution**:
    ```bash
    php artisan test --filter=<ModuleName>
    ```