# DevDojo Components

A beautifully crafted set of Blade UI components for Laravel. Browse them, then
**add** the ones you need straight into your app — once added, the code is yours
to keep and customize. No black boxes, no lock‑in.

Built with [Tailwind CSS](https://tailwindcss.com) v4 and
[Alpine.js](https://alpinejs.dev). Works great with Livewire.

```blade
<x-button>Get started</x-button>
<x-input label="Email" type="email" wire:model="email" />
<x-alert variant="success" title="Saved!" description="Your changes are live." />
```

---

## Why "add, don't depend"

Most component libraries hide their markup behind a dependency. DevDojo Components
takes the opposite approach (à la shadcn/ui): you preview everything from the
package, then add the components you want into
`resources/views/components/`. They become ordinary
[anonymous Blade components](https://laravel.com/docs/blade#anonymous-components)
that you own and can edit freely.

## Requirements

- PHP 8.2+
- Laravel 11, 12, or 13
- Tailwind CSS v4
- Alpine.js (bundled with Livewire, or include it yourself)

## Installation

```bash
composer require devdojo/components
```

Then run the installer to publish the theme and wire it into your CSS:

```bash
php artisan components:install
```

This copies `resources/css/components.css` (the design tokens) and adds
`@import './components.css';` to your `resources/css/app.css`. Finally, rebuild
your assets:

```bash
npm run build   # or: npm run dev
```

> The theme enables **class‑based dark mode** — add the `dark` class to your
> `<html>` element to switch.

## Previewing components

Before adding a component, every one is available under the `components`
namespace so you can try it instantly:

```blade
<x-components.button variant="primary">Preview me</x-components.button>
```

In your **local** environment you also get a full gallery at **`/components`**
that renders every component in light and dark mode.

## Adding components

List what's available:

```bash
php artisan components:list
```

Add components interactively (multi‑select), or pass names directly:

```bash
php artisan components:add              # interactive picker
php artisan components:add button alert # add specific components
php artisan components:add --all        # add everything
```

Each component is written to its own folder as an index component, e.g.
`resources/views/components/button/index.blade.php`, so you use it as
`<x-button>`. Dependencies are resolved automatically — adding `input` also
adds `label`; adding `modal` also adds `button`.

Re‑run with `--force` to overwrite components you've already added.

## The components

| Component | Use it as | Notes |
|-----------|-----------|-------|
| Button | `<x-button>` | 6 variants, 7 sizes, link mode, Livewire loaders |
| Input | `<x-input>` | Optional label + automatic validation errors |
| Label | `<x-label>` | Accessible form label |
| Checkbox | `<x-checkbox>` | Label, description, custom icon, clickable rows |
| Radio | `<x-radio>` | Same API as checkbox |
| Toggle | `<x-toggle>` | Animated switch, 3 sizes |
| Card | `<x-card>` | Header / footer slots, 3 sizes |
| Modal | `<x-modal>` | Teleported, focus‑trapped dialog |
| Dropdown | `<x-dropdown>` | Click‑to‑open menu anchored to a trigger |
| Popover | `<x-popover>` | The dropdown's positioning engine, fully placeable |
| Tooltip | `<x-tooltip>` | Pure‑Alpine, hover/focus, 4 positions |
| Drawer | `<x-drawer>` | Teleported slide‑in panel (left/right) |
| Alert | `<x-alert>` | 6 contextual variants with built‑in icons |
| Toast | `<x-toast>` | Global notification stack |
| Monaco Editor | `<x-monaco-editor>` | The VS Code editor — needs JS assets ⚡ |
| Tiptap | `<x-tiptap>` | Rich‑text WYSIWYG editor — needs JS assets ⚡ |

### ⚡ Asset‑backed components

`monaco-editor` and `tiptap` lazy‑load a compiled JavaScript bundle. After
adding either, publish the bundles once:

```bash
php artisan vendor:publish --tag=devdojo-assets
```

This copies the editors' JS/CSS into `public/devdojo/`. They are loaded on
demand the first time the component appears on a page — nothing is added to your
main bundle. To rebuild the assets from source (in this package):

```bash
npm install && npm run build   # outputs to public/devdojo
```

### Toast usage

Drop a single `<x-toast />` into your layout, then trigger notifications from:

```js
// JavaScript / Alpine
window.toast('Saved successfully!', 'success', 'Optional description.');
```

```php
// Livewire
$this->dispatch('pop-toast', message: 'Saved!', type: 'success');

// Or flash from a controller
return back()->with('toast', ['message' => 'Saved!', 'type' => 'success']);
```

## Theming

All colors and radii are CSS variables in `resources/css/components.css`. Change
them once and every component updates — in both light and dark mode:

```css
:root {
    --primary: oklch(0.55 0.21 264); /* make the brand blue */
}
```

## Class merging

Components use [`tailwind-merge-laravel`](https://github.com/gehrisandro/tailwind-merge-laravel)
so any classes you pass intelligently override the defaults:

```blade
<x-button class="rounded-full px-10">Pill</x-button>
```

## Testing

```bash
composer test
```

## Credits

Built by [Tony Lea](https://twitter.com/tnylea) and the
[DevDojo](https://devdojo.com) community. Many components began life in
[KatanaUI](https://github.com/katanaui/katana).

## License

The MIT License (MIT). See [LICENSE.md](LICENSE.md).
