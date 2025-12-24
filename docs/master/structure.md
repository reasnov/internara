# Internara - Core, Shared, and Support Modules Overview

This document provides a detailed overview of the foundational `Core`, `Shared`, and `Support` modules. These modules define the structural hierarchy and portability standards of the Internara application.

---

## 1. Portability & Role Hierarchy

To maintain a clean modular monolith, we categorize foundational modules based on their relationship to the business logic and their requirement for portability.

| Module | Role | Portability |
| :--- | :--- | :--- |
| **Shared** | Universal Toolbox & Base Classes | **Mandatory Portable** (Wajib Portable) |
| **Support** | Infrastructure Utilities for Core | **Non-Portable** (Business-Specific Support) |
| **Core** | Base Architecture & Business Rules | **Non-Portable** (Business-Specific) |

---

## 2. Shared Module (The Portable Foundation)

The `Shared` module is the "toolbox" of the application. It contains components that are completely agnostic of Internara's specific business rules.

*   **Requirement:** Every file in this module **must** be able to function in a completely different project (e.g., a Fintech app or an E-commerce site) without modification.
*   **Contents:**
    *   **Universal Exceptions:** `AppException`, `RecordNotFoundException`.
    *   **General Utilities:** `UsernameGenerator`, `DateHelper`, `StringFormatter`.
    *   **Generic UI Components:** Pure DaisyUI/Tailwind wrappers (buttons, inputs) that don't assume any business context.
*   **Key Principle:** Modul bisnis (seperti `User`) hanya boleh bergantung pada modul `Shared` jika ingin tetap portable.

## 3. Core Module (The Business Architecture)

The `Core` module encapsulates the essential architectural building blocks that are **specific to the Internara business domain**.

*   **Purpose:** To provide universal interfaces and data that define *what* Internara is.
*   **Contents:**
    *   **Business Seeders:** `RoleSeeder` (Owner, Admin, Teacher, Student), `PermissionSeeder`.
    *   **Base Contracts:** Interfaces that define the communication standards between Internara modules.
    *   **Universal Business Logic:** Logic that applies to all modules but only within this specific application.
*   **Portability:** This module is **not portable**. It is the "glue" that binds the Internara system together.

## 4. Support Module (The Infrastructure Support)

The `Support` module handles specialized integrations and infrastructure-level utilities that support the `Core` architecture.

*   **Purpose:** To abstract complexity and provide tools for the `Core` and domain modules.
*   **Contents:**
    *   **Exception Handlers:** Logic for rendering `AppException` into specific UI responses.
    *   **External Clients:** Media library interactions, specialized logging, or monitoring tools.
*   **Portability:** This module is **non-portable** as it is designed specifically to support the Internara `Core`.

---

## 5. Domain Module (The Business Heart)

Domain modules (e.g., `User`, `Internship`) represent distinct business areas. 

*   **Standard:** They should strive to be **Portable** by only depending on the `Shared` module and external framework packages.
*   **Constraint:** They use the data provided by `Core` (like Roles/Permissions) via standard Laravel interfaces (Gates/Policies) to avoid hard-coding dependencies on the physical `Core` module.