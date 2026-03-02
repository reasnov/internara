# Workflow: Comprehensive Security & Privacy Audit

This workflow defines the authoritative protocol for auditing system security, data flow integrity, and privacy compliance. It aligns with ISO/IEC 27034, 29146, and 29100, focusing on defensive implementation and risk mitigation.

---

## 1. Objective
Identify, verify, and remediate vulnerabilities while ensuring that the system's security posture remains resilient against modern threat vectors, in alignment with the **3S Doctrine** (Secure, Sustain, Scalable).

## 2. Audit Phases

### Phase 1: Threat Modeling & Defensive Layer Analysis (PEP/PDP/PIP)
- **Action**: Map the defense-in-depth layers for the target domain.
- **Checks**:
    - **Presentation Layer (PEP)**: Verify Livewire/Controller validation rules and authentication middleware protection.
    - **Service Layer (PDP)**: Ensure mandatory `Gate::authorize()` calls exist for every state-altering operation. Verify that Policies implement "Explicit Deny by Default."
    - **Persistence Layer (PIP)**: Confirm that only UUID v4 is used for identifiers and that sensitive data is properly cast for encryption.

### Phase 2: RBAC & Identity Governance (ISO/IEC 29146)
- **Action**: Audit the Role-Based Access Control implementation.
- **Checks**:
    - **Role Taxonomy**: Verify that access is restricted according to the stakeholder roles (Student, Instructor, Staff, Industry Supervisor, Administrator).
    - **Permission Naming**: Ensure permissions follow the `{module}.{action}` convention (e.g., `attendance.report`).
    - **Ownership Context**: Verify that Policies enforce resource ownership (e.g., a student can only update their own profile).
    - **Least Privilege**: Check if any component or service has been granted excessive permissions beyond its domain function.

### Phase 3: Data Privacy & Encryption Integrity (ISO/IEC 29100)
- **Action**: Trace the lifecycle of Personally Identifiable Information (PII).
- **Checks**:
    - **Encryption at Rest**: Verify that PII fields (Name, Email, Phone, Identifiers) utilize the Eloquent `encrypted` cast.
    - **Identity Obfuscation**: Strictly enforce the UUID v4 invariant. Ensure no numeric IDs are leaked via URLs or responses.
    - **Logging Redaction**: Verify that `ActivityLog` and standard logs do not capture raw PII or secret keys. Check for automated redaction implementation.

### Phase 4: Vulnerability & SAST Audit (OWASP Top 10)
- **Action**: Static analysis for common injection and logic vulnerabilities.
- **Checks**:
    - **Injection**: Audit for `DB::raw`, `shell_exec`, or unsanitized dynamic inclusions.
    - **Mass-Assignment**: Ensure `$fillable` is restrictive. No use of `$guarded = []`.
    - **State Transitions**: Verify that status changes (via `HasStatus`) cannot be bypassed or triggered out of sequence.
    - **Dependency Audit (SBOM)**: Check for known vulnerabilities in upstream packages using tools like `composer audit`.

---

## 3. Execution Protocol for the Agent

1.  **Baseline Review**: Analyze `docs/security-architecture.md`, `docs/vulnerability-management.md`, and `docs/engineering-standards.md`.
2.  **Reproduction (PoC)**: Before applying a fix, create a failing automated test (Proof of Concept) that demonstrates the security failure.
3.  **Severity Assessment**: Assign a preliminary **CVSS Score** to every identified vulnerability.
4.  **Surgical Remediation**: Apply the fix ensuring it adheres to the 3S Doctrine.
5.  **Security Regression Prevention**: Add a permanent security test case to the module suite to prevent the vulnerability from re-emerging.
6.  **Final Verification**: Run the full module test suite and architectural audit.

---

## 4. Pull Request Report Template

```markdown
# Security Audit & Remediation Report: [Module]

## 1. Executive Risk Summary
- **Risk Score**: [Critical / High / Medium / Low]
- **CVSS Vector**: [e.g., AV:N/AC:L/PR:N/UI:N/S:U/C:H/I:H/A:H]
- **Audited Components**: [List]

## 2. Findings & Remediation
| ID | Vulnerability | Standard | Impact | Fix Applied |
|:---|:---|:---|:---|:---|
| S-01 | Missing PDP | ISO 29146 | Escalation | Added `Gate::authorize` |
| P-01 | Plaintext PII | ISO 29100 | Exposure | Applied `encrypted` cast |

## 3. Compliance Verification
- [X] PEP/PDP/PIP Layer Alignment
- [X] UUID v4 Invariant Maintained
- [X] PII Encryption & Redaction Verified
- [X] Activity Audit Trails Enabled

## 4. V&V Evidence
- **PoC Link**: [Link to reproduction test]
- **Remediation Test**: [Link to security test case]
- **Pass Rate**: 100%
```
