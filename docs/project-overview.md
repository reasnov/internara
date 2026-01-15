# Project Overview

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

### 3.1. Main Documentation (`docs/main/`)

This directory contains the primary and authoritative documentation for developers working on the Internara application. It focuses on the core principles, development workflows, and essential tools.
For a comprehensive overview of all main documentation, see the **[Main Documentation Overview](main/main-documentation-overview.md)**.

### 3.2. Version-Specific Documentation (`docs/vx.x/`)

These directories (e.g., `docs/v1.0/`, `docs/v2.0/`) are reserved for version-specific documentation. As the project evolves, separate documentation sets can be maintained for different major versions. Each `docs/vx.x/` directory is expected to contain an `overview.md` file summarizing its contents, accessible via its respective **[Version Overview](versions/versions-overview.md)**.

### 3.3. Development Logs (`docs/logs/vx.x/`)

These directories (e.g., `docs/logs/v1.0/`, `docs/logs/v2.0/`) are reserved for development logs. Each `docs/logs/vx.x/` directory is expected to contain a series of markdown files documenting key development decisions, challenges, solutions, and progress for that version.

---

## 4. Documentation Writing Guide

To maintain the high quality, consistency, and developer-friendliness of Internara's documentation, please adhere to the following guidelines when writing or updating any documentation:

1.  **Single Source of Truth:**

    -   Each unique concept, principle, or detailed explanation must reside in **one authoritative document**.
    -   Avoid re-explaining content comprehensively in multiple places. Instead, provide a concise summary or a brief statement of the rule, followed by a **clear hyperlink** to the authoritative document for full details. This prevents inconsistencies and ensures maintainability.

2.  **Focus & Conciseness:**

    -   Each documentation file should have a **singular, clear focus**.
    -   Avoid unnecessary verbosity. Get straight to the point and provide value-driven information.

3.  **Developer-Friendly Language:**

    -   Use clear, direct, and actionable language. Assume the reader is a developer familiar with Laravel and related technologies, but avoid jargon where simpler terms suffice.
    -   Explain the _why_ behind a decision or convention, not just the _what_.

4.  **Extensive Cross-Referencing:**

    -   Utilize **hyperlinks extensively** to connect related topics and guide readers to more detailed explanations. This enhances navigation and reinforces the "single source of truth" principle.
    -   Example: `Refer to the **[Architecture Guide](main/architecture-guide.md)** for more details.`

5.  **English Only:**

    -   All documentation, including comments and explanatory text, **must be written entirely in English**.

6.  **Structured Formatting (Markdown):**

    -   Use Markdown for all documentation files (`.md`).
    -   Employ consistent heading levels (`#`, `##`, `###`) to structure content logically.
    -   Use code blocks (`php) for code examples, shell commands (`bash), and configuration snippets.
    -   Use lists (ordered/unordered), bolding, and italics to improve readability.
    -   Consider adding a "Table of Contents" for longer documents.

7.  **Code-Level Documentation (PHPDoc/DocBlocks):**
    -   For code files, use comprehensive PHPDoc/DocBlocks for every method and class. Focus these comments on the _purpose_ and _complex logic_ of the code, not merely restating what the code does.

By following these guidelines, we ensure that Internara's documentation remains a valuable, accessible, and up-to-date resource for all developers.

---

**Navigation**

[Next: Main Documentation Overview →](main/main-documentation-overview.md)