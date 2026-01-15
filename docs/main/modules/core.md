# Core Module

The `Core` module contains application-specific infrastructure, foundational configurations, and
custom development tooling. Unlike the `Shared` module, the `Core` module is tailored to Internara's
specific business model and architecture.

## Purpose

- **Infrastructure:** Manages project-wide settings and foundational services.
- **Tooling:** Provides custom Artisan commands to speed up development.
- **Identity:** Defines the technical identity of the application.

## Key Features

### 1. Development Tooling

- **Custom Generators:** Extended commands for creating modular classes, interfaces, and traits.
    - `php artisan module:make-class`
    - `php artisan module:make-interface`
    - `php artisan module:make-trait`

### 2. Application Identity

- **[App Info Command](../../main/artisan-commands-reference.md#1-application-app-commands)**:
  Displays application version, author, and environment details via `php artisan app:info`.

### 3. Core Functions

- **Setting Helper:** Provides a global `setting()` function to access application configuration
  with ease.

---

**Navigation** [← Back to Module TOC](table-of-contents.md)
