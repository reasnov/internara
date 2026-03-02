# Workflow: Quality Engineering & Defect Remediation

This workflow defines the protocol for verifying system behavior and remediating defects. It adopts a "Test-First" philosophy to ensure that every fix or feature is backed by executable proof of quality.

---

## 1. Objective
Achieve 100% behavioral alignment between the implementation and the authoritative specifications, while maintaining a robust test suite in alignment with the **3S Doctrine** (Secure, Sustain, Scalable).

## 2. Execution Protocol
1.  **Analysis**: Identify expected behavior from `docs/`.
2.  **Reproduction**: Create a failing test case (The "Red" phase).
3.  **Remediation**: Implement the fix and verify (The "Green" phase).
4.  **Reporting**: Generate the Quality Verification report.

## 3. Workflow Phases

### Phase 1: Requirement-Driven Discovery
- **Action**: Study the SyRS or Blueprint artifacts related to the module.
- **Checks**:
    - Input invariants and validation rules.
    - Expected side effects (Events, Logs, DB state).
    - Authorization constraints.

### Phase 2: Defect Reproduction & POC
- **Action**: Isolate the issue and create a Proof of Concept (PoC) test.
- **Tasks**:
    - Run the existing suite to detect regressions.
    - **Add a new test case** that explicitly fails due to the reported bug.

### Phase 3: The Testing Pyramid Execution
- **Action**: Execute tests across all granularities.
- **Levels**:
    - **Unit**: Isolated service and domain logic.
    - **Feature**: End-to-end flows and Livewire state.
    - **Arch**: Architectural boundary verification.
    - **Robustness**: Edge-case audit (nulls, large inputs, guest access).

### Phase 4: Fix Implementation & Refactoring
- **Action**: Apply the minimal code change to pass the test.
- **Standards**:
    - Ensure fix is idiomatic and follows project conventions.
    - Refactor for clarity without changing behavior.
    - Maintain 100% pass rate for ALL existing tests.

---

## 4. Pull Request Report Template

```markdown
# Quality Verification Report: [Issue/Feature]

## 1. Root Cause Analysis
- **Defect Description**: [What went wrong?]
- **Traceability**: [SyRS-F-XXX]
- **Reproduction**: [Link to new test case]

## 2. Test Coverage
- [X] Unit Tests (Logic)
- [X] Feature Tests (Flow)
- [X] Arch Tests (Boundaries)
- [X] Edge Cases (Robustness)

## 3. Verification Evidence
- **Pass Rate**: 100%
- **Module(s)**: [List]
- **Regressions Checked**: [Yes]
```
