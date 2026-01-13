# Laravel Framework Integration

This document details Internara's specific implementation and conventions for leveraging the **Laravel Framework** as its core backend. Laravel provides the foundational structure, security, and tooling upon which the entire system is built. Our approach is uniquely tailored to support a **Modular Monolith** architecture, ensuring consistency and scalability. For a comprehensive understanding of Internara's modular architecture, refer to the **[Architecture Guide](../architecture-guide.md)**.

---

## 1. Laravel-Specific Architectural Patterns and Conventions

Our use of Laravel adheres to specific patterns and conventions to ensure code is organized, maintainable, and scalable within the Modular Monolith structure. For general development conventions across the project, refer to the **[Development Conventions Guide](../development-conventions.md)**.

*   **Modular Structure:** The application leverages `nwidart/laravel-modules` to divide into distinct business domains. Domain-specific logic resides within its respective module.
*   **Service-Oriented Logic:** Business logic is delegated to dedicated **Service Classes**; Controllers remain thin, focusing on HTTP requests.
*   **Eloquent ORM:** Used as the primary method for database interaction. Models are placed within each module's `src/Models` directory. Direct database queries (`DB::`) are discouraged.
*   **Artisan Console:** Used extensively for code generation (`make:*`, `module:make-*`), database management, and other tasks. Custom commands are defined within their respective modules.
*   **Validation:** All request validation **must** be handled by dedicated **Form Request classes**.
*   **Routing:** URLs are generated using **named routes** (`route('name')`).
*   **Configuration:** Access configuration values via the `config()` helper. The `env()` helper **must not** be used outside of the files in the `config/` directory.
*   **Queues:** Time-consuming tasks are offloaded to **queued jobs**.
