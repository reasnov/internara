# UI Module Overview

The `UI` module is the central repository for all shared frontend assets, global Blade components, and Livewire UI elements within Internara. it serves as the project's internal design system, ensuring visual consistency and accelerating development.

## Purpose

-   **Consistency:** Maintains a unified look and feel across all modules (Shared, Core, and Domain).
-   **Reusability:** Provides a library of battle-tested components, reducing code duplication.
-   **Efficiency:** Allows developers to build complex interfaces quickly by leveraging existing UI blocks.
-   **Centralization:** Acts as the single source of truth for global layouts, CSS, and interactive assets.

---

## Technical Stack

The UI module is built upon the **TALL Stack** with several key additions:
-   **Tailwind CSS v4:** Utility-first styling.
-   **DaisyUI:** Semantic component classes.
-   **MaryUI:** Enhanced Livewire components.
-   **Iconify:** Universal icon framework.

---

## Core Strategy

Internara's UI strategy follows a specific preference hierarchy for components:
1.  **Project-Specific Components:** Components tailored for Internara, located in `modules/UI/resources/views/components`.
2.  **MaryUI Components:** High-level wrappers for DaisyUI.
3.  **DaisyUI Components:** Raw semantic components for custom building blocks.

---

## Directory Structure

-   `resources/css/`: Global stylesheets and Tailwind entry points.
-   `resources/js/`: Shared JavaScript logic and Alpine.js integrations.
-   `resources/views/components/`: Global Blade components.
-   `resources/views/layouts/`: Primary application layouts (App, Guest).
-   `src/View/Components/`: Class-based Blade components.

---

**Navigation**

[← Back to TOC](table-of-contents.md) | [Next: Layouts →](components/layouts.md)
