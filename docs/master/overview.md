# Internara - Master Documentation Overview

This directory (`docs/master/`) contains the primary documentation for developers working on the Internara project. These documents cover core architectural principles, development workflows, and available tools.

---

**Table of Contents**

-   **[Architecture Guide](architecture.md)**: Provides a high-level, developer-friendly guide to Internara's modular architecture, explaining the core concepts, layers (UI, Services, Repositories, Entities), and module communication best practices.
-   **[Module Structure Overview](structure.md)**: Provides an overview of the Core, Shared, Support, and Domain modules, detailing their purpose and contents.
-   **[Best Practices Guide](best-practices.md)**: A conceptual overview of core architectural principles, development conventions, testing philosophy, and UI/UX design.
-   **[Development Conventions](conventions.md)**: Outlines the coding and development conventions for the project, ensuring consistency and maintainability, including important namespace conventions for modular development.
-   **[Workflow Developer Guide](workflow.md)**: A practical, step-by-step guide for creating new features within a module, including concrete Artisan commands and code examples for Models, Entities, Repositories, Services, and UI components. It clarifies the distinction and interaction between Eloquent Models and Domain Entities.
-   **[Testing Guide](testing.md)**: A comprehensive guide covering the project's testing philosophy, Pest framework usage, test directory structure, writing tests (including `uses()` helper for Laravel functionalities), and running tests for both application and module levels.
-   **[Artisan Commands Reference](command.md)**: A comprehensive list of all available Artisan commands within the Internara application, categorized for easy reference.
-   **[Exception Handling Guide](exception.md)**: Details the philosophy, key classes, and best practices for managing exceptions consistently across the application.
