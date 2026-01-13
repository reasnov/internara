# Application Versions Overview

This document provides a central overview of all application versions and their respective documentation within the Internara project. Each major version (e.g., `v0.x`, `v1.x`) has its own dedicated documentation directory, containing specific details relevant to that version.

---

## Versioning Guidelines

Internara adheres to a modified semantic versioning approach, incorporating major, minor, and patch releases, along with pre-release and build metadata flags. This system ensures clarity and predictability in application development and deployment.

### 1. Version Components

*   **Major Version (X.y.z)**: Incremented for incompatible API changes, significant architectural overhauls, or major new feature sets that break backward compatibility. A major version increment resets the minor and patch versions to zero.
    *   **Example**: `1.0.0` to `2.0.0`

*   **Minor Version (x.Y.z)**: Incremented for new functionalities in a backward-compatible manner. This includes new features, significant improvements, or non-breaking API additions. A minor version increment resets the patch version to zero.
    *   **Example**: `1.0.0` to `1.1.0`, or `1.2.5` to `1.3.0`

*   **Patch Version (x.y.Z)**: Incremented for backward-compatible bug fixes and minor internal changes. These releases are intended to be drop-in replacements.
    *   **Example**: `1.0.0` to `1.0.1`, or `1.1.2` to `1.1.3`

### 2. Pre-release Identifiers

Pre-release versions can be denoted by appending a hyphen and a series of dot-separated identifiers immediately following the patch version. These indicate unstable or experimental releases.

*   **Alpha (`-alpha`)**: The initial phase of development, often unstable and feature-incomplete.
    *   **Example**: `1.0.0-alpha.1`

*   **Beta (`-beta`)**: Feature-complete but potentially unstable. Used for internal testing and early feedback.
    *   **Example**: `1.0.0-beta.2`

*   **Release Candidate (`-rc`)**: Potentially final release. Used for final testing before a stable release.
    *   **Example**: `1.0.0-rc.3`

### 3. Build Metadata

Build metadata can be appended to the version number by adding a plus sign (`+`) and a series of dot-separated identifiers immediately following the patch or pre-release version. This is used for build-specific information and does not affect version precedence.

*   **Example**: `1.0.0-alpha.1+001.build-123`

---

## Available Versions

*   **[v0.x-alpha Overview](v0.x-alpha.md)**: Details the initial alpha release features and scope.