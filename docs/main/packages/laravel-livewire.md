# Laravel Livewire Integration

Livewire is the primary framework for building dynamic, reactive user interfaces in the Internara application. It allows us to create modern, interactive components primarily using PHP, which aligns with our goal of a simplified, full-stack development experience. For general development conventions [Development Conventions Guide](../development-conventions.md)**.

---

## Table of Contents

1.  [Livewire-Specific Architectural Patterns](#1-livewire-specific-architectural-patterns)
2.  [Livewire Cross-Module Communication Mechanisms](#2-livewire-cross-module-communication-mechanisms)
3.  [Dependency Injection in Livewire Components](#3-dependency-injection-in-livewire-components)

---

## 1. Livewire-Specific Architectural Patterns

Our use of Livewire is heavily integrated with our modular architecture and follows strict conventions. For a comprehensive understanding of the project's overall modular architecture, refer to the **[Architecture Guide](../architecture-guide.md)**.

*   **Modular Components:** Livewire components reside within their respective modules (`modules/{ModuleName}/src/Livewire`). We use the `mhmiton/laravel-modules-livewire` package to enable component discovery via the `module::component` syntax (e.g., `@livewire('user::profile-form')`).
*   **Thin Components (No Business Logic):** Livewire components **must not** contain any business logic. Their role is strictly limited to handling UI events, managing component state, and delegating all business operations to dedicated **Service classes**. This reinforces the layered architecture detailed in the **[Architecture Guide](../architecture-guide.md)**.
*   **Volt for Single-File Components:** Where appropriate, we use **Volt** to create single-file Livewire components.
*   **UI Libraries:** All Livewire components are styled using a combination of **Tailwind CSS**, **DaisyUI**, and the **MaryUI** component library. [UI/UX Development Guide](../ui-ux-development-guide.md).

---

## 2. Livewire Cross-Module Communication Mechanisms

To maintain the isolation of our modules, Livewire components communicate across module boundaries using one of two approved methods. For the general principles of inter-module communication, refer to the **[Architecture Guide](../architecture-guide.md)**.

1.  **Event-Based Rendering (Browser Events):** This is the preferred method for dynamic, client-side interactions. A component in one module can dispatch a browser event (`$this->dispatch()`), and a component in another module can listen for that event (`#[On('event-name')]`) to refresh its state or trigger an action.
2.  **Slot Injection (`@slotRender`):** For server-rendered UI, the application uses a custom slot system. This allows a module to register a Livewire component to a named "slot," which is then rendered by a layout in another module (typically the `UI` module). This is used for less dynamic content. For details on the slot system, refer to the **[UI/UX Development Guide](../ui-ux-development-guide.md)**.

---

## 3. Dependency Injection in Livewire Components

When performing Dependency Injection (DI) into Livewire components, it is crucial to use the `boot()` method instead of the class constructor. This approach is necessitated by Livewire's internal mechanisms for component hydration and property management. For the general convention on DI in Livewire, refer to the **[Development Conventions Guide](../development-conventions.md)**.

**Why use `boot()` for DI?**
Livewire's lifecycle involves hydrating component properties (e.g., public properties bound to the view) *after* the constructor has run. Injecting dependencies directly into the constructor can interfere with this hydration process or lead to unexpected behavior if those dependencies are needed during hydration. The `boot()` method is executed after the component's properties have been fully hydrated, making it the ideal place to resolve and inject any required services.

**Best Practice:** Always type-hint interfaces for your dependencies to maintain loose coupling.

**Example:**

```php
<?php

namespace Modules\User\Livewire;

use Livewire\Component;
use Modules\User\Contracts\Services\UserService; // Injecting an interface

class UserProfileEditor extends Component
{
    public $userId;
    protected UserService $userService; // Declare the property for the injected service

    // Avoid constructor injection for services in Livewire components
    // public function __construct(UserService $userService) { ... }

    public function boot(UserService $userService): void
    {
        // Resolve and assign the service here, after properties are hydrated
        $this->userService = $userService;
    }

    public function mount(string $userId): void
    {
        $this->userId = $userId;
        // You can now use $this->userService here
        $user = $this->userService->getUser($this->userId);
        // ...
    }

    public function render()
    {
        return view('user::livewire.user-profile-editor');
    }
}
