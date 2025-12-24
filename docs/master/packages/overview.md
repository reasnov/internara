# Package Integration Overview

Internara leverages several key Laravel packages to achieve its **Modular Monolith** architecture and reactive UI. This directory documents the specific configurations, extensions, and usage patterns for these core dependencies.

---

## Core Packages

### 1. [nwidart/laravel-modules](nwidart-laravel-modules.md)
The backbone of our modular architecture. It allows us to encapsulate business domains into self-contained modules under the `modules/` directory.

### 2. [mhmiton/laravel-modules-livewire](mhmiton-laravel-modules-livewire.md)
An extension that enables seamless discovery and usage of Livewire components within modules, supporting the `module::component` syntax.

### 3. [spatie/laravel-permission](spatie-laravel-permission.md)
Used for Role-Based Access Control (RBAC). We have refactored this into a portable `Permission` module with support for configurable ID types (UUID or Integer) and module-specific ownership.

---

## Implementation Philosophy

We do not use these packages "out of the box" in a standard way. Instead, we wrap or configure them to:
- **Enforce Isolation:** Modules should not leak implementation details.
- **Support Portability:** Modules (especially `Permission`) are designed to be plug-and-play.
- **Maintain Clean Namespaces:** Our custom configuration omits the `src` segment from namespaces for better readability.
