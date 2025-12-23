# Internara: Developer MVP Specification (v1.0.0)

This document provides the core technical specifications for Internara's Minimum Viable Product (MVP). It is designed to be an actionable guide for developers, focusing on architecture, features, and constraints.

---

**Table of Contents**

-   [1. Core Technical Architecture & Standards](#1-core-technical-architecture--standards)
-   [2. Key Performance & Quality Metrics](#2-key-performance--quality-metrics)
-   [3. MVP Feature Scope (v1.0.0)](#3-mvp-feature-scope-v100)
-   [4. User Roles & Stories](#4-user-roles--stories)
-   [5. Initial Development Roadmap](#5-initial-development-roadmap)
-   [6. Project Vision & Context](#6-project-vision--context)

---

## 1. Core Technical Architecture & Standards

### 1.1. Technology Stack

Internara is built on the **TALL Stack**:

-   **T**ailwind CSS (v4)
-   **A**lpine.js
-   **L**aravel (v12)
-   **L**ivewire (v3)

### 1.2. Architecture

-   **Pattern:** **Modular Monolith** using the `nwidart/laravel-modules` package.
-   **Guides:**
    -   [Architecture Guide](master/architecture.md)
    -   [Modular Monolith Developer Guide](master/modular-monolith.md)
    -   [Module Structure Overview](master/modules.md)

### 1.3. Key Architectural Rules

-   **Namespace Convention:** For files in `modules/{ModuleName}/src/{Subdirectory}/{FileName}.php`, the namespace **MUST** omit the `src` segment.
    -   **Example:** `Modules\{ModuleName}\{Subdirectory}`.
    -   **Reference:** [Development Conventions](master/conventions.md)
-   **Module Communication:** Interactions between modules **MUST** be explicit via service class interfaces or events to maintain loose coupling.

## 2. Key Performance & Quality Metrics

-   **Performance:** Core page load time must be **< 1.5 seconds**.
-   **Stability:** The entire internship lifecycle (registration to reporting) must be **100% free of critical bugs**.
    -   A **critical bug** is any issue causing data loss, security vulnerabilities, workflow blockage, or fatal application errors.
-   **UI/UX:**
    -   **Philosophy:** Minimalist and functional. Prioritize task completion over elaborate aesthetics.
    -   **Guidelines:** [UI/UX Design Guidelines](uix-guidelines.md).
    -   **Language:** Default user-facing language is **Indonesian (`id`)**.
-   **Error Handling:**
    -   **Frontend:** User errors must be clear, concise, and non-technical.
    -   **Backend:** All critical exceptions must be logged with detailed context.

## 3. MVP Feature Scope (v1.0.0)

Features are prioritized based on dependencies and core workflow sequence.

-   **P1 - Foundational:** Must be built first. These are blockers for all other development.
-   **P2 - Core Data:** Essential master data management required before workflows can be tested.
-   **P3 - Core Workflow:** The main user journey and primary value proposition of the application.

All features must be implemented using simple, efficient Livewire components.

### 3.1. Features to **EXCLUDE** (Hard Constraints)

To ensure rapid delivery, the following are strictly out of scope for v1.0.0:

-   Internal messaging/chat.
-   Automated notifications (email, push).
-   Complex analytics or reporting dashboards.
-   Automatic grading of assignments or journals.

### 3.2. Application Setup (Admin Only) [Priority: P1 - Foundational]

-   **Description:** A guided, one-time installation process. This is the first feature to be implemented.
-   **Components:**
    -   `setup:welcome`: Initial setup screen.
    -   `setup:account`: Configure the primary 'Owner' account.
    -   `setup:school`: Configure initial school data.
    -   `setup:department`: Configure initial department data.
    -   `setup:program`: Configure initial internship program data.
    -   `setup:complete`: Finalize setup and sync settings.

### 3.3. Data Management (Admin Only) [Priority: P2 - Core Data]

-   **Description:** CRUD operations for core application data. These must be in place before the internship lifecycle can function.
-   **Modules & Order:**
    1.  User Management
    2.  School Management
    3.  Department Management
    4.  Program Management
    5.  Placement Management
    6.  Assessment (`Aspects` & `Indicators`)
    7.  Evaluation

| Module         | Features                          | Deletion Constraints                                   |
| :------------- | :-------------------------------- | :----------------------------------------------------- |
| **User**       | CRUD, Role Assignment             | -                                                      |
| **School**     | CRUD                              | Cannot delete if `Departments` are associated.         |
| **Department** | CRUD                              | Cannot delete if `Programs` or `Users` are associated. |
| **Program**    | CRUD, Link to `Department`        | -                                                      |
| **Placement**  | CRUD, Link to `Department`        | -                                                      |
| **Assessment** | CRUD for `Aspects` & `Indicators` | -                                                      |

#### Data Validation Rules

| Entity                   | Field           | Rules                                      |
| :----------------------- | :-------------- | :----------------------------------------- |
| **User**                 | `email`         | `required`, `email`, `unique`              |
|                          | `username`      | `required`, `min:4`, `alpha_num`, `unique` |
|                          | `password`      | `required` (on create), `min:8`            |
| **School**               | `name`          | `required`, `min:3`                        |
| **Department**           | `name`          | `required`                                 |
|                          | `school_id`     | `required`, `exists:schools,id`            |
| **Program**              | `name`          | `required`                                 |
|                          | `start_date`    | `required`, `date`, `before:end_date`      |
|                          | `department_id` | `required`, `exists:departments,id`        |
| **Placement**            | `name`          | `required`                                 |
| **Assessment Aspect**    | `name`          | `required`, `unique`                       |
| **Assessment Indicator** | `name`          | `required`                                 |
|                          | `aspect_id`     | `required`, `exists:assessment_aspects,id` |
| **StudentReport**        | `student_id`    | `required`, `exists:users,id`              |
|                          | `teacher_id`    | `required`, `exists:users,id`              |
|                          | `placement_id`  | `required`, `exists:placements,id`         |
|                          | `score`         | `required`, `numeric`, `min:0`             |
|                          | `comment`       | `required`, `string`                       |
|                          | `notes`         | `nullable`, `string`                       |
|                          | `date`          | `required`, `date`                         |

### 3.4. Internship Lifecycle [Priority: P3 - Core Workflow]

This represents the end-to-end user flow and should be implemented in the following sequence:

1.  **Internship Registration (Student, Admin/Teacher)**
    -   Students select an available `Placement` filtered by their department.
    -   Admins/Teachers approve or reject registrations.
    -   **AC:** A "Computer Science" student can register for an approved tech company placement.
2.  **Journals & Assignments (Student, Teacher)**
    -   Students perform CRUD on their daily `Journals`.
    -   Students upload files for `Assignments`.
    -   Teachers view submitted work.
3.  **Student Assessment (Teacher)**
    -   Teachers view a list of students to assess.
    -   Teachers fill a form with `Assessment Indicators` (grouped by `Aspect`) and input a score (1-5) for each.
    -   **AC:** A teacher can assign a score of 4 for "Punctuality" and 5 for "Teamwork".
4.  **Student Report (Teacher, Student)**
    -   Teachers can perform CRUD (Create, Read, Update, Delete) operations on a `StudentReport`.
    -   This report is a manual document where teachers enter the final details of a student's internship.
    -   The form will include fields for `score`, `comment`, and `notes`.
    -   The final report is viewable as a simple web page or downloadable PDF.
    -   **Technical Note:** The PDF download can be implemented using a library like `barryvdh/laravel-dompdf`.
    -   **AC:** A teacher successfully creates and saves a report for a student. The student can then view their completed report.

## 4. User Roles & Stories

-   **Owner:** Manages application settings and top-level admin accounts.
-   **Administrator:** Manages all master data (users, schools, departments, assessments).
-   **Supervising Teacher:** Assesses students and creates final student reports.
-   **Student:** Registers for internships, submits work, and views their final report.

## 5. Initial Development Roadmap

-   **1. Develop Core Modules:** Plan and build the foundational modules for the application.
-   **2. Prepare `Program` Module:**
    -   Generate the module: `php artisan module:make Program`.
    -   Create `Program` model, migration (with `BIGINT` IDs), and factory.
    -   Implement `ProgramService` (Interface & concrete class).
    -   Add professional PHPDoc to all new/modified classes.
    -   Register interfaces with the `BindServiceProvider`.
    -   *(Optional) Implement Repository/Entity layers if complex logic demands it.*
-   **3. Prepare `Report` Module:**
    -   Generate the module: `php artisan module:make Report`.
    -   Create the `StudentReport` model and migration.
    -   Implement the Service and necessary Livewire components for CRUD operations.

## 6. Project Vision & Context

### Vision

To rapidly build a **minimalist, structured, and secure** web application for managing the entire school internship lifecycle, prioritizing core functions to deliver a functional MVP with minimal effort.

### Problem Summary

Vocational internship (PKL) programs lack a structured, integrated framework for governance. This leads to misaligned placements, poor tracking of student progress, subjective assessments, and fragmented communication. The result is a diminished learning experience and certifications that do not reliably signal student competency. This project aims to solve this by providing a centralized digital system for integrated planning, observation, and evaluation, creating a legitimate and transparent internship process.