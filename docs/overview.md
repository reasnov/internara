# Internara Project Documentation Overview

**Internara** is an internship management system built with a **Modular Monolith** architecture using Laravel. It is designed to centralize and streamline the management of internship programs, connecting students, supervisors, and host organizations within a unified platform.

This `docs` directory is the central repository for all Internara project documentation. It provides comprehensive information for developers, from high-level architectural concepts to detailed implementation guides.

---

## 1. Project Vision & Purpose

### Vision

To rapidly build a **minimalist, structured, and secure** web application for managing the entire school internship lifecycle, prioritizing core functions to deliver a functional MVP with minimal effort.

### Problem Summary

Vocational internship (PKL) programs often lack a structured, integrated framework for governance. This leads to misaligned placements, poor tracking of student progress, subjective assessments, and fragmented communication. The result is a diminished learning experience and certifications that do not reliably signal student competency.

This project aims to solve this by providing a centralized digital system for integrated planning, observation, and evaluation, creating a legitimate and transparent internship process.

---

## 2. Core Principles & Technology

### Core Technology

Internara is built on a modern, robust, and PHP-native technology stack, chosen for its development speed and maintainability.

-   **Technology:** The **TALL Stack**
    -   **T**ailwind CSS (v4)
    -   **A**lpine.js
    -   **L**aravel (v12)
    -   **L**ivewire (v3)
-   **UI Components:** **DaisyUI** and **MaryUI** are used to ensure a consistent and professional user interface.

### Architectural Pillars

The application is engineered for scalability and long-term maintainability.

-   **Pattern:** **Modular Monolith** using the `nwidart/laravel-modules` package. Each business domain (e.g., Users, Internships) is encapsulated in its own isolated module.
-   **Communication:** Interaction between modules is strictly controlled via service class **interfaces (Contracts)** or **events** to ensure loose coupling and prevent dependencies.
-   **Separation of Concerns:** A strict layered architecture is enforced within each module:
    1.  **UI Layer (Livewire):** Handles presentation and user input.
    2.  **Service Layer:** Contains all business logic and orchestration.
    3.  **Data Layer (Eloquent):** Manages database interaction.

---

## 3. Documentation Structure

### 3.1. Master Documentation (`docs/master/`)

This directory contains the primary and authoritative documentation for developers working on the Internara application. It focuses on the core principles, development workflows, and essential tools.

### 3.2. Version-Specific Documentation (`docs/vx.x/`)

These directories (e.g., `docs/v1.0/`, `docs/v2.0/`) are reserved for version-specific documentation. As the project evolves, separate documentation sets can be maintained for different major versions. Each `docs/vx.x/` directory is expected to contain an `overview.md` file summarizing its contents.