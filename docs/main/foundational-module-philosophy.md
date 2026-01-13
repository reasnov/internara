# Foundational Module Philosophy

This document provides a detailed overview of the foundational `Core`, `Shared`, `Support`, and `UI` modules. These modules define the structural hierarchy and portability standards that govern the Internara application.

---

## 1. Portability & Role Hierarchy

To maintain a clean modular monolith, we categorize foundational modules based on their relationship to the business logic and their requirement for portability.

| Module      | Role                               | Portability                                  |
| :---------- | :--------------------------------- | :------------------------------------------- |
| **Core**    | Base Architecture & Business Rules | **Non-Portable** (Business-Specific)         |
| **UI**      | UI Foundation & Assets             | **Non-Portable** (Business-Specific UI)      |
| **Support** | Infrastructure Utilities for Core  | **Non-Portable** (Business-Specific Support) |
| **Shared**  | Universal Toolbox & Base Classes   | **Mandatory Portable** (Strictly Mandatory)  |

---

## 2. Shared Module (The Portable Foundation)

The `Shared` module is the "toolbox" of the application. It contains components that are completely agnostic of Internara's specific business rules.

-   **Requirement:** Every component in this module must be portable enough to function in a completely different project without modification.
-   **Key Principle:** Business modules (such as `User`) should only depend on the `Shared` module if they aim to remain portable.

## 3. Core Module (The Business Architecture)

The `Core` module encapsulates the essential architectural building blocks and business rules that are **specific to the Internara business domain**.

-   **Purpose:** It provides the foundational data and contracts that define _what_ Internara is, serving as the central "glue" that binds the system together.
-   **Portability:** This module is **not portable** as it is intrinsically tied to the application's business logic.

## 4. Support Module (The Infrastructure Support)

The `Support` module handles specialized integrations and abstracts away infrastructure-level complexity.

-   **Purpose:** To provide tools and abstract complex processes for the `Core` and domain modules to consume, keeping them clean from infrastructure-specific code.
-   **Portability:** This module is **non-portable** as it is designed specifically to support the Internara `Core` architecture.

## 5. UI Module (The Frontend Foundation)

The `UI` module is the definitive source of truth for all frontend assets and standards within the Internara application.

-   **Purpose:** To encapsulate the application's visual identity, including all core styling, JavaScript, global Blade/Livewire components, and frontend build configurations.
-   **Portability:** This module is **non-portable** as it defines the specific look, feel, and brand identity of the Internara application.

---

## 6. Domain Module (The Business Heart)

Domain modules (e.g., `User`, `Internship`) represent distinct business areas.

-   **Standard:** They should strive to be **Portable** by only depending on the `Shared` module and external framework packages.
-   **Constraint:** They use the data provided by `Core` (like Roles/Permissions) via standard Laravel interfaces (Gates/Policies) to avoid hard-coding dependencies on the physical `Core` module. For details, refer to the **[Role and Permission Management Guide](../role-permission-management.md)**.
