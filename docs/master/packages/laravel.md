# Laravel Integration

Laravel serves as the core backend framework for the Internara application, providing the foundational structure, security, and tooling upon which the entire system is built. Our implementation of Laravel is tailored to support a **Modular Monolith** architecture.

---

## 1. Core Architectural Patterns

Our use of Laravel adheres to specific patterns to ensure code is organized, maintainable, and scalable.

*   **Modular Structure:** The application is not a standard Laravel project. Instead, it is divided into distinct business domains using the `nwidart/laravel-modules` package. All domain-specific logic, from models to controllers, resides within its respective module.

*   **Service-Oriented Logic:** Business logic is intentionally kept out of Controllers. Controllers are thin and responsible only for handling HTTP requests. All core business rules, data manipulation, and orchestration are delegated to dedicated **Service Classes**.

*   **Eloquent ORM:** We use Laravel's Eloquent ORM as the primary method for database interaction. Models are placed within each module's `src/Models` directory. Direct database queries (`DB::`) are discouraged in favor of Eloquent's expressive and secure methods.

*   **Artisan Console:** The Artisan console is used extensively for code generation (`make:*`, `module:make-*`), database management (`migrate`, `seed`), and other maintenance tasks. All custom commands are also defined within their respective modules.

---

## 2. Key Conventions

We follow a strict set of Laravel conventions to maintain consistency across the codebase.

*   **Validation:** All request validation **must** be handled by dedicated **Form Request classes**. No validation logic should be placed directly within controllers or services.

*   **Routing:** URLs are generated using **named routes** (`route('name')`) to decouple the application from a specific URL structure.

*   **Configuration:** Configuration values must be accessed via the `config()` helper. The `env()` helper **must not** be used outside of the files in the `config/` directory to ensure that configuration can be properly cached in production.

*   **Queues:** Time-consuming tasks are offloaded to **queued jobs** to ensure a responsive user experience.
