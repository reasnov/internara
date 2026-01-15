# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and this project
adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [v0.4.x-alpha] - In Progress

### Added

- New roadmap for the **Institutional & Academic Phase** (`ARC01-INST`).
- Unified codebase formatting with **Prettier** and **Laravel Pint**.
- Automated documentation formatting script (`npm run format`).

---

## [v0.3.0-alpha] - 2026-01-15 (ARC01-USER)

### Added

- **Administrative User Management:** Full CRUD interface with role-based adaptive forms.
- **Specialized Profiles:** Polymorphic-like relations for **Student** (NISN) and **Teacher** (NIP).
- **Email Verification:** Mandatory verification for Student and Teacher roles to secure the
  platform.
- **Role-Based Redirection:** Intelligent post-login redirection based on user roles.
- **Institution-Centric Notifications:** Customized email verification and welcome templates
  sounding from the school/brand.
- **Contextual Onboarding:** Integrated welcome notifications for new accounts.
- **Scaffolding Refinement:** Enhanced `module:make-class` generator with `--interface` support.

### Fixed

- **Security Hardening:** Remediated Authentication Bypass in email verification.
- **Access Control:** Fixed IDOR vulnerabilities in User Management via explicit Policy enforcement.
- **Privacy:** Implemented email masking in application logs to prevent PII leaks.
- **Infrastructure:** Mass-fix of namespace inconsistencies in School, Department, and Internship
  modules.
- **Localization:** Repaired invalid JSON structure in `id.json` translation file.

---

## [v0.2.0-alpha] - 2026-01-10 (ARC01-CORE)

### Added

- **RBAC Infrastructure:** Full integration of `spatie/laravel-permission` within a modular context.
- **Shared Traits:** Foundational traits for **UUID** support and **Status** management.
- **EloquentQuery Service:** Base service for standardized CRUD and query orchestration.
- **Custom Generators:** Added `module:make-class`, `module:make-interface`, and
  `module:make-trait`.

---

## [v0.1.1-alpha] - 2026-01-01 (ARC01-INIT)

### Added

- **Project Genesis:** Initial environment setup with Laravel 12 and PHP 8.4.
- **Modular Monolith:** Implementation of `nwidart/laravel-modules` with a custom `src/` structure.
- **TALL Stack Foundation:** Integration of Tailwind CSS v4, Alpine.js, and Livewire v3.
- **UI Module:** Centralized design system using **DaisyUI** and **MaryUI**.
- **Quality Tooling:** Initial configuration for **Pest PHP** and **Laravel Pint**.

[v0.4.x-alpha]: https://github.com/reasnov/internara/compare/v0.3.0-alpha...main
[v0.3.0-alpha]: https://github.com/reasnov/internara/compare/v0.2.0-alpha...v0.3.0-alpha
[v0.2.0-alpha]: https://github.com/reasnov/internara/compare/v0.1.1-alpha...v0.2.0-alpha
[v0.1.1-alpha]: https://github.com/reasnov/internara/releases/tag/v0.1.1-alpha
