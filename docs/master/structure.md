# Internara - Core, Shared, and Support Modules Overview

This document provides a detailed overview of the foundational `Core`, `Shared`, and `Support` modules within Internara's modular architecture. These modules are crucial for maintaining the application's structure, promoting reusability, and enabling extensibility, all while adhering to the established [Architecture Guide](architecture.md) and [Development Conventions](conventions.md).

---

**Table of Contents**

1.  [Core Module](#1-core-module)
2.  [Shared Module](#2-shared-module)
3.  [Support Module](#3-support-module)
4.  [Domain Module](#4-domain-module)

---

**Namespace Convention Note:** For module files located in `modules/{ModuleName}/src/{Subdirectory}/{FileName}.php`, the namespace **must omit the `src` segment**. For instance, `Modules\{ModuleName}\{Subdirectory}`. This ensures a consistent and clean namespace structure across the modular application.

## 1. Core Module

The `Core` module serves as the foundational layer of the application, encapsulating essential architectural building blocks and shared resources critical for functionality and consistency across all other modules.

*   **Purpose:** To provide universal interfaces, abstract base classes, and fundamental architectural components that are globally required.
*   **Contents:**
    *   **Shared Interfaces (Contracts):** Defines contracts for inter-module communication, ensuring loose coupling and strict adherence to the [Interface-First Principle](architecture.md#3-inter-module-communication-the-golden-rule).
    *   **Abstract Base Classes:** Provides reusable abstract implementations for common patterns.
    *   **Architectural Utilities:** Includes core services, traits, or helpers that are universally applicable.
    *   **Optional:** Base implementations for Repositories and Entities, if the advanced workflow is utilized.
*   **Key Principle:** The `Core` module is designed to be highly stable and independent, with minimal dependencies on other modules. Changes here should be carefully considered due to their potential widespread impact on the entire application.

## 2. Shared Module

The `Shared` module is dedicated to housing concrete, reusable components and helpers that offer common functionalities not specific to any particular business domain. It promotes code reuse and reduces duplication across various parts of the application.

*   **Purpose:** To store generic, concrete implementations that can be utilized by multiple modules.
*   **Contents:**
    *   **Common Utilities:** General-purpose helper functions or classes that provide convenience functionalities without being tied to a specific business process.
    *   **Generic Blade Components:** Reusable UI components (e.g., buttons, alerts, form elements) built with **DaisyUI** that can be rendered consistently across different module views, adhering to [UI/UX Design Guidelines](../internal/uix-guidelines.md).
    *   **Reusable Traits:** Traits offering common behaviors to classes without requiring complex inheritance hierarchies.
*   **Key Principle:** Components within the `Shared` module should be highly cohesive and loosely coupled, designed for broad applicability across the application without introducing module-specific logic.

## 3. Support Module

The `Support` module specializes in handling integrations with external services and providing infrastructure-level utilities. It acts as an abstraction layer for third-party dependencies or system-level functionalities, ensuring that core business logic remains clean and decoupled from external complexities.

*   **Purpose:** To abstract external service integrations and provide specialized, infrastructure-level utilities.
*   **Contents:**
    *   **External Service Clients:** Integrations with APIs (e.g., payment gateways, email services, cloud storage).
    *   **Media Library Interactions:** Abstractions for handling file uploads, storage, and retrieval, potentially interfacing with services like AWS S3 or local storage.
    *   **Specialized Infrastructure Tools:** Utilities for logging, monitoring, or other system-level operations that are not part of a specific business domain.
    *   **Static Helper Classes:** Collections of static methods for common, non-object-oriented utility functions.
*   **Key Principle:** The `Support` module isolates external dependencies, making the application more resilient to changes in third-party services and easier to maintain or swap out integrations without impacting core business logic.

## 4. Domain Module

Domain modules are the heart of the modular monolith, each representing a distinct business domain within the Internara application. They encapsulate all business logic, data models, and presentation concerns related to a specific domain.

*   **Purpose:** To achieve high cohesion and low coupling by grouping all domain-specific functionalities into a self-contained unit. This promotes independent development, easier maintenance, and scalability for individual business areas.
*   **Contents:**
    *   **Models:** Eloquent models and their associated factories, migrations, and seeders.
    *   **Controllers:** Handle incoming HTTP requests and delegate tasks to services or actions.
    *   **Views:** Blade templates or Livewire components for rendering domain-specific UI.
    *   **Services:** Contains the core business logic, orchestrating interactions between models and other components.
    *   **Actions:** Small, single-purpose classes that encapsulate a specific action or use case.
    *   **Routes:** Define web and API endpoints for the domain.
    *   **Tests:** Unit and feature tests specific to the domain's functionality.
*   **Key Principle:** A domain module should be as independent as possible, communicating with other modules primarily through defined interfaces or events. Direct dependencies on other domain modules should be minimized and carefully managed.

**Example: User Domain Module**

The `User` module would encapsulate everything related to user management:

*   `Modules/User/src/Models/User.php`: The Eloquent User model.
*   `Modules/User/src/Http/Controllers/UserController.php`: Handles user-related requests (e.g., registration, profile management).
*   `Modules/User/src/Services/UserService.php`: Contains business logic for user operations (e.g., creating a new user, updating a profile).
*   `Modules/User/src/Actions/RegisterUser.php`: A specific action for user registration.
*   `Modules/User/resources/views/profile.blade.php`: User profile view.
*   `Modules/User/routes/web.php`: Defines routes like `/profile`, `/register`.
*   `Modules/User/tests/Feature/UserRegistrationTest.php`: Tests for user registration functionality.

This structure ensures that all aspects of user management are co-located, making the `User` domain module a self-sufficient unit.
