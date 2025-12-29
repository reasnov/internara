# Internara - Developer UI/UX Guide

This guide provides the core UI/UX principles and technical standards for developers working on the Internara application. Its purpose is to ensure a consistent, modern, and high-quality user experience.

---

## 1. Core Principles

-   **Minimalist & Modern:** Prioritize a clean, uncluttered aesthetic. Focus on essential functionality.
-   **User-Friendly:** The interface must be intuitive and easy to navigate for all user roles.
-   **Consistent:** Adhere to the guidelines and components defined here to maintain a cohesive look and feel.

---

## 2. UI Framework: DaisyUI + Tailwind CSS

-   **Primary Library:** Internara uses **[DaisyUI](https://daisyui.com/)**, a component library for **[Tailwind CSS](https://tailwindcss.com/)**.
-   **Secondary Library (MaryUI):** We also leverage **[MaryUI](https://mary-ui.com/)** for specialized Laravel/Livewire components that build upon DaisyUI, providing enhanced functionality and developer experience.
-   **Mandatory Use:** All new UI development **must** use DaisyUI components and utility classes. Do not introduce custom one-off styles or new component libraries.
-   **Rationale:** DaisyUI provides a robust set of pre-built, themeable components (`btn`, `card`, `modal`, etc.) that speed up development and ensure visual consistency. MaryUI extends this with Livewire-ready components.

---

## 3. Theming & Color Palette

We use DaisyUI's theme system to manage colors for both light and dark modes. The default themes are `light` and `dark`.

### 3.1. Semantic Colors

Use DaisyUI's semantic color classes instead of hard-coded color utilities. This ensures that elements adapt correctly to theming.

| Class Name  | Role                   | Example Usage                                 |
| :---------- | :--------------------- | :-------------------------------------------- |
| `primary`   | Primary actions, links | `<button class="btn btn-primary">`            |
| `secondary` | Secondary actions      | `<button class="btn btn-secondary">`          |
| `accent`    | Emphasis, highlights   | `<progress class="progress progress-accent">` |
| `success`   | Success states, alerts | `<div class="alert alert-success">`           |
| `warning`   | Warning states         | `<div class="alert alert-warning">`           |
| `error`     | Error states, danger   | `<div class="alert alert-error">`             |
| `info`      | Informational messages | `<div class="alert alert-info">`              |

### 3.2. Theme Configuration

The following configuration in `tailwind.config.js` defines our application's color scheme. The `light` theme is monochrome with an emerald accent, and `dark` follows a similar pattern.

```css
// resources/css/app.css
@import 'tailwindcss';

@plugin 'daisyui';

@plugin "daisyui/theme" {
  name: "light";
  default: true;
  prefersdark: false;
  color-scheme: "light";
  --color-base-100: oklch(100% 0 0);
  --color-base-200: oklch(97% 0 0);
  --color-base-300: oklch(92% 0 0);
  --color-base-content: oklch(14% 0 0);
  --color-primary: oklch(20% 0 0);
  --color-primary-content: oklch(98% 0 0);
  --color-secondary: oklch(20% 0 0);
  --color-secondary-content: oklch(98% 0 0);
  --color-accent: oklch(69% 0.17 162.48);
  --color-accent-content: oklch(26% 0.051 172.552);
  --color-neutral: oklch(20% 0 0);
  --color-neutral-content: oklch(97% 0 0);
  --color-info: oklch(74% 0.16 232.661);
  --color-info-content: oklch(29% 0.066 243.157);
  --color-success: oklch(76% 0.177 163.223);
  --color-success-content: oklch(37% 0.077 168.94);
  --color-warning: oklch(82% 0.189 84.429);
  --color-warning-content: oklch(41% 0.112 45.904);
  --color-error: oklch(71% 0.194 13.428);
  --color-error-content: oklch(27% 0.105 12.094);
  --radius-selector: 0.5rem;
  --radius-field: 0.5rem;
  --radius-box: 1rem;
  --size-selector: 0.25rem;
  --size-field: 0.25rem;
  --border: 1px;
  --depth: 1;
  --noise: 0;
}
```

---

## 4. Layout & Spacing

-   **Grid System:** Use Tailwind's built-in grid (`grid`, `grid-cols-*`) or flexbox (`flex`) utilities for all layouts.
-   **Spacing Scale:** Adhere strictly to Tailwind’s default 4px-based spacing scale (`p-4`, `m-8`, `gap-2`).
-   **Whitespace:** Embrace whitespace to improve focus and reduce cognitive load. Don't crowd elements.

> **DO:** Use spacing scale classes: `<div class="p-4 m-8">` > **DON'T:** Use arbitrary values: `<div style="padding: 15px; margin: 30px;">`

---

## 5. Components & Interactivity

Use DaisyUI components for all standard UI elements. Below are examples for common components.

-   **Buttons:** Use `btn` and semantic color classes.
-   **Primary:** `<button class="btn btn-primary">Action</button>`
-   **Secondary:** `<button class="btn btn-secondary">Action</button>`
-   **Outline:** `<button class="btn btn-primary btn-outline">Action</button>`
-   **Link:** `<button class="btn btn-link">Action</button>`
-   **[View Docs](https://daisyui.com/components/button/)**

-   **Forms & Inputs:** Wrap inputs in `form-control` for proper spacing and labeling.

```html
<div class="form-control w-full max-w-xs">
    <label class="label">
        <span class="label-text">Username</span>
    </label>
    <input
        type="text"
        placeholder="Type here"
        class="input input-bordered w-full max-w-xs"
    />
</div>
```

-   **[View Docs](https://daisyui.com/components/input/)**

-   **Modals:** Use the `modal` component for dialogs that require user focus.
-   **[View Docs](https://daisyui.com/components/modal/)**

-   **Alerts:** Use `alert` with status colors for feedback.
-   `<div class="alert alert-success"><span>Success!</span></div>`
-   **[View Docs](https://daisyui.com/components/alert/)**

-   **Animations:** Use subtle, built-in DaisyUI/Tailwind transitions. Avoid excessive or distracting motion.

### 5.1. MaryUI Components

For complex UI interactions within Livewire, prefer MaryUI wrappers where available. These provide a fluent PHP interface for DaisyUI components.

-   **Priority Rule:** If a required custom component does not already exist in the project, developers **must prioritize using a MaryUI component** before creating a new one from scratch. This maintains consistency and accelerates development.
-   **Examples:** `x-mary-header`, `x-mary-list`, `x-mary-form`, etc.
-   Refer to the official [MaryUI Documentation](https://mary-ui.com/) for usage details.

---

## 6. Typography

-   **Font Family:** Use the default sans-serif font stack provided by Tailwind (`font-sans`).
-   **Hierarchy:** Use Tailwind's responsive font size utilities to create a clear hierarchy.
-   `h1`: `text-3xl font-bold`
-   `h2`: `text-2xl font-bold`
-   `h3`: `text-xl font-semibold`
-   **Body:** `text-base`
-   **Labels/Captions:** `text-sm`
-   **Line Height:** Use Tailwind's leading utilities (e.g., `leading-relaxed`) for readable text blocks.

## 7. Iconography

-   **Library & Integration:** Internara uses **[Iconify](https://icon-sets.iconify.design/)** for its icon set, integrated via the **[Iconify for Tailwind CSS plugin](https://docs.iconify.design/docs/integrations/tailwind/)**. This plugin allows you to use thousands of icons from various collections simply by adding a class name.
-   **Installation (for reference):**
    1.  Install the plugin: `npm i -D @iconify/tailwind`
    2.  Configure `/resources/css/app.css`:
        ```css
        // resources/css/app.css
        @import 'tailwindcss';
        @plugin 'daisyui';
        @plugin 'iconify';
        ```
-   **Usage:** Icons are used directly in your HTML/Blade templates via `span` tags with specific class names. The plugin automatically converts these classes into inline SVGs during the build process.
    -   The class name format is `icon-[collection--icon-name]`.
    -   You can find icon names and collections on the Iconify website.
-   **Example:** To use a trash icon from the Heroicons collection:

```html
<button class="btn btn-error">
    <span class="icon-[heroicon--trash] h-6 w-6"></span>
    Delete
</button>
```

-   **Styling:** Icons can be styled using standard Tailwind CSS utility classes (e.g., `h-6 w-6` for size, `text-red-500` for color).
-   **Usage Principle:** Icons should supplement text labels, not replace them, unless the meaning is universally understood and unambiguous (e.g., a trash can for delete).