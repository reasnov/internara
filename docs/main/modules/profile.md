# Profile Module

The `Profile` module handles extended user information and role-specific data. it utilizes a
polymorphic relationship to store details that are not required for core authentication but are
essential for the application's business logic.

## Purpose

- **Information Management:** Stores PII like phone numbers, addresses, and bios.
- **Polymorphism:** Adapts fields based on user roles (e.g., NIP for Teachers, NISN for Students).
- **Self-Service:** Empowers users to manage their own presence and security settings.

## Key Features

### 1. Unified Profile

- A single entry point for users to manage basic info and avatar.

### 2. Specialized Fields

- **Teacher Data**: Field for Employee ID (NIP).
- **Student Data**: Field for National Student ID (NISN).

### 3. Security Settings

- Self-service password updates and session management.

---

**Navigation** [← Back to Module TOC](table-of-contents.md)
