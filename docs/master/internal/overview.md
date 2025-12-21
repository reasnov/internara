# Internara - Internal Documentation Hub

This directory contains the core planning and specification documents for the **Internara** project. It serves as the primary starting point for developers to understand the project's architecture, requirements, and technical standards.

Internara is a minimalist and secure web application designed to manage the school internship lifecycle. Its core architecture is a **Modular Monolith** built on **Laravel** and **Livewire**. The frontend is powered by **Tailwind CSS** with the **DaisyUI** component library and **Iconify** for icons. The project structure is organized into independent modules using the `nwidart/laravel-modules` package.

The primary goal is to rapidly develop a functional Minimum Viable Product (MVP) that covers the full internship cycle, from registration to final reporting.

---

## Document Index

This section provides a developer-focused overview of each key document.

*   **[Internal Documentation Hub](overview.md)**
    *   **Purpose:** You are here. A summary and index of all internal planning files.

*   **[Application Specification](internara-spec.md)**
    *   **Purpose:** The primary technical specification. Defines the MVP feature scope, user roles, data models, and core architectural rules. **Start here.**

*   **[Entity-Relationship Diagram (ERD)](internara-erd.md)**
    *   **Purpose:** A visual (Mermaid) diagram and detailed schema for the project's database. Use this as a reference for data structures.

*   **[UI/UX Design Guidelines](uix-guidelines.md)**
    *   **Purpose:** The technical guide for all frontend development. Defines the **DaisyUI** theme, color palette, component standards, and **Iconify** integration. All UI work must adhere to this guide.
