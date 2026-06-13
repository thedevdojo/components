<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>DevDojo Components</title>

    {{-- Set the theme before paint to avoid a flash of the wrong mode. --}}
    <script>
        (() => {
            const param = new URLSearchParams(window.location.search).get('theme');
            const stored = param || localStorage.getItem('dd-theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (stored === 'dark' || (!stored && prefersDark)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>

    @vite(['resources/css/app.css'])

    {{-- Alpine + the focus plugin (powers the overlay components). --}}
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/focus@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="bg-background text-foreground antialiased"
    x-data="{ dark: document.documentElement.classList.contains('dark'),
        toggle() {
            this.dark = !this.dark;
            document.documentElement.classList.toggle('dark', this.dark);
            localStorage.setItem('dd-theme', this.dark ? 'dark' : 'light');
        } }">

    {{-- Toast container (single instance for the whole page). --}}
    <x-components.toast />

    {{-- Header --}}
    <header class="sticky top-0 z-40 border-b border-foreground/10 bg-background/80 backdrop-blur-md">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4">
            <div class="flex items-center gap-2.5">
                <span class="flex h-8 w-8 items-center justify-center rounded-medium bg-primary text-primary-foreground shadow-[inset_0_1px_1px_0_rgba(255,255,255,0.3)]">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m7 11 2-2-2-2" /><path d="M11 13h4" /><rect x="3" y="3" width="18" height="18" rx="2" /></svg>
                </span>
                <div class="leading-tight">
                    <p class="text-sm font-semibold">DevDojo <span class="text-foreground/50">Components</span></p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="https://devdojo.com" target="_blank" class="hidden text-sm text-foreground/60 transition hover:text-foreground sm:inline-flex">devdojo.com</a>
                <button @click="toggle()" type="button" aria-label="Toggle theme"
                    class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-foreground/10 bg-background text-foreground/70 transition hover:bg-secondary hover:text-foreground">
                    <svg x-show="!dark" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4" /><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41" /></svg>
                    <svg x-show="dark" x-cloak class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z" /></svg>
                </button>
            </div>
        </div>
    </header>

    {{-- Hero --}}
    <section class="mx-auto max-w-6xl px-6 pt-16 pb-10 text-center">
        <span class="inline-flex items-center gap-1.5 rounded-full border border-foreground/10 bg-secondary px-3 py-1 text-xs font-medium text-foreground/70">
            <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
            {{ count($categories->flatten(1)) }} components ready to add
        </span>
        <h1 class="mx-auto mt-6 max-w-2xl text-balance text-4xl font-semibold tracking-tight sm:text-5xl">
            Blade components you'll actually want to ship.
        </h1>
        <p class="mx-auto mt-4 max-w-xl text-balance text-base text-foreground/60">
            Browse the collection below, then add the ones you need straight into your app.
            They become <span class="font-medium text-foreground">your</span> code — edit freely.
        </p>

        <div class="mx-auto mt-7 flex max-w-md items-center justify-center gap-2 rounded-medium border border-foreground/10 bg-card px-4 py-2.5 font-mono text-sm shadow-xs">
            <span class="text-foreground/40 select-none">$</span>
            <span class="text-foreground/90">php artisan components:add</span>
        </div>
    </section>

    {{-- Category navigation --}}
    <nav class="sticky top-[65px] z-30 border-y border-foreground/10 bg-background/80 backdrop-blur-md">
        <div class="mx-auto flex max-w-6xl items-center gap-1 overflow-x-auto px-6 py-3">
            @foreach ($categories as $category => $components)
                <a href="#{{ Str::slug($category) }}" class="whitespace-nowrap rounded-md px-3 py-1.5 text-sm text-foreground/60 transition hover:bg-secondary hover:text-foreground">{{ $category }}</a>
            @endforeach
        </div>
    </nav>

    <main class="mx-auto max-w-6xl space-y-20 px-6 py-16">

        {{-- ============================ FORMS ============================ --}}
        <section id="forms" class="scroll-mt-32 space-y-8">
            <x-devdojo-components::section title="Forms" subtitle="The building blocks of every interface." />

            <x-devdojo-components::demo name="button" title="Button"
                description="Six variants, seven sizes, link mode and built-in Livewire loading states.">
                <div class="flex flex-wrap items-center gap-3">
                    <x-components.button>Primary</x-components.button>
                    <x-components.button variant="secondary">Secondary</x-components.button>
                    <x-components.button variant="outline">Outline</x-components.button>
                    <x-components.button variant="ghost">Ghost</x-components.button>
                    <x-components.button variant="destructive">Destructive</x-components.button>
                    <x-components.button variant="link">Link</x-components.button>
                </div>
                <div class="mt-4 flex flex-wrap items-center gap-3">
                    <x-components.button size="xs">Extra small</x-components.button>
                    <x-components.button size="sm">Small</x-components.button>
                    <x-components.button size="md">Medium</x-components.button>
                    <x-components.button size="lg">Large</x-components.button>
                    <x-components.button size="xl">Extra large</x-components.button>
                    <x-components.button loading>Loading</x-components.button>
                </div>
            </x-devdojo-components::demo>

            <x-devdojo-components::demo name="input" title="Input"
                description="Text input with an optional label and automatic validation errors.">
                <div class="grid max-w-md gap-4">
                    <x-components.input label="Email address" type="email" placeholder="you@example.com" />
                    <x-components.input label="Password" type="password" placeholder="••••••••" />
                </div>
            </x-devdojo-components::demo>

            <x-devdojo-components::demo name="label" title="Label"
                description="An accessible label that pairs with any form control.">
                <x-components.label for="demo-field">Display name</x-components.label>
            </x-devdojo-components::demo>

            <x-devdojo-components::demo name="checkbox" title="Checkbox"
                description="Labels, descriptions, custom icons and clickable rows. Works with wire:model.">
                <div class="grid max-w-md gap-4">
                    <x-components.checkbox label="Email notifications" description="Get notified when something happens." checked />
                    <x-components.checkbox label="SMS notifications" description="Standard rates may apply." />
                    <x-components.checkbox label="Disabled option" disabled />
                </div>
            </x-devdojo-components::demo>

            <x-devdojo-components::demo name="radio" title="Radio"
                description="Same friendly API as the checkbox, for mutually exclusive choices.">
                <div class="grid max-w-md gap-4">
                    <x-components.radio name="plan" label="Starter" description="For side projects." checked />
                    <x-components.radio name="plan" label="Pro" description="For growing teams." />
                    <x-components.radio name="plan" label="Enterprise" description="Talk to sales." />
                </div>
            </x-devdojo-components::demo>

            <x-devdojo-components::demo name="toggle" title="Toggle"
                description="An animated switch for boolean settings, in three sizes.">
                <div class="flex flex-col gap-4">
                    <x-components.toggle label="Public profile" description="Anyone can view your page." checked />
                    <x-components.toggle label="Two-factor auth" size="sm" />
                    <x-components.toggle label="Large switch" size="lg" checked />
                </div>
            </x-devdojo-components::demo>
        </section>

        {{-- ============================ LAYOUT ============================ --}}
        <section id="layout" class="scroll-mt-32 space-y-8">
            <x-devdojo-components::section title="Layout" subtitle="Containers that bring structure to your pages." />

            <x-devdojo-components::demo name="card" title="Card"
                description="A flexible container with optional header and footer slots.">
                <div class="grid w-full gap-4 sm:grid-cols-2">
                    <x-components.card>
                        <x-slot:header>
                            <h3 class="font-semibold text-foreground">Project Atlas</h3>
                            <p class="text-foreground/50">Updated 2 hours ago</p>
                        </x-slot:header>
                        <p class="text-foreground/70">A clean surface for any content — text, forms, stats, you name it.</p>
                        <x-slot:footer>
                            <x-components.button size="sm">Open</x-components.button>
                            <x-components.button size="sm" variant="ghost">Archive</x-components.button>
                        </x-slot:footer>
                    </x-components.card>
                    <x-components.card size="lg">
                        <h3 class="font-semibold text-foreground">Simple card</h3>
                        <p class="mt-1 text-foreground/70">No header or footer — just your content with comfortable padding.</p>
                    </x-components.card>
                </div>
            </x-devdojo-components::demo>
        </section>

        {{-- ============================ OVERLAYS ============================ --}}
        <section id="overlays" class="scroll-mt-32 space-y-8">
            <x-devdojo-components::section title="Overlays" subtitle="Floating UI that appears on demand." />

            <x-devdojo-components::demo name="modal" title="Modal"
                description="Teleported, focus-trapped dialog with header, content and footer slots.">
                <x-components.modal>
                    <x-slot:trigger>
                        <x-components.button>Open modal</x-components.button>
                    </x-slot:trigger>
                    <x-slot:header>Delete project</x-slot:header>
                    <x-slot:content>
                        <p class="text-foreground/70">This action cannot be undone. This will permanently delete the project and all of its data.</p>
                    </x-slot:content>
                    <x-slot:footer>
                        <x-components.button variant="ghost" x-on:click="modalOpen = false">Cancel</x-components.button>
                        <x-components.button variant="destructive" x-on:click="modalOpen = false">Delete</x-components.button>
                    </x-slot:footer>
                </x-components.modal>
            </x-devdojo-components::demo>

            <x-devdojo-components::demo name="dropdown" title="Dropdown"
                description="A click-to-open menu anchored to its trigger.">
                <x-components.dropdown>
                    <x-slot:trigger>
                        <x-components.button variant="outline">
                            Options
                            <svg class="ml-1 h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6" /></svg>
                        </x-components.button>
                    </x-slot:trigger>
                    <x-slot:menu>
                        <a href="#" class="block rounded px-3 py-1.5 text-sm text-foreground/80 transition hover:bg-secondary">Profile</a>
                        <a href="#" class="block rounded px-3 py-1.5 text-sm text-foreground/80 transition hover:bg-secondary">Settings</a>
                        <a href="#" class="block rounded px-3 py-1.5 text-sm text-foreground/80 transition hover:bg-secondary">Billing</a>
                        <div class="my-1 h-px bg-foreground/10"></div>
                        <a href="#" class="block rounded px-3 py-1.5 text-sm text-destructive transition hover:bg-secondary">Sign out</a>
                    </x-slot:menu>
                </x-components.dropdown>
            </x-devdojo-components::demo>

            <x-devdojo-components::demo name="popover" title="Popover"
                description="The dropdown's positioning engine, with full control over placement.">
                <div class="flex flex-wrap gap-3">
                    <x-components.popover position="bottom" align="center">
                        <x-slot:trigger><x-components.button variant="outline">Bottom</x-components.button></x-slot:trigger>
                        <x-slot:content>
                            <p class="text-sm font-medium text-foreground">Popover title</p>
                            <p class="mt-1 text-sm text-foreground/60">Positioned below and centered on the trigger.</p>
                        </x-slot:content>
                    </x-components.popover>
                    <x-components.popover position="right" align="center">
                        <x-slot:trigger><x-components.button variant="outline">Right</x-components.button></x-slot:trigger>
                        <x-slot:content>
                            <p class="text-sm text-foreground/70">I appear to the right.</p>
                        </x-slot:content>
                    </x-components.popover>
                    <x-components.popover position="top" align="center">
                        <x-slot:trigger><x-components.button variant="outline">Top</x-components.button></x-slot:trigger>
                        <x-slot:content>
                            <p class="text-sm text-foreground/70">I appear above.</p>
                        </x-slot:content>
                    </x-components.popover>
                </div>
            </x-devdojo-components::demo>

            <x-devdojo-components::demo name="tooltip" title="Tooltip"
                description="A lightweight, pure-Alpine tooltip on hover or focus.">
                <div class="flex flex-wrap gap-6">
                    <x-components.tooltip content="Top tooltip" position="top">
                        <x-components.button variant="secondary">Hover me</x-components.button>
                    </x-components.tooltip>
                    <x-components.tooltip content="Now to the right" position="right">
                        <x-components.button variant="secondary">And me</x-components.button>
                    </x-components.tooltip>
                    <x-components.tooltip content="Below the button" position="bottom">
                        <x-components.button variant="secondary">Me too</x-components.button>
                    </x-components.tooltip>
                </div>
            </x-devdojo-components::demo>
        </section>

        {{-- ============================ FEEDBACK ============================ --}}
        <section id="feedback" class="scroll-mt-32 space-y-8">
            <x-devdojo-components::section title="Feedback" subtitle="Tell users what just happened." />

            <x-devdojo-components::demo name="alert" title="Alert"
                description="Six contextual variants with a built-in icon, title and description.">
                <div class="flex w-full flex-col items-center gap-3">
                    <x-components.alert variant="primary" title="Primary Alert" description="This is the subtext for your alert message. Add information or instructions here." />
                    <x-components.alert variant="secondary" title="Secondary Alert" description="This is the subtext for your alert message. Add information or instructions here." />
                    <x-components.alert variant="destructive" title="Destructive Alert" description="This is the subtext for your alert message. Add information or instructions here." />
                    <x-components.alert variant="info" title="Info Alert" description="This is the subtext for your alert message. Add information or instructions here." />
                    <x-components.alert variant="success" title="Success Alert" description="This is the subtext for your alert message. Add information or instructions here." />
                    <x-components.alert variant="warning" title="Warning Alert" description="This is the subtext for your alert message. Add information or instructions here." />
                </div>
            </x-devdojo-components::demo>

            <x-devdojo-components::demo name="toast" title="Toast"
                description="A global notification stack with progress, hover-to-pause and four types.">
                <div class="flex flex-wrap gap-3">
                    <x-components.button variant="outline" x-on:click="window.toast('Saved successfully!', 'success')">Success</x-components.button>
                    <x-components.button variant="outline" x-on:click="window.toast('Something went wrong', 'error', 'Please try again later.')">Error</x-components.button>
                    <x-components.button variant="outline" x-on:click="window.toast('Heads up', 'warning')">Warning</x-components.button>
                    <x-components.button variant="outline" x-on:click="window.toast('New update available', 'info')">Info</x-components.button>
                </div>
            </x-devdojo-components::demo>
        </section>

    </main>

    <footer class="border-t border-foreground/10 py-10 text-center text-sm text-foreground/50">
        Built with care by <a href="https://devdojo.com" class="font-medium text-foreground/70 underline-offset-4 hover:underline">DevDojo</a>.
        Add any component with <code class="rounded bg-secondary px-1.5 py-0.5 font-mono text-xs text-foreground/80">php artisan components:add</code>
    </footer>
</body>

</html>
