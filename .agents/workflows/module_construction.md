# Workflow: Standardized Module Construction

This workflow defines the protocol for creating new domain modules within the Internara ecosystem. It ensures that every module is built with a consistent structure, adheres to modular monolith invariants, and passes all architectural quality gates.

---

## 1. Objective
Construct a new domain module that is logically isolated, contract-driven, and fully integrated into the system's service-oriented architecture, satisfying the **3S Doctrine** (Secure, Sustain, Scalable).

## 2. Execution Protocol
1.  **Preparation**: Ensure the Design Blueprint (BP) is approved.
2.  **Scaffolding**: Initialize directory structure and configuration.
3.  **Iterative Build**: Follow the TDD cycle (Red-Green-Refactor).
4.  **Reporting**: Generate the Module Construction report.

## 3. Workflow Phases

### Phase 1: Scaffolding & Structural Integrity
- **Action**: Initialize the module directory following the canonical structure.
- **Components**:
    - `src/` (Models, Services, Providers, Livewire).
    - `database/` (Migrations, Seeders, Factories).
    - `lang/`, `config/`, `resources/`.
- **Mapping**: Register PSR-4 mapping and Service Providers.

### Phase 2: Domain Persistence & Privacy
- **Action**: Implement foundational Eloquent models and migrations.
- **Invariants**:
    - Use `HasUuid` trait for all primary keys.
    - Apply `encrypted` casts for PII fields.
    - Define strict `$fillable` attributes.
    - No cross-module physical foreign keys.

### Phase 3: Service Layer & Contract Fulfillment
- **Action**: Implement business logic and inter-module contracts following the "Service-Oriented Logic Execution" philosophy.
- **Tasks**:
    - **Contract-First**: Define the Service Contract (Interface) in `src/Services/Contracts/` before implementation.
    - **Implementation**: Extend `BaseService` (for orchestration) or `EloquentQuery` (for data-centric logic).
    - **Security Policy**: Create a dedicated **Policy class** for each Model. Implement the "Deny by Default" principle and map permissions to actions.
    - **Defense**: Integrate `Gate::authorize()` for all state-changing operations.
    - **Atomicity**: Ensure multi-record mutations are wrapped in database transactions.
    - **Events**: Dispatch Domain Events (past tense) for significant state changes to enable asynchronous cross-module decoupling.

### Phase 4: Integration & Boundary Governance
- **Action**: Connect the module to the ecosystem while preserving strict isolation.
- **Tasks**:
    - **UI Composition**: Register UI Slots via `SlotRegistry`. Never call foreign Livewire components directly.
    - **RBAC Baseline**: Define and seed granular permissions (e.g., `{module}.manage`).
    - **Localization**: All user-facing strings, including exceptions and flash messages, MUST resolve via modular translation keys.
    - **Dependency Audit**: Verify that no direct instantiation of foreign Models occurs within this module.

### Phase 5: Verification & Architectural Audit
- **Action**: Execute the full test suite and quality gates.
- **Requirements**:
    - Behavioral Unit/Feature tests.
    - Architectural Audit (enforce modular isolation).
    - Code style formatting (`Laravel Pint`).

---

## 4. Pull Request Report Template

```markdown
# Module Construction Report: [Module Name]

## 1. Overview
- **Requirement Mapping**: [SyRS-F-XXX]
- **Design Reference**: [BP-XXX-XXX]
- **Scope**: [Description]

## 2. Modular Invariants
- [X] UUID-only Primary Keys
- [X] Service Contracts Implemented
- [X] No Physical Cross-Module Keys
- [X] Strictly Typed Interfaces

## 3. Security & Privacy
- [X] Policy Applied (Deny by Default)
- [X] PII Encryption Verified
- [X] Permissions Synchronized

## 4. V&V Evidence
- **Pass Rate**: 100%
- **Arch Audit**: PASS
- **Style Gate**: PASS
```
