# Internara Project Documentation Overview

**Internara** is an internship management system built with **Laravel Modular MVC**. It is designed to centralize and streamline the management of internship programs, connecting students, supervisors, and host organizations within a unified platform.

The system prioritizes a **Modular Monolith** approach, ensuring that business rules are centralized in a service-oriented logic layer while maintaining a pragmatic separation of concerns.

This `docs` directory is the central repository for all Internara project documentation. It provides comprehensive information for developers, from high-level architectural concepts to detailed implementation guides and internal specifications.

---

**Table of Contents**

-   [Internara Project Documentation Overview](#internara-project-documentation-overview)
    -   [Project Description](#project-description)
    -   [1. Master Documentation (`docs/master/`)](#1-master-documentation-docsmaster)
    -   [2. Version-Specific Documentation (`docs/vx.x/`)](#2-version-specific-documentation-docsvxx)
    -   [3. Internal \& Specification Documents (`docs/master/internal/`)](#3-internal--specification-documents-docsmasterinternal)

---

## Project Description

Internara serves as a bridge for internship stakeholders. Key features include:

-   **Module-based isolation:** Clean separation of features (e.g., User, Internship, Profile).
-   **Service-oriented logic:** Business rules reside in dedicated Service classes.
-   **Modern Stack:** Powered by Laravel 12, Livewire 3, and Volt.

## 1. Master Documentation (`docs/master/`)

This directory contains the primary and authoritative documentation for developers working on the Internara application. It focuses on the core principles, development workflows, and essential tools.

## 2. Version-Specific Documentation (`docs/vx.x/`)

These directories (e.g., `docs/v1.0/`, `docs/v2.0/`) are reserved for version-specific documentation. As the project evolves, separate documentation sets can be maintained for different major versions. Each `docs/vx.x/` directory is expected to contain an `overview.md` file summarizing its contents.

## 3. Internal & Specification Documents (`docs/master/internal/`)

This directory houses the core planning and specification documents that define the Internara project's vision, requirements, and foundational design. These documents are crucial for understanding the "why" and "what" behind the application's existence and structure.
