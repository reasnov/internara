# Gemini Guidelines: Internara Project

This document outlines the core principles, project context, and operational guidelines for the AI assistant (“Gemini”) when working on the Internara project. These guidelines ensure consistency, architectural clarity, high code quality, and efficient collaboration.

---

## Project Overview

**Internara** is an internship management system built with **Laravel Modular Monolith (Modular MVC)**, where business rules are centralized in a **service-oriented business logic layer**.

The architecture prioritizes clarity, maintainability, and pragmatic separation of concerns by leveraging Laravel’s native MVC conventions while intentionally avoiding unnecessary abstraction layers such as standalone Entities, Repositories, or internal DTOs unless explicitly justified by clear boundary or integration requirements.

---

## Interaction Principles

As an AI assistant, you operate under the following identity and interaction principles.

### Interaction Guidelines

-   **Aesthetic-Natural Tone**
    Maintain a calm, adaptive, and structured tone focused on clarity, efficiency, and cognitive ease.

-   **Keypoints Summary**
    Always conclude interactions with a concise, scannable summary of key information, decisions, and outcomes.

-   **Privacy First**
    Never store, write, or engage with personal or sensitive information. Actively refuse such requests.

---

## Core Architecture Philosophy

-   The project adopts **Modular Monolith**, not strict Domain-Driven Design or Clean Architecture.
-   **Eloquent Models** represent persisted domain data and may contain limited, domain-relevant behavior.
-   **Services** are the primary holders of business logic and orchestration.
-   **Controllers and Livewire components remain thin**, focusing only on request handling and UI concerns.
-   Abstractions such as Repositories, Entities, or internal DTOs are **not introduced by default** and must be explicitly justified by concrete architectural needs (e.g. external integrations, async boundaries, or storage substitution).

---

## Project-Specific Workflow & Principles

These directives guide all technical workflows for the Internara project. For detailed architectural and development documentation, always refer to the project’s `/docs` directory.

### Core Workflow Principles

-   **Planning First**
    Always formulate a detailed plan, present it to the user, and obtain explicit approval before starting any implementation.

-   **English Only**
    All code, documentation, comments, and communication must be written entirely in **English**.

-   **Professional PHPDoc**
    Every class and method must include concise and professional PHPDoc in English. Every method and function must have a PHPDoc that clearly describes its intent, parameters, and return values.

-   **Mandatory Documentation**
    Every new feature, standardized pattern, or significant technical change must be accompanied by comprehensive documentation in the `docs/` directory. Documentation is a primary artifact and a prerequisite for task completion.

-   **Cross-check Project Documentation**
    Always cross-reference and adhere to existing project documentation (especially within the `/docs` directory). For comprehensive navigation, prioritize:
    - [docs/table-of-contents.md](docs/table-of-contents.md)
    - [docs/main/main-documentation-overview.md](docs/main/main-documentation-overview.md)
    - [docs/main/development-conventions.md](docs/main/development-conventions.md)
    - [docs/versions/versions-overview.md](docs/versions/versions-overview.md)
    - [docs/versions/v0.1.x-alpha.md](docs/versions/v0.1.x-alpha.md)

---

## Standard Project Workflow

1. **Receive User Input**
   Carefully analyze the request, identifying explicit instructions, goals, and implied context.

2. **Study Instructions**
   Review all provided guidelines, project documentation, and task-specific requirements.

3. **Build Knowledge**
   Use available tools (`search-docs`, `glob`, `read_file`, `codebase_investigator`) to understand the codebase and relevant context.

4. **Formulate Task Plan**
   Create a clear, step-by-step plan aligned with project architecture and best practices.

5. **Request User Approval**
   Present the plan and wait for explicit approval before implementation.

6. **Execute Approved Tasks**
   Implement strictly according to the approved plan, project conventions, and coding standards.

7. **Report and Conclude**
   Provide a concise Keypoints Summary outlining actions taken, decisions made, and outcomes achieved.

8. **Commit and Push All Changes (Mandatory)**
   When instructed to commit and push all, ensure *all* changes in the codebase (including those not directly made by Gemini) are staged and committed. Craft professional commit messages by thoroughly reviewing *all actual* changes in the codebase.

---

## Foundational Technical Context

### Environment & Stack
-   **PHP Version:** 8.4.1
-   **Laravel Framework:** v12.43.1
-   **Database:** SQLite (Initiation Phase)
-   **Frontend:** TALL Stack (Tailwind CSS v4.1.18, Alpine.js, Laravel, Livewire v3.7.3)
-   **Component Library:** DaisyUI + MaryUI
-   **Architecture:** Modular Monolith using `nwidart/laravel-modules`
-   **Active Application Version:** `v0.2.x-alpha` (`ARC01-CORE`) - Core & Shared Systems Phase

### Current Version Constraints (v0.1.x-alpha)
-   **No "App" Logic:** The `app/` directory must remain minimal. All business and framework logic must reside in `modules/`.
-   **No Business Features:** Do not create "Users", "Schools", or "Internships" models/features yet. Focus on building the capability and infrastructure (UI, Exceptions, Shared, etc.).

### Versioning & Documentation Policy
-   **'x' Notation:** Version notations using 'x' (e.g., `v0.1.x`, `v1.x`) signify compatibility with the entire sequence within that parent version.
-   **Single Source of Truth:** Do not create new documentation files for every patch or minor release within a sequence. Use the parent documentation file (e.g., `docs/versions/v0.1.x-alpha.md`) to maintain a single, comprehensive history for that version series.

### Namespace Convention
Namespaces **must omit the `src` segment**:
- *Correct:* `namespace Modules\User\Services;`
- *Location:* `modules/User/src/Services/UserService.php`

---

## Technical Conventions

### Service Layer
-   Most services should extend `Modules\Shared\Services\EloquentQuery`.
-   Services must implement an interface (contract) that extends `Modules\Shared\Services\Contracts\EloquentQuery`.
-   Initialize the associated Model in the constructor using `$this->setModel(new YourModel())`.

### Inter-Module Communication
-   **Synchronous:** Use **Interfaces** (Contracts). Type-hint the interface, not the concrete class.
-   **Asynchronous:** Use **Events**.
-   **Isolation:** Modules MUST NOT directly reference concrete classes or internal models of other modules.

### UI & Component Preference
1.  Existing **UI Module** Components (`<x-ui::... />`).
2.  **MaryUI** Components.
3.  New Custom Component in **UI Module**.

### Cross-Module UI Injection (Slot System)
-   Use `@slotRender('slot.name')` in Blade to allow other modules to inject UI elements.
-   Register components to slots via `SlotRegistry::register()` in Service Providers.

### Exception Handling
-   Use `Modules\Exception\AppException` for domain errors.
-   Use translation keys: `{module_name}::exceptions.{key_name}`.

### Testing & Formatting
-   **Framework:** All tests must be written using **Pest**.
-   **Pint:** Always run `vendor/bin/pint --dirty` before finalizing changes.

---

## Leveraging Laravel Boost Tools

Use the integrated Laravel Boost tools to support efficient development:
- **Artisan Commands:** `list-artisan-commands`
- **URL Generation:** `get-absolute-url`
- **Debugging:** `tinker`, `database-query`, `browser-logs`, `last_error`.
- **Documentation Search:** Prioritize `search-docs` for Laravel-ecosystem docs. Use `search_file_content` or `glob` for project-local docs under `/docs`.