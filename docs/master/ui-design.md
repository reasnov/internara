# Internara - UI Technical Implementation Guide

This document provides detailed technical specifications for implementing UI components and systems in the Internara application. It is intended for developers who are actively building or modifying the user interface.

---

## 1. The `UI` Module: The Center of Frontend

The `UI` module (`modules/UI`) is the definitive source of truth for all frontend assets and standards within the Internara application. It encapsulates all core styling, JavaScript, and view components.

-   **Asset Entry Points:** The root-level files at `resources/js/app.js` and `resources/css/app.css` simply import the actual assets from `modules/UI/resources/`.
-   **Configuration:** All frontend tooling, including Tailwind CSS, DaisyUI, and Vite, is configured to process and bundle assets originating from this module.
-   **Component Storage:** Core Blade and Livewire components that form the foundation of the user interface are stored within `modules/UI/resources/views/`.

All UI development should be done with the `UI` module as the central context.

---

## 2. CSS & Theme Configuration

All theme and Tailwind CSS configurations are managed directly within the `UI` module's primary CSS file.

### 2.1. Theme and Plugin Configuration

The following configuration in `modules/UI/resources/css/app.css` defines our application's color scheme, plugins, and custom styles.

```css
// modules/UI/resources/css/app.css
@import "tailwindcss";

@source '../views';
@source '../../vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php';

@theme {
    --font-sans: "Instrument Sans", ui-sans-serif, system-ui, sans-serif,
        "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol",
        "Noto Color Emoji";
}

/** daisyUI */
@plugin "daisyui";

@plugin "daisyui/theme" {
    name: "light";
    default: true;
    prefersdark: false;
    color-scheme: "light";
    --color-base-100: oklch(100% 0 0);
    --color-base-200: oklch(97% 0 0);
    --color-base-300: oklch(94% 0 0);
    --color-base-content: oklch(0% 0 0);
    --color-primary: oklch(15.906% 0 0);
    --color-primary-content: oklch(100% 0 0);
    --color-secondary: oklch(21.455% 0.001 17.278);
    --color-secondary-content: oklch(100% 0 0);
    --color-accent: oklch(26.861% 0 0);
    --color-accent-content: oklch(100% 0 0);
    --color-neutral: oklch(0% 0 0);
    --color-neutral-content: oklch(100% 0 0);
    --color-info: oklch(79.54% 0.103 205.9);
    --color-info-content: oklch(15.908% 0.02 205.9);
    --color-success: oklch(90.13% 0.153 164.14);
    --color-success-content: oklch(18.026% 0.03 164.14);
    --color-warning: oklch(88.37% 0.135 79.94);
    --color-warning-content: oklch(17.674% 0.027 79.94);
    --color-error: oklch(78.66% 0.15 28.47);
    --color-error-content: oklch(15.732% 0.03 28.47);
    --radius-selector: 0.5rem;
    --radius-field: 0.5rem;
    --radius-box: 0.5rem;
    --size-selector: 0.25rem;
    --size-field: 0.25rem;
    --border: 1px;
    --depth: 0;
    --noise: 0;
}

/* maryUI */
@source "../../vendor/robsontenorio/mary/src/View/Components/**/*.php";

/* Theme toggle */
@custom-variant dark (&:where(.dark, .dark *));

/**
* Paginator - Traditional style
* Because Laravel defaults does not match well the design of daisyUI.
*/

.mary-table-pagination span[aria-current="page"] > span {
    @apply bg-primary text-base-100;
}

.mary-table-pagination button {
    @apply cursor-pointer;
}
```

---

## 3. Component Strategy

To maintain consistency and accelerate development, a clear component hierarchy is enforced.

### 3.1. Component Choice Policy

When new UI functionality is required, developers must follow this order of preference:

1.  **Use Existing `UI` Module Components:** First, check if a suitable Blade or Livewire component already exists within the `UI` module (`modules/UI/resources/views/components`).
2.  **Use `MaryUI` Components:** If no suitable component is found in the `UI` module, the next choice **must** be a component from the [MaryUI](https://mary-ui.com/) library. MaryUI is the project's designated component kit for building upon DaisyUI with Livewire.
3.  **Create a New `UI` Module Component:** Only if neither of the above options provides the necessary functionality should a new, custom component be created. All new components must be placed within the `UI` module.

---

## 4. Cross-Module UI Communication

Directly coupling modules at the UI layer is prohibited. To ensure components remain isolated and portable, all interactions between modules must use one of the two approved methods: Slot Injection or Event-Based Rendering.

### 4.1. Slot Injection (`@slotRender`)

As detailed in the "Slot Registry for Dynamic UI" section, the `@slotRender` directive is the primary method for a module to inject Blade or Livewire components into a layout defined in another module. This method is ideal for server-rendered content that doesn't require complex client-side reactivity between the two components.

### 4.2. Event-Based Rendering (Browser Events)

For dynamic client-side communication between components (especially Livewire components) across different modules, use browser events. This creates a fully decoupled system.

**Example:**

1.  **Module A (`Edit-Post` component)** dispatches an event after saving a post.

    ```php
    // In a Livewire component in Module A
    class EditPost extends Component
    {
        public function save()
        {
            // ... save logic ...

            // Dispatch a browser event to notify other components
            $this->dispatch('post-updated', postId: $this->post->id);
        }
    }
    ```

2.  **Module B (`Post-List` component)** listens for this event to refresh itself.

    ```php
    // In a Livewire component in Module B
    use Livewire\Attributes\On;

    class PostList extends Component
    {
        #[On('post-updated')]
        public function refreshPostList($postId)
        {
            // The component will automatically re-render.
            // You can add specific logic here if needed, like showing a notification.
        }

        // ... rest of the component ...
    }
    ```

This approach ensures that `Module A` does not need to know that `Module B` exists, and vice-versa.

---

## 5. Slot System for Dynamic UI

The slot system provides a powerful mechanism to dynamically inject server-rendered UI components (Blade views or Livewire components) into predefined "slots" within layouts. This promotes modularity by allowing different parts of the application to contribute UI elements to a common layout without direct coupling.

The system is composed of two core services:

-   **`SlotRegistry`**: (`Modules\UI\Core\SlotRegistry`) This service acts as a singleton container that collects UI component registrations from anywhere in the application. Its contract is defined at `Modules\UI\Contracts\Core\SlotRegistry`.
-   **`SlotManager`**: (`Modules\UI\Core\SlotManager`) This service is responsible for handling the rendering logic. It retrieves registered components from the `SlotRegistry` and renders them into HTML. Its contract is defined at `Modules\UI\Contracts\Core\SlotManager`.

### Key Concepts

*   **Dynamic Injection:** Register UI components (Blade views, Livewire components, or custom render functions) to named slots using the `SlotRegistry`.
*   **Centralized Rendering:** Render all components registered to a specific slot at a single point in your layouts using the `SlotManager` via the `@slotRender` directive.
*   **Decoupling:** Modules can contribute UI pieces without knowing the exact structure of the consuming layout.

### When to Use the Slot System (The Philosophy)

The Slot System is a powerful tool for decoupling, but it should be used judiciously. Its primary purpose is to create **extension points** in a component where other modules might need to inject UI elements.

> **Rule of Thumb:** Use `@slotRender` only when you expect or want to allow another module to provide a sub-component for a specific part of the UI.

-   **GOOD Use Case:** A slot in the main navbar for user actions (`@slotRender('navbar.actions')`). The `User` module can inject a profile dropdown, and a future `Notification` module could inject a notification bell, all without the `UI` module needing to know about them.
-   **BAD Use Case:** Rendering a static logo within the navbar. The logo is an integral part of the navbar component and is not expected to be replaced by other modules. In this case, a standard `<img ...>` tag or a simple Blade component (`<x-ui::logo />`) is more appropriate.

For static, non-changeable parts of a component, always prefer standard Blade `@include`s or direct component tags over the slot system.

### How to Use

#### 1. Registering Components to a Slot

You can register various types of renderable content using the `SlotRegistry` facade. This typically happens within a module's Service Provider (`boot()` method) or any other bootstrapping location.

*   **Blade View:** Register a Blade view by its name.
    ```php
    // In your Service Provider's boot() method
    \Modules\UI\Facades\SlotRegistry::register('header.actions', 'partials.user-profile-dropdown', ['user' => auth()->user()]);
    ```

*   **Livewire Component:** Register a Livewire component by its tag name (`livewire:component-name`).
    ```php
    // In your Service Provider's boot() method
    \Modules\UI\Facades\SlotRegistry::register('sidebar.menu.top', 'livewire:user-card');
    ```

*   **Custom Renderer (Closure):** Use a closure for more complex logic or conditional rendering.
    ```php
    // In your Service Provider's boot() method
    \Modules\UI\Facades\SlotRegistry::register('footer.links', function() {
        if (auth()->check()) {
            return view('partials.authenticated-footer-links');
        }
        return view('partials.guest-footer-links');
    });
    ```
    *Note: The `$data` parameter can be passed as the second argument to the closure.*

#### 2. Rendering Slots in your Layouts

Once components are registered, you can render them in your Blade layouts. There are two ways to do this: using the custom `@slotRender` directive or the `<x-ui::slot-render>` component tag.

**Method 1: Using the `@slotRender` Directive (Recommended)**

This is the primary and cleanest way to render a slot. It is powered by a custom Blade directive for simplicity.

```blade
<header>
    <div class="container">
        <!-- Other header content -->
        @slotRender('header.actions') {{-- Renders all components registered to 'header.actions' --}}
    </div>
</header>
```

**Method 2: Using the Component Tag**

Alternatively, you can use the standard Blade component syntax. This is functionally equivalent to the directive but is more verbose.

```blade
<header>
    <div class="container">
        <!-- Other header content -->
        <x-ui::slot-render name="header.actions" />
    </div>
</header>
```

Both methods achieve the same result, rendering all UI components that have been registered to the specified slot name.

### Important Note on Inertia

The slot system is designed for server-rendered HTML (Blade and Livewire components). It is **not** suitable for dynamically injecting Inertia components. Inertia operates on a client-side rendering paradigm where dynamic components should be managed using Inertia's own features (e.g., `<component :is="dynamicComponentName" />` in your client-side framework) in conjunction with props passed from your Laravel controllers.
