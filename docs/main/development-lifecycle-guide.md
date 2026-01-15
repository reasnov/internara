# Development Lifecycle & Versioning Strategy Guide

This document is the **authoritative standard** for versioning and documenting releases within the
Internara project. It enforces a **Lifecycle Documentation Standard**, ensuring every version
document captures the complete engineering context: **Pre-Production** (Planning), **Production**
(Execution), and **Post-Production** (Verification).

Strict adherence to this guide is mandatory to maintain historical clarity and architectural
integrity.

---

## 1. Versioning Guidelines

Internara uses a hybrid approach combining Semantic Versioning (SemVer) with a descriptive Series
Code.

### 1.1. Semantic Versioning (SemVer)

We follow the standard `MAJOR.MINOR.PATCH` format:

- **`MAJOR`**: Incompatible API changes or architectural shifts.
- **`MINOR`**: New, backward-compatible functionality.
- **`PATCH`**: Backward-compatible bug fixes.

### 1.2. Series Code

Each development cycle is identified by a `SERIES-CODE`.

- **Format:** `{codename}-{stage}-{scope}`
- **Example:** `ARC01-ALPHA-FND`

#### Components:

- **`{codename}`**: Unique abstract identifier (e.g., `ARC01`, `CYGNUS2`).
- **`{stage}`**: `ALPHA`, `BETA`, `RC`, `STABLE`.
- **`{scope}`**:
    - `INIT`: Initiation & Setup.
    - `FND`: Foundational (architecture, core setup).
    - `FEAT`: Feature-driven.
    - `RFT`: Refactor.
    - `SEC`: Security.

---

## 2. The Lifecycle Documentation Standard

Every version document (e.g., `v0.1.x.md`) must be structured to reflect the engineering lifecycle.
Do not simply list features; tell the story of **Why**, **How**, and **Proof**.

### Documentation Maintenance Principle

**Prioritize Updates Over Creation:** Always prioritize updating existing documentation files to
reflect changes or new features rather than creating new ones. New documentation files should only
be introduced for entirely new domains, major architectural shifts, or distinct technical topics
that cannot be logically integrated into existing guides. This prevents information fragmentation
and ensures a "single source of truth" for each topic.

---

### Phase 1: Pre-Production (The Planning Context)

This section defines the "Rules of Engagement" established _before_ code was written.

#### Section: `Goals & Architectural Philosophy`

- **Purpose:** To explain the _problems_ necessitating this version and the _strategies_ chosen to
  solve them.
- **What to Write:**
    - **Problem Keypoints:** Bullet points describing the pain points (e.g., "Codebase is becoming
      coupled," "Setup takes too long").
    - **Architectural Pillars:** The high-level concepts used as solutions (e.g., "Service-Oriented
      Architecture," "TALL Stack").
- **Detailed Explanation:** Don't just say "We used Modular Monolith." Explain _why_ (e.g., "To
  enforce strict boundaries between domains.").

#### Section: `Architectural Constraints` (Crucial)

- **Purpose:** To define the immutable boundaries set to prevent scope creep and architectural
  decay.
- **What to Write:** Explicit "Must" and "Must Not" rules.
- **Examples:**
    - "No business logic is allowed in Controllers."
    - "Modules must not import concrete classes from other modules; use Interfaces only."
    - "The `app/` directory must remain empty except for providers."

---

### Phase 2: Production (The Execution)

This section details the _actual work_ performed. Depending on the release type (`INIT` vs `FEAT`),
use one of the formats below.

#### Option A: `Scope of Work` (For Setup/Refactor/Init)

Use this for versions focused on infrastructure, tooling, or refactoring (`INIT`, `RFT`).

- **Format:** Categorized checklists of concrete tasks.
- **Detail Level:** High. Mention specific packages, configurations, and commands used.
- **Example:**
    - **Environment Setup:** "Install Laravel v12, Configure SQLite."
    - **Tooling:** "Install Pest, Configure Pint with strict preset."

#### Option B: `System & Feature Keystones` (For Features/Foundation)

Use this for versions delivering tangible value or modules (`FEAT`, `FND`).

- **Format:** Group related work into logical "Keystones".
- **Required Sub-Sections per Keystone:**
    1.  **Goal:** The user-centric objective (e.g., "To manage user roles").
    2.  **Implementation:**
        - **Approach:** Technical strategy (e.g., "Uses `spatie/laravel-permission` with UUID
          support").
        - **Analysis:** Verification of code placement (e.g., "Models located in `Modules/User`").
    3.  **Developer Impact:** How this helps the team (e.g., "Reduces boilerplate for auth checks").

---

### Phase 3: Post-Production (Verification & Future)

This section provides the _proof_ of quality and the bridge to the next version.

#### Section: `Quality Assurance & Verification`

- **Purpose:** To prove that the implementation works and meets the project's high standards.
- **What to Write:**
    - **Tooling Setup:** Confirm that testing (Pest) and linting (Pint) tools are active.
    - **Verification Checklist:** A list of specific checks performed to validate the release (e.g.,
      "Run `php artisan test`: All Green", "Verify storage link functionality").

#### Section: `Security Issues`

- **Purpose:** To document known vulnerabilities addressed or discovered.
- **What to Write:** Severity, Impact, and Description of any security-related findings. If none,
  state "No critical issues identified."

#### Section: `Documentation Checks`

- **Purpose:** To ensure that all architectural changes, new features, and technical conventions
  introduced in this version are accurately reflected in the project's documentation.
- **What to Write:**
    - Verification that new features have corresponding documentation in `docs/main/`.
    - Confirmation that all cross-module communication or shared utilities are documented.
    - Ensuring the Version History and TOCs are updated.

#### Section: `Roadmap & Next Steps`

- **Purpose:** To define the transition to the next version.
- **What to Write:** A bulleted list of high-level objectives for the immediate next release (e.g.,
  "Implement User Module," "Setup Shared Utilities").

---

## 3. Standard Document Template

Copy and paste this template for every new version document.

```markdown
# Overview: Version vX.X.X (Codename)

## 1. Version Details

- **Name**:
- **Series Code**:
- **Status**:
- **Description**:

---

## 2. Goals & Architectural Philosophy (Pre-Production)

### Problem Keypoints

- [ ] ...

### Architectural Pillars

- [ ] ...

### Architectural Constraints (The Rules)

_Immutable rules defined for this version._

1.  ...
2.  ...

---

## 3. Scope of Work / Keystones (Production)

_(Choose Option A or B based on release type)_

### Option A: Scope of Work

- [ ] Task 1
- [ ] Task 2

### Option B: System Keystones

#### Keystone 1: Title

- **Goal**:
- **Implementation**:
- **Developer Impact**:

---

## 4. Quality Assurance & Finalization (Post-Production)

### 4.1. Quality Tooling Setup

- [ ] ...

### 4.2. Verification Checklist

- [ ] ...

### 4.3. Documentation Checks

- [ ] New features documented in `docs/main/`.
- [ ] All technical conventions updated.
- [ ] Root TOC and Module TOCs updated.

### 4.4. Security Issues

- ...

### 4.5. Roadmap & Next Steps

- ...
```

---

**Navigation**

[← Previous: Versions Overview](../versions/versions-overview.md)
