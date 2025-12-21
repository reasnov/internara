# Internara - Custom Module Generator Guide

This document outlines the strategies for customizing module generation within the Internara project. While `laravel-modules` provides flexible configuration options, advanced use cases may require a custom generator command.

---

**Table of Contents**

1.  [Overview](#1-overview)
2.  [Strategy 1: Configuration & Stubs (Current Approach)](#2-strategy-1-configuration--stubs-current-approach)
3.  [Strategy 2: The Base Module Pattern (Advanced)](#3-strategy-2-the-base-module-pattern-advanced)

---

## 1. Overview

Standardizing the structure of new modules is crucial for the **Modular Monolith** architecture. Internara uses a modified directory structure (using `src/` instead of `app/`) to align with package development standards. We achieve this primarily through configuration, but developers should be aware of advanced generation techniques.

## 2. Strategy 1: Configuration & Stubs (Current Approach)

This is the primary method used in Internara. We leverage the native capabilities of `nwidart/laravel-modules` to adjust paths and scaffold files.

### 2.1 Configuration

The `config/modules.php` file controls the default generation paths. Key customizations include:

*   **`app_folder`**: Set to `src/` to house domain logic.
*   **`generator`**: Customized to map default layers (Controllers, Models, etc.) to the `src/` directory.

```php
// config/modules.php
'paths' => [
    'app_folder' => 'src/',
    'generator' => [
        'model' => ['path' => 'src/Models', 'generate' => false],
        'controller' => ['path' => 'src/Http/Controllers', 'generate' => false],
        // ...
    ],
],
```

### 2.2 Stubs

We publish and modify stubs to ensure generated files follow our strict **Strict Typing** and **PHPDoc** conventions.

*   **Location:** `stubs/modules/*.stub`
*   **Usage:** The generator automatically uses these files when running `php artisan module:make`.

## 3. Strategy 2: The Base Module Pattern (Advanced)

For highly specific module structures that exceed the capabilities of the default configuration (e.g., pre-creating specific `README.md`, `LICENSE`, or complex directory trees), the **Base Module** pattern is recommended.

### 3.1 Concept

Instead of generating a module from scratch using stubs, we maintain a "Template Module" (or Base Module) that contains the exact desired structure. Creating a new module becomes a process of **Cloning** this template and **Renaming** the contents.

### 3.2 Implementation Steps

1.  **Create a Base Module:**
    Create a folder `stubs/base-module` that looks exactly like a finished module. Use placeholders for names.
    *   `composer.json`: ` "name": "internara/{module}"`
    *   `src/Providers/{Module}ServiceProvider.php`

2.  **Create a Custom Command:**
    Generate a command like `make:module-custom`.

    ```bash
    php artisan make:command MakeCustomModule
    ```

3.  **Command Logic:**
    The command should perform the following actions:
    *   **Copy:** Copy `stubs/base-module` to `modules/{TargetName}`.
    *   **Replace:** Recursively search and replace placeholders (e.g., `{Module}`, `{module}`, `{MODULE_NAMESPACE}`) in file names and contents.
    *   **Rename:** Rename specific files like `Service.php` to `TargetService.php`.

### 3.3 Recommended Placeholders

When creating a Base Module, use distinct placeholders to handle casing:

| Placeholder | Description | Example (Target: UserProfile) |
| :--- | :--- | :--- |
| `{Module}` | StudlyCase name | `UserProfile` |
| `{module}` | Lowercase name | `userprofile` |
| `{module-}` | Kebab-case name | `user-profile` |
| `{module_}` | Snake_case name | `user_profile` |

### 3.4 Example Command Logic Snippet

```php
// app/Console/Commands/MakeCustomModule.php

protected function handle()
{
    $name = $this->argument('name');
    $source = base_path('stubs/base-module');
    $destination = base_path("modules/$name");

    // 1. Copy
    File::copyDirectory($source, $destination);

    // 2. Replace Content
    $files = File::allFiles($destination);
    foreach ($files as $file) {
        $content = File::get($file);
        $content = str_replace('{Module}', $name, $content);
        $content = str_replace('{module}', strtolower($name), $content);
        File::put($file, $content);
    }

    // 3. Rename Files
    // ... logic to rename files like {Module}ServiceProvider.php
}
```

This strategy ensures that every new module starts with a perfect, project-compliant structure without manual adjustment.
