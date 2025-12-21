# Internara

**Internara** is an open-source internship management system built with **Laravel 12**, **Livewire 3**, and a **Modular Monolith** architecture. It is designed to streamline the entire internship lifecycle, from registration and journal logging to final reporting and evaluation.

## Features

-   **Modular Architecture:** Built using `nwidart/laravel-modules` for a scalable and maintainable codebase.
-   **Modern Tech Stack:** Leveraging the **TALL Stack** (Tailwind CSS, Alpine.js, Laravel, Livewire) for a responsive and interactive user experience.
-   **Role-Based Access Control:** Secure access management for Students, Teachers, and Administrators using `spatie/laravel-permission`.
-   **Internship Lifecycle Management:**
    -   **Registration:** Students can browse and apply for available placements.
    -   **Journals:** Daily log entries for students to track their progress.
    -   **Assignments:** Teachers can assign tasks and review submissions.
    -   **Assessments:** Comprehensive evaluation system with customizable aspects and indicators.
    -   **Reporting:** Automated and manual reporting tools for final grades.
-   **Minimalist UI:** Designed with **DaisyUI** and **Iconify** for a clean, distraction-free interface.

## Requirements

-   PHP 8.2 or higher
-   Composer
-   Node.js & NPM
-   SQLite, MySQL, or PostgreSQL

## Installation

1.  **Clone the repository:**

    ```bash
    git clone https://github.com/reasnov/internara.git
    cd internara
    ```

2.  **Install PHP dependencies:**

    ```bash
    composer install
    ```

3.  **Install Node.js dependencies:**

    ```bash
    npm install
    ```

4.  **Configure environment:**

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

    Update your `.env` file with your database credentials.

5.  **Run migrations and seeders:**

    ```bash
    php artisan migrate --seed
    ```

6.  **Build assets:**

    ```bash
    npm run build
    ```

7.  **Start the development server:**
    ```bash
    php artisan serve
    ```

## Documentation

Comprehensive documentation for developers is available in the `docs/` directory:

-   [**Architecture Guide**](docs/master/architecture.md): Understand the modular structure.
-   [**Development Conventions**](docs/master/conventions.md): Coding standards and best practices.
-   [**Workflow Guide**](docs/master/workflow.md): Step-by-step guide for building new features.

## Contributing

Contributions are welcome! Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details on how to contribute to this project.

## License

This project is open-sourced software licensed under the [MIT license](LICENSE).

## Credits

-   **Author:** [Reas Vyn](https://github.com/reasnov)
-   **Email:** reasnov.official@gmail.com
