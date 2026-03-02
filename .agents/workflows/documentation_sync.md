# Workflow: Documentation-as-Code Synchronization

This workflow ensures that the system's technical documentation evolves in lockstep with the source code. It prevents "Documentation Decay" and ensures that technical artifacts remain a reliable source of truth.

---

## 1. Objective
Maintain 100% synchronization between implementation and documentation, ensuring architectural clarity and alignment with the **3S Doctrine** (Secure, Sustain, Scalable).

## 2. Execution Protocol
1.  **Change Discovery**: Identify implementations affecting public interfaces or data.
2.  **Artifact Update**: Revise root docs, module READMEs, and Wikis.
3.  **Audit**: Verify consistency between code comments and formal docs.
4.  **Reporting**: Generate the Documentation Sync report.

## 3. Workflow Phases

### Phase 1: Impact Analysis & Surface Discovery
- **Action**: Map code changes to relevant documentation artifacts.
- **Triggers**:
    - New Service Contracts or modified public APIs.
    - Changes to the Data Model or Privacy rules.
    - New UI Flows or Slot registrations.

### Phase 2: Authoritative Artifact Evolution
- **Action**: Update the core documents in `docs/`.
- **Targets**:
    - **SyRS/Roadmap**: Mark requirements as implemented and link PRs.
    - **Data Model**: Reflect schema changes and encryption status.
    - **Arch Design**: Update diagrams or logic flows if boundaries shifted.

### Phase 3: Module & PHPDoc Synchronization
- **Action**: Ensure localized documentation is accurate.
- **Checks**:
    - **README.md**: Update "Features" and "API Contracts" sections.
    - **Source**: Verify English PHPDocs match implementation reality.
    - **Local**: Sync translation keys if new UI strings were added.

---

## 4. Pull Request Report Template

```markdown
# Documentation Synchronization Report

## 1. Modified Artifacts
- **Core Docs**: [List]
- **Module READMEs**: [List]
- **Wiki/Guides**: [List]

## 2. Integrity Audit
- [X] PHPDocs match implementation.
- [X] Service Contracts synchronized.
- [X] Requirement Traceability maintained.
- [X] strictly English-only technical text.
```
