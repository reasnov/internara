# Internara - Modular Monolith Developer Guide

This document provides a practical, step-by-step guide for developers on how to implement new features within the modular architecture of the Internara application. It builds upon the foundational concepts introduced in the [Architecture Guide](architecture.md).

---

**Table of Contents**

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

Our modular architecture aims to enforce a strict separation of concerns, where each module is a self-contained unit representing a specific business domain.

-   **Isolation Principle:** One module **MUST NOT** directly reference a concrete class or an internal model from another module.
-   **Namespace Convention:** For all module files located in `modules/{ModuleName}/src/{Subdirectory}/{FileName}.php`, the namespace **must omit the `src` segment**.
-   **Example:** A model `User.php` in `modules/User/src/Models/` should have the namespace `Modules\User\Models`.

## 2. Key Concepts: Eloquent Models vs. Services

-   **Eloquent Model (`Modules/User/Models/User.php`):**
    -   Represents the database table and handles persistence.
    -   Handles configurable ID types (UUID or Integer).
    -   **It is our data persistence object.**

-   **Service (`Modules/User/Services/UserService.php`):**
    -   Encapsulates the core business logic and rules.
    -   Orchestrates operations on Models.
    -   The entry point for UI components to interact with the domain.

## 3. Standard Workflow (Service-Model)

### Step 1: Create the Module

If your feature requires a new domain, start by creating a new module.

```bash
php artisan module:make User
```

### Step 2: Create the Eloquent Model and Migration

The **Eloquent Model** defines your direct interface to the database table. Ensure you use configurable ID types (matching the User ID type) if the module needs to be portable.

```bash
php artisan module:make-model User User --migration
```

### 3.3 Create the Service (Interface and Implementation)

The service encapsulates the business logic.

**3.3.1 Create the Service Interface**
Generate the contract (interface) for your service in `src/Contracts/Services`:

```bash
php artisan module:make-interface Services/UserService User
```

**3.3.2 Create the Service Implementation**

Next, generate the concrete implementation for your service in `src/Services`:

```bash
php artisan module:make-service UserService User
```

**3.3.3 Using EloquentQuery Trait for Common CRUD Operations**

For services that primarily deal with standard CRUD (Create, Read, Update, Delete) operations on a single Eloquent model, it is highly recommended to use the `Modules\Shared\Concerns\EloquentQuery` trait. This trait provides boilerplate implementations for common methods, reducing code duplication and enforcing consistency.

**How to Implement:**

1.  **Use `EloquentQuery` Trait**: Your service implementation class should `use` the `EloquentQuery` trait.
2.  **Set Model in Constructor**: In your service's constructor, call `parent::__construct()` (if extending another class) and then `setModel()` with an instance of the Eloquent model your service manages. This is crucial for initializing the trait.

*Example for `modules/User/Services/UserService.php`:*

```php
<?php

namespace Modules\User\Services;

use Modules\Shared\Concerns\EloquentQuery;
use Modules\User\Contracts\Services\UserService as UserServiceContract;
use Modules\User\Models\User;

class UserService implements UserServiceContract
{
    use EloquentQuery;

    public function __construct(User $userModel)
    {
        $this->setModel($userModel); // Initialize the EloquentQuery trait with the model
    }

    // Add specific business logic methods here.
    // Overwrite CRUD methods if specific logic is needed (e.g., authorization, complex validation).
}
```

### Step 4: Create the UI Component

For user interaction, create a Livewire component in `src/Livewire`. This component will interact with your **Service interface**.

```bash
php artisan module:make-livewire CreateUser User --view
```

### Step 5: Create the Route

Finally, expose your Livewire component via a route in your module's `routes/web.php`.

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

1.  **Route** (`web.php`) maps to a **Livewire Component**.
2.  The **Livewire Component** authorizes the user using **Policies** and calls a method on a **Service interface**.
3.  The **Service** executes business logic and interacts with the **Eloquent Model**.
4.  The **Model** persists/retrieves data from the database.
5.  Data returns up the chain to the **Livewire Component** for rendering.

## 6. Testing Your Module

All tests **must** be written using Pest.

-   **Location**: Tests for a specific module reside in `modules/{ModuleName}/tests/`.
-   **Creation**:
    ```bash
    php artisan module:make-test <TestName> <ModuleName> [--feature]
    ```
-   **Execution**:
    ```bash
    php artisan test --filter=<ModuleName>
    ```
