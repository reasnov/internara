# Internara - Developer UI/UX Guide

This guide provides the core UI/UX principles and standards for developers working on the Internara application. Its purpose is to ensure a consistent, modern, and high-quality user experience.

---

## 1. The `UI` Module: The Center of Frontend

The `UI` module (`modules/UI`) is the definitive source of truth for all frontend assets and standards within the Internara application. It encapsulates all core styling, JavaScript, and view components. All UI-related development and configuration, including the principles in this guide, are implemented within this module.

---

## 2. Core Principles

-   **Minimalist & Modern:** Prioritize a clean, uncluttered aesthetic. Focus on essential functionality.
-   **User-Friendly:** The interface must be intuitive and easy to navigate for all user roles.
-   **Consistent:** Adhere to the guidelines and components defined here to maintain a cohesive look and feel.

---

## 3. UI Framework: DaisyUI + Tailwind CSS

-   **Primary Library:** Internara uses **[DaisyUI](https://daisyui.com/)**, a component library for **[Tailwind CSS](https://tailwindcss.com/)**.
-   **Secondary Library (MaryUI):** We also leverage **[MaryUI](https://mary-ui.com/)** for specialized Laravel/Livewire components that build upon DaisyUI.
-   **Mandatory Use:** All new UI development **must** use DaisyUI components and utility classes. Do not introduce custom one-off styles or new component libraries.
-   **Configuration:** The DaisyUI and MaryUI libraries are configured within the `UI` module's primary stylesheet at `modules/UI/resources/css/app.css`.

---

## 4. Theming & Color Palette

We use DaisyUI's theme system to manage colors for both light and dark modes.

### 4.1. Semantic Colors

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

*The theme is configured in `modules/UI/resources/css/app.css`. For the complete technical details, see the [UI Technical Implementation Guide](../ui-design.md).*

---

## 5. Layout & Spacing

-   **Grid System:** Use Tailwind's built-in grid (`grid`, `grid-cols-*`) or flexbox (`flex`) utilities for all layouts.
-   **Spacing Scale:** Adhere strictly to Tailwind’s default 4px-based spacing scale (`p-4`, `m-8`, `gap-2`).
-   **Whitespace:** Embrace whitespace to improve focus and reduce cognitive load. Don't crowd elements.

> **DO:** Use spacing scale classes: `<div class="p-4 m-8">`
> **DON'T:** Use arbitrary values: `<div style="padding: 15px; margin: 30px;">`

---

## 6. Components & Interactivity

Use DaisyUI components for all standard UI elements. Below are examples for common components.

-   **Buttons:** Use `btn` and semantic color classes.
-   **Primary:** `<button class="btn btn-primary">Action</button>`
-   **Secondary:** `<button class="btn btn-secondary">Action</button>`
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
-   **[View Docs](https://daisyui.com/components/alert/)**

### 6.1. MaryUI Components

For complex UI interactions within Livewire, prefer MaryUI wrappers where available.

-   **Primary Component Choice:** When a required component is not already provided by the `UI` module, developers **must first use a component from MaryUI** before attempting to build a custom one. This policy ensures maximum consistency and development speed.
-   **Examples:** `x-mary-header`, `x-mary-list`, `x-mary-form`, etc.
-   Refer to the official [MaryUI Documentation](https://mary-ui.com/) for usage details.

### 6.2. Module-Specific Components & Communication

To maintain the project's modular architecture, components that are specific to a single module (e.g., a "Create User" form in the `User` module) must be fully **isolated and portable**. They should not have direct dependencies on other modules.

When a component from Module A needs to trigger a UI change or display content from Module B, direct coupling is forbidden. Instead, one of the following approved decoupling mechanisms must be used:

-   **Slot Injection (`@slotRender`):** For statically injecting UI from one module into a designated "slot" in another. This is best for layouts and sections where content is added, but not dynamically changed on the client-side.
-   **Event-Based Rendering:** For dynamic, client-side interactions. A component can dispatch a browser event, and other components (from any module) can listen for this event to update their state and re-render. This is the preferred method for actions like "refresh a data table after a modal closes."

*For details on advanced dynamic UI, see the [UI Technical Implementation Guide](./../../ui-design.md).*

---

## 7. Typography

-   **Font Family:** The primary font is `Instrument Sans`, configured in `modules/UI/resources/css/app.css`. It falls back to Tailwind's default sans-serif stack (`font-sans`).
-   **Hierarchy:** Use Tailwind's responsive font size utilities to create a clear hierarchy.
-   `h1`: `text-3xl font-bold`
-   `h2`: `text-2xl font-bold`
-   `h3`: `text-xl font-semibold`
-   **Body:** `text-base`
-   **Labels/Captions:** `text-sm`
-   **Line Height:** Use Tailwind's leading utilities (e.g., `leading-relaxed`) for readable text blocks.
