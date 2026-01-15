# UI Components: Navigation

These components handle application navigation, branding, and modular UI injection.

## `navbar`

The primary navigation bar. It is designed to be extensible via the `SlotManager`.

- **Prefix:** `ui::navbar`

### Usage

```blade
<x-ui::navbar />
```

### Extension Points (Slots)

The navbar uses `@slotRender` to allow other modules to inject content:

1. `navbar.brand`: Where the logo or app name is rendered.
2. `navbar.actions`: Where user menus, theme togglers, or notifications are rendered.

---

## `footer`

The global application footer.

- **Prefix:** `ui::footer`

### Usage

```blade
<x-ui::footer />
```

### Extension Points (Slots)

1. `footer.app-credit`: Allows modules to add additional credits or links to the footer.

---

## `brand`

Renders the application name from the system settings.

- **Usage:** `<x-ui::brand />`
- **Output:** A link to `/` containing the `brand_name` setting.

---

## `sidebar`

_(Currently placeholder)_ - Intended for vertical navigation layouts.

---

**Navigation**

[← Forms](forms.md) | [Next: Display →](display.md)
