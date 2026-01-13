# Main Documentation Overview

This document serves as the central entry point and comprehensive table of contents for the primary developer documentation within the Internara project. It provides direct links to key guides covering architectural principles, development workflows, coding conventions, and available tools.

---

**Table of Contents**

-   **[Architecture Guide](architecture.md)**: Provides a high-level, developer-friendly guide to Internara's modular architecture, explaining the core concepts, layers (UI, Services, Repositories, Entities), and module communication best practices.
-   **[Service Binding & Auto-Discovery](service-binding-auto-discovery.md)**: Detailed documentation on the automated dependency injection system, configuration, caching, and manual overrides.
-   **[Module Structure Overview](foundational-module-philosophy.md)**: Provides an overview of the Core, Shared, Support, and Domain modules, detailing their purpose and contents.
-   **[Best Practices Guide](conceptual-best-practices.md)**: A conceptual overview of core architectural principles, development conventions, testing philosophy.
-   **[UI/UX Development Guide](ui-ux-development-guide.md)**: Comprehensive guide detailing UI/UX principles, standards, and technical specifications for frontend implementation.
-   **[Development Conventions](development-conventions.md)**: Outlines the coding and development conventions for the project, ensuring consistency and maintainability, including important namespace conventions for modular development.
-   **[Role & Permission Management Guide](role-permission-management.md)**: The primary guide for creating, managing, and using Roles and Permissions, detailing the conventions and separation of concerns between modules.
-   **[Workflow Developer Guide](modular-monolith-workflow.md)**: A practical, step-by-step guide for creating new features within a module, including concrete Artisan commands and code examples for Models, Entities, Repositories, Services, and UI components. It clarifies the distinction and interaction between Eloquent Models and Domain Entities.
-   **[Testing Guide](testing.md)**: A comprehensive guide covering the project's testing philosophy, Pest framework usage, test directory structure, writing tests (including `uses()` helper for Laravel functionalities), and running tests for both application and module levels.
-   **[Artisan Commands Reference](artisan-commands-reference.md)**: A comprehensive list of all available Artisan commands within the Internara application, categorized for easy reference.
-   **[Installation Guide](installation.md)**: Provides step-by-step instructions to set up the Internara application for local development.
-   **[Exception Handling Guide](exception-handling-guidelines.md)**: Details the philosophy, key classes, and best practices for managing exceptions consistently across the application.
-   **[Advanced Developer Guides](advanced/overview.md)**: Strategies for advanced customization, including custom module generation and base module patterns.
