# Application Versions Overview

This document outlines the versioning strategy and documentation standards for the Internara project. Adhering to these guidelines ensures clarity, consistency, and predictability across all development cycles.

**Internara** is an open-source internship management system built with a modern Laravel tech stack, including **Livewire 3** and a **Modular Monolith** architecture. Its primary goal is to streamline the entire internship lifecycle, from student registration and daily journal logging to final assessments and reporting.

---

## Versioning Guidelines

Internara uses a hybrid approach that combines Semantic Versioning (SemVer) for technical compatibility with a unique Series Code for identifying the goals and stage of each release.

### 1.1. Semantic Versioning (SemVer)

We follow the standard `MAJOR.MINOR.PATCH` format:

-   **`MAJOR`**: Incremented for incompatible API changes or major architectural shifts.
-   **`MINOR`**: Incremented for new, backward-compatible functionality.
-   **`PATCH`**: Incremented for backward-compatible bug fixes.

### 1.2. Series Code

Each development cycle is identified by a `SERIES-CODE` that provides immediate context about its purpose.

-   **Format:** `{codename}-{stage}-{scope}`
-   **Example (for v0.1.x):** `ARC01-ALPHA-FND`

#### Components:

-   **`{codename}`**: A unique, abstract identifier for a release cycle (e.g., `ARC01`, `CYGNUS2`).
-   **`{stage}`**: The current development stage: `ALPHA`, `BETA`, `RC`, or `STABLE`.
-   **`{scope}`**: A short code for the primary focus:
    -   `FND`: Foundational (architecture, core setup).
    -   `FEAT`: Feature-driven (new user-facing capabilities).
    -   `RFT`: Refactor (codebase improvements).
    -   `SEC`: Security (addressing vulnerabilities).

---

## Version Documentation Guidelines

Each version must have a corresponding document in this directory (e.g., `v0.1.x-alpha.md`) that follows a narrative-driven format. The goal is to explain the "why" behind decisions, not just list changes.

The document must contain the following sections:

### 2.1. Overview & Version Details

-   **Title:** State the version and its thematic name (e.g., `Overview: Version v0.1.x-alpha (Foundational)`).
-   **Version Details:** A list including `Name`, `Series Code`, `Status`, and a `Description`.

### 2.2. Goals & Architectural Philosophy

-   **Problem Keypoints:** A bulleted list of the core problems this version solves.
-   **Architectural Pillars/Philosophy:** The strategies used to solve the problems.

### 2.3. System & Feature Keystones

The main body, grouping features into logical "Keystones". Each Keystone must include:

-   **`Goal`:** The objective of the system.
-   **`Implementation`:**
    -   `Approach`: The technical strategy.
    -   `Analysis Reports`: A summary of code verification.
    -   `Security and Testing`: Security and testing strategies.
-   **`Developer Impact`:** The tangible benefit for developers.

### 2.4. Security Issues

Detail any identified security vulnerabilities, their impact, severity, and remediation.

### 2.5. Improvement Suggestions

Capture potential improvements or refactoring opportunities.

---

## Available Versions

-   **[v0.1.x-alpha pre-release (ARC01-ALPHA-FND)](v0.1.x-alpha.md)** _(current version (unstable))_: Details the initial alpha release features and scope.
