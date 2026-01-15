# UI Components: Display

Components used for data presentation and visual decoration.

## `card`

A standard container for grouping related content.

- **Usage:**

```blade
<x-ui::card title="User Stats" subtitle="Updated hourly">
    <p>Stats content...</p>
</x-ui::card>
```

- **Technical Note:** Thin wrapper around `x-mary-card`.

---

## `icon`

A helper component to render SVG icons from the **Iconify** library via Blade Icons.

- **Props:**
    - `name`: The icon name (e.g., `tabler-home`, `tabler-user`).
- **Usage:**

```blade
<x-ui::icon name="tabler-settings" class="w-5 h-5 text-primary" />
```

---

## `app-credit`

Renders a small credit tag, typically used in the footer.

---

**Navigation**

[← Navigation](navigation.md) | [Next: Utilities →](utilities.md)
