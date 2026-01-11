# Internara - Conceptual Best Practices Guide

This guide provides a high-level overview of the core conceptual best practices and foundational principles governing development on the Internara project. These are the overarching ideas that developers should always keep in mind.

---

## 1. Core Architecture: Modular Monolith

Internara is built as a **Modular Monolith**, where distinct business domains are encapsulated in self-contained **Modules**. This promotes organization, maintainability, and scalability.

-   **Layered Structure:** Each module adheres to a pragmatic layered architecture:

    -   **UI Layer (Livewire):** Handles user interaction and presentation. Components should be **embeddable** and reusable.
    -   **Business Logic Layer (Services):** Orchestrates domain-specific business rules.
    -   **Data Layer (Models):** Represents persisted data and database interactions via Eloquent.
    -   **Optional Layers (Repositories/Entities):** Used only when explicit architectural boundaries or complex integrations are required.

-   **Interface-First Communication:** Communication between modules **MUST** happen through shared interfaces (contracts) or standard Laravel features (Gates/Policies) to ensure loose coupling. Avoid direct referencing of concrete classes or models between modules.

-   **Namespace Convention:** Module files located in `modules/{ModuleName}/src/` **must omit** the `src` segment in their namespace (e.g., `Modules\{ModuleName}\{Subdirectory}`).

-   **Portability Standards:**
    -   **Shared Module:** Must be strictly universal and portable.
    -   **Domain Modules:** Should strive for portability by only depending on **Shared** and external packages.
    -   **Core/Support Modules:** Can be business-specific and non-portable.

---

## 2. Development & Coding Principles

-   **English Only:** All code, comments, and documentation must be in English.
-   **Descriptive Naming:** Use clear and descriptive names for all code elements.
-   **DRY Principle:** Promote code reuse and avoid unnecessary duplication.
-   **Strict Adherence to Structure:** Follow existing directory structures and conventions; avoid creating new base folders without approval.
-   **Comprehensive PHPDoc:** Document all classes and methods thoroughly, explaining _why_ complex logic exists.
-   **PHP & Laravel Standards:**
    -   Always use curly braces for control structures.
    -   Utilize PHP 8 constructor property promotion.
    -   Enforce explicit return type declarations and parameter type hints.
    -   Use `php artisan make:` commands for scaffolding.
    -   Leverage Eloquent ORM with eager loading and `casts()` methods.
    -   Always use Form Request classes for validation.
    -   Use queued jobs for time-consuming operations.
    -   Utilize Laravel's built-in authentication and authorization.
    -   Access config via `config()`, never `env()` outside config files.

---

## 3. Testing Philosophy

-   **Test First (When Practical):** Prioritize writing tests with new features.
-   **Comprehensive Coverage:** All new features/bug fixes require relevant tests.
-   **No Test Deletion:** Existing tests are critical and must not be removed.
-   **Pest Framework:** All tests **must** be written using Pest. Follow Arrange, Act, Assert pattern. Use `uses(Tests\TestCase::class);` for Laravel helpers.

---

## 4. UI/UX Design Principles

-   **Minimalist & Functional:** Prioritize a clean, uncluttered aesthetic focused on task completion.
-   **User-Friendly & Professional:** Intuitive interfaces conveying trust and competence.
-   **Monochrome Theme:** Strict monochrome color scheme with sparing use of accent/state colors.
-   **Consistent Design:** Adhere to defined typography, layout, spacing, and iconography guidelines.
-   **DaisyUI Framework:** All new UI development **must** leverage DaisyUI components for consistency.

---

## 5. Vision & Technical Goals

-   **Primary Goal:** Rapidly build a **minimalist, structured, and secure** Full-Cycle Internship MVP.
-   **Technology Stack:** Built on the **TALL Stack** (Tailwind CSS v4, Alpine.js, Laravel v12, Livewire v3).
-   **Performance Target:** Core page load time of **less than 1.5 seconds**.
-   **Stability Target:** Critical bugs in the full internship cycle must be **100% free** before internal testing.
-   **Default Localization Language:** Indonesian (`id`).