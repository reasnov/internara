# Workflow: Blueprint Development & Architectural Design

This workflow defines the protocol for designing technical solutions based on functional requirements. It ensures that every implementation is guided by a blueprint that addresses a specific **Functional Scope** (e.g., Identity, Evaluation, Setup) while maintaining modular monolith invariants.

---

## 1. Objective
Produce high-fidelity Design Blueprints (BP) that define data structures, service contracts, and interaction flows for a specific **Functional Domain**, ensuring alignment with the **3S Doctrine** (Secure, Sustain, Scalable).

## 2. Execution Protocol
1.  **Requirement Review**: Analyze the target SyRS ID and identify the functional requirements.
2.  **Functional Scope Identification**: Determine the conceptual domain of the blueprint (e.g., `ID` for Identity, `SYS` for System, `EVAL` for Evaluation).
3.  **Module Mapping**: Identify which technical modules will collaborate to fulfill this functional scope.
4.  **Drafting**: Create the blueprint using the standardized template in `docs/blueprints/`.
5.  **Reporting**: Generate the Architectural Design report focusing on the functional goal and implementation impact.

## 3. Workflow Phases

### Phase 1: Functional Domain & Persistence Design
- **Action**: Define the data entities and persistence invariants required for the functional scope.
- **Invariants**:
    - **Identity**: Strictly UUID v4 for all primary and foreign keys.
    - **Privacy**: Identify fields for `encrypted` cast (PII) regardless of which module they reside in.
    - **Data Integrity**: Enforce rules at the service layer to prevent cross-module physical keys.

### Phase 2: Orchestration & Service Specification
- **Action**: Design the interfaces and orchestration logic to fulfill the feature.
- **Standards**:
    - Define public methods in Service Contracts.
    - Use strict typing for all parameters and return types.
    - Ensure contracts return lightweight DTOs or Models to prevent deep implementation leakage.

### Phase 3: Interaction Mapping & Logic Flow
- **Action**: Map the sequence of operations required by the functional requirement.
- **Path**: UI -> Orchestration/Service Layer -> Persistence -> Domain Events.
- **Security**: Identify explicit `Gate::authorize()` points across the involved modules.

### Phase 4: Blueprint Formalization
- **Action**: Create the BP file in `docs/blueprints/` using `BP-[SCOPE_CODE]-[ID]`.
- **Naming Examples**:
    - `BP-ID-XXX`: Identity & Profile management.
    - `BP-SYS-XXX`: System core and installation.
    - `BP-EVAL-XXX`: Rubrics and competency evaluation.
- **Requirements**:
    - Link to SyRS ID.
    - Define functional Acceptance Criteria (AC).
    - Specify cross-module Verification & Validation (V&V) steps.

---

## 4. Pull Request Report Template

```markdown
# Architectural Design Report: [Functional Scope Name]

## 1. Design Artifacts
- **Blueprint ID**: [BP-SCOPE-XXX] (e.g., BP-ID-F201)
- **Functional Scope**: [e.g., Identity / Assessment / System]
- **Requirement Mapping**: [SyRS-F-XXX]

## 2. Implementation Impact
- **Involved Modules**: [List of modules that will implement this blueprint]
- **New/Modified Models**: [List]
- **Service Contracts**: [List new interfaces]

## 3. Modular Integrity & Compliance
- [X] UUID v4 Enforcement.
- [X] Contract-driven communication.
- [X] PII encryption rules defined for the scope.
- [X] No cross-module physical keys.
```
