# UI Components: Layouts

Layout components provide the structural foundation for all pages in Internara. They ensure
consistent HTML boilerplate, head meta-tags, and global asset loading.

## `layouts.base`

The root layout component. Every page in the application ultimately extends this.

- **File Location:** `modules/UI/resources/views/components/layouts/base.blade.php`
- **Prefix:** `ui::layouts.base`

### Usage

```blade
<x-ui::layouts.base title="Dashboard">
    <div>Your content here</div>
</x-ui::layouts.base>
```

### Props

| Prop    | Type     | Default | Description                                  |
| :------ | :------- | :------ | :------------------------------------------- |
| `title` | `string` | `null`  | The page title displayed in the browser tab. |

### Internal Structure

- Includes `<x-ui::layouts.base.head>` for scripts, styles, and meta-tags.
- Includes `<x-mary-toast />` for global notification support.
- Provides a `@stack('scripts')` for page-specific JS.

---

## `layouts.base.with-navbar`

A specialized layout that includes the global navigation bar and a responsive main content area.

- **File Location:** `modules/UI/resources/views/components/layouts/base/with-navbar.blade.php`

### Usage

```blade
<x-ui::layouts.base.with-navbar title="Settings">
    <div class="p-6">Settings Content</div>
</x-ui::layouts.base.with-navbar>
```

### Slots

- `default`: The main content of the page, wrapped in a `<main>` tag with appropriate spacing.

---

**Navigation**

[← Back to TOC](../table-of-contents.md) | [Next: Forms →](forms.md)
