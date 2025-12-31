```markdown
# Gemini Guidelines: Internara Project

This document outlines the core principles, project context, and operational guidelines for the AI assistant (“Gemini”) when working on the Internara project. These guidelines ensure consistency, architectural clarity, high code quality, and efficient collaboration.

---

## Project Overview

**Internara** is an internship management system built with **Laravel Modular MVC**, where business rules are centralized in a **service-oriented business logic layer**.

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

-   The project adopts **Modular MVC**, not strict Domain-Driven Design or Clean Architecture.
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

-   **English Code Only**
    All code must be written in English.

-   **Professional PHPDoc**
    Every class and method must include concise and professional PHPDoc, focusing on _why_ complex logic exists.

-   **Cross-check Project Documentation**
    Always cross-reference and adhere to existing project documentation (especially within the `/docs` directory) to ensure consistency and alignment with established patterns, conventions, and architectural decisions.

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

---

## Foundational Technical Context

The Internara project is built with the following core technologies:

-   **PHP Version:** 8.4.1
-   **Laravel Framework:** v12
-   **Livewire:** v3
-   **Volt:** v1
-   **Architecture:** Modular Monolith using `nwidart/laravel-modules`

### Namespace Convention

For files located at:
```

modules/{ModuleName}/src/{Subdirectory}/{FileName}.php

```

Namespaces **must omit the `src` segment**:

```

namespace Modules\\{ModuleName}\\{Subdirectory};

```

---

## General Development Practices

- **Adherence to Existing Code**
  Always analyze and follow existing conventions, structure, naming, and patterns used in sibling files.

- **Naming Conventions**
  Use clear, descriptive, and intention-revealing names for classes, methods, and variables.

- **UUID Usage**
  The `User` model uses UUIDs as its primary key.

---

## Livewire & Volt Conventions

- **Thin Components (UI Focus)**
  Livewire components must remain UI-focused and must not contain business logic. All business operations must be delegated to the appropriate Service layer.

- **Component Embedding**
  When embedding Livewire components within Blade views, use:
```

@livewire('module-alias::component-dot-notation-name')

```
or
```

<x-livewire:module-alias::component-dot-notation-name />

```

- **State Management**
Livewire component state resides primarily on the server.

- **Event Dispatching**
Use `$this->dispatch()` for emitting events between Livewire components.

---

## Testing Conventions

- **Pest Exclusivity**
All tests must be written using the Pest testing framework.

- **Module-Specific Tests**
Generate tests using:
```

php artisan module:make-test <TestName> <ModuleName> [--feature]

```
Omit the `--feature` flag to generate unit tests.

---

## Code Formatting

- **Pint Enforcement**
Before finalizing any code changes, always run:
```

vendor/bin/pint --dirty

```
to ensure compliance with project coding standards.

---

## Leveraging Laravel Boost Tools

Use the integrated Laravel Boost tools to support efficient development:

- **Artisan Commands:** `list-artisan-commands`
- **URL Generation:** `get-absolute-url`
- **Debugging:**
- Application code: `tinker`
- Read-only database access: `database-query`
- Frontend diagnostics: `browser-logs`
- Backend error inspection: `last_error`
- **Documentation Search:**
Prioritize `search-docs` for version-specific Laravel documentation. Use `search_file_content` or `glob` for project-local documentation under `/docs`.


```
