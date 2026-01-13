# Package Integration Overview

Internara leverages several key Laravel packages to achieve its **Modular Monolith** architecture and reactive UI. This directory documents the specific configurations, extensions, and usage patterns for these core dependencies.

---

## Core Packages

### 1. [Laravel/framework](laravel-framework.md)

The core backend framework providing the application's foundational structure, security, and tooling.

### 2. [Livewire/livewire](laravel-livewire.md)

The primary framework for building dynamic, reactive user interfaces with PHP.

### 3. [nwidart/laravel-modules](nwidart-laravel-modules.md)

The backbone of our modular architecture. It allows us to encapsulate business domains into self-contained modules under the `modules/` directory.

### 4. [mhmiton/laravel-modules-livewire](mhmiton-laravel-modules-livewire.md)

An extension that enables seamless discovery and usage of Livewire components within modules, supporting the `module::component` syntax.

### 5. [spatie/laravel-permission](spatie-laravel-permission.md)

Used for Role-Based Access Control (RBAC). We have refactored this into a portable `Permission` module with support for configurable ID types (UUID or Integer) and module-specific ownership.

### 6. [spatie/laravel-activitylog](spatie-laravel-activitylog.md)

Integrated for robust system monitoring and user activity logging, aligning with the `Log` module's functionality and supporting Internara's modular architecture.

---

## Implementation Philosophy

We do not use these packages "out of the box" in a standard way. Instead, we wrap or configure them to:

-   **Enforce Isolation:** Modules should not leak implementation details. All logic, from database migrations to UI components, remains within the module's directory.
-   **Support Portability:** Modules (especially `Permission`) are designed to be plug-and-play. They use "Runtime Configuration Injection" to configure their dependencies without requiring the developer to modify global application config files.
-   **Maintain Clean Namespaces:** Our custom configuration omits the `src` segment from namespaces for better readability and a more professional class structure.
-   **Zero-Manual-Setup:** A well-designed module should work immediately upon being enabled, handling its own service bindings and dependency overrides.
