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

    @if (class_exists(\Livewire\Livewire::class))
        {{-- Livewire ships Alpine (with the focus & collapse plugins) and powers
             the "add" buttons that publish components into the host app. --}}
        @livewireStyles
    @else
        {{-- No Livewire installed: load Alpine + the focus plugin from a CDN. --}}
        <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/focus@3.x.x/dist/cdn.min.js"></script>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @endif
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

            <x-devdojo-components::demo name="select" title="Select"
                description="A styled native select with an optional label, placeholder and validation errors.">
                <div class="grid w-full max-w-md gap-4">
                    <x-components.select label="Country" placeholder="Choose a country">
                        <option>United States</option>
                        <option>United Kingdom</option>
                        <option>Australia</option>
                        <option>Canada</option>
                    </x-components.select>
                </div>
            </x-devdojo-components::demo>

            <x-devdojo-components::demo name="textarea" title="Textarea"
                description="A multi-line field matching the input styling, with label and validation errors.">
                <div class="grid w-full max-w-md gap-4">
                    <x-components.textarea label="Message" placeholder="Write your message…" />
                </div>
            </x-devdojo-components::demo>

            <x-devdojo-components::demo name="slider" title="Slider"
                description="A range slider themed with your primary color, with an optional live value readout.">
                <div class="grid w-full max-w-md gap-6">
                    <x-components.slider label="Volume" :value="60" show-value />
                    <x-components.slider :value="30" />
                </div>
            </x-devdojo-components::demo>

            <x-devdojo-components::demo name="rating" title="Rating"
                description="Interactive star rating with hover preview. Click the current value again to clear it.">
                <div class="flex flex-col items-center gap-4">
                    <x-components.rating :value="4" />
                    <x-components.rating :value="3" size="lg" />
                    <x-components.rating :value="5" size="sm" readonly />
                </div>
            </x-devdojo-components::demo>

            <x-devdojo-components::demo name="date-picker" title="Date Picker"
                description="A calendar bound to an input, with month navigation and several formats. The calendar teleports so it never clips.">
                <div class="w-full max-w-xs">
                    <x-components.date-picker label="Departure date" />
                </div>
            </x-devdojo-components::demo>

            <x-devdojo-components::demo name="otp" title="OTP Input"
                description="A segmented one-time-code input with auto-advance, backspace navigation and paste-to-fill.">
                <x-components.otp :length="6" />
            </x-devdojo-components::demo>

            <x-devdojo-components::demo name="avatar-upload" title="Avatar Upload"
                description="A circular picker with a built-in crop-and-resize modal. Click to choose a JPG or PNG.">
                <div class="flex items-center gap-8">
                    <x-components.avatar-upload :size="20" />
                    <x-components.avatar-upload :size="20" src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=200&h=200&fit=crop&crop=faces" />
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

            <x-devdojo-components::demo name="infinite-canvas" title="Infinite Canvas"
                description="A pannable, zoomable surface with momentum. Scroll or trackpad to glide across it; drop any content in the middle.">
                <div class="h-80 w-full overflow-hidden rounded-large border border-foreground/10 bg-card">
                    <x-components.infinite-canvas :scale="100" dot-pattern>
                        <div class="rounded-large border border-foreground/10 bg-background px-6 py-4 text-center shadow-xs">
                            <p class="font-semibold text-foreground">Pan around me</p>
                            <p class="mt-1 text-sm text-foreground/60">Scroll or trackpad to glide across the canvas.</p>
                        </div>
                    </x-components.infinite-canvas>
                </div>
            </x-devdojo-components::demo>

            <x-devdojo-components::demo name="splitter" title="Splitter"
                description="Resizable panes with a draggable gutter. Drag the seam to resize; lay panes out horizontally or vertically, and nest them.">
                <x-components.splitter class="h-72 w-full overflow-hidden rounded-large border border-foreground/10" :min-size="80">
                    <x-components.splitter.pane :size="35">
                        <div class="flex h-full items-center justify-center bg-card p-6 text-center">
                            <div>
                                <p class="font-semibold text-foreground">Sidebar</p>
                                <p class="mt-1 text-sm text-foreground/60">Drag the seam →</p>
                            </div>
                        </div>
                    </x-components.splitter.pane>
                    <x-components.splitter.pane :size="65">
                        <x-components.splitter direction="vertical" :min-size="60">
                            <x-components.splitter.pane>
                                <div class="flex h-full items-center justify-center bg-background p-6 text-center text-sm text-foreground/70">Top panel</div>
                            </x-components.splitter.pane>
                            <x-components.splitter.pane>
                                <div class="flex h-full items-center justify-center bg-background p-6 text-center text-sm text-foreground/70">Bottom panel</div>
                            </x-components.splitter.pane>
                        </x-components.splitter>
                    </x-components.splitter.pane>
                </x-components.splitter>
            </x-devdojo-components::demo>

            <x-devdojo-components::demo name="accordion" title="Accordion"
                description="Stacked collapsible sections with a smooth, plugin-free height animation. Set exclusive to open one at a time.">
                <div class="w-full max-w-xl rounded-large border border-foreground/10 bg-card px-4">
                    <x-components.accordion exclusive>
                        <x-components.accordion.item title="Is it accessible?" open>
                            Yes. Each header is a real button and the panel is fully keyboard reachable.
                        </x-components.accordion.item>
                        <x-components.accordion.item title="Is it animated?">
                            Yes — it animates with a CSS grid trick, so there's no Alpine plugin to install.
                        </x-components.accordion.item>
                        <x-components.accordion.item title="Can I customize it?">
                            Absolutely. Every part is yours to edit once you publish the component.
                        </x-components.accordion.item>
                    </x-components.accordion>
                </div>
            </x-devdojo-components::demo>

            <x-devdojo-components::demo name="tabs" title="Tabs"
                description="A segmented control that switches between panels of content.">
                <div class="w-full max-w-md">
                    <x-components.tabs>
                        <x-components.tabs.list>
                            <x-components.tabs.tab tab="account">Account</x-components.tabs.tab>
                            <x-components.tabs.tab tab="password">Password</x-components.tabs.tab>
                            <x-components.tabs.tab tab="team">Team</x-components.tabs.tab>
                        </x-components.tabs.list>
                        <x-components.tabs.panel tab="account">
                            <x-components.card><p class="text-sm text-foreground/70">Manage your account details and preferences here.</p></x-components.card>
                        </x-components.tabs.panel>
                        <x-components.tabs.panel tab="password">
                            <x-components.card><p class="text-sm text-foreground/70">Change your password — you'll be signed out afterwards.</p></x-components.card>
                        </x-components.tabs.panel>
                        <x-components.tabs.panel tab="team">
                            <x-components.card><p class="text-sm text-foreground/70">Invite teammates and manage their roles.</p></x-components.card>
                        </x-components.tabs.panel>
                    </x-components.tabs>
                </div>
            </x-devdojo-components::demo>

            <x-devdojo-components::demo name="separator" title="Separator"
                description="A thin divider — horizontal, vertical, or with a centered label.">
                <div class="w-full max-w-md">
                    <p class="text-sm text-foreground/70">Above the line</p>
                    <x-components.separator class="my-4" />
                    <div class="flex items-center text-sm text-foreground/70">
                        <span>Profile</span>
                        <x-components.separator vertical class="h-4" />
                        <span>Settings</span>
                        <x-components.separator vertical class="h-4" />
                        <span>Logout</span>
                    </div>
                    <x-components.separator label="OR" class="my-4" />
                    <p class="text-sm text-foreground/70">Below the labeled divider</p>
                </div>
            </x-devdojo-components::demo>
        </section>

        {{-- ============================ NAVIGATION ============================ --}}
        <section id="navigation" class="scroll-mt-32 space-y-8">
            <x-devdojo-components::section title="Navigation" subtitle="Help people find their way around." />

            <x-devdojo-components::demo name="breadcrumbs" title="Breadcrumbs"
                description="A navigation trail to the current page, with chevron separators added automatically.">
                <x-components.breadcrumbs>
                    <x-components.breadcrumbs.item href="#">Home</x-components.breadcrumbs.item>
                    <x-components.breadcrumbs.item href="#">Projects</x-components.breadcrumbs.item>
                    <x-components.breadcrumbs.item href="#">Atlas</x-components.breadcrumbs.item>
                    <x-components.breadcrumbs.item current>Settings</x-components.breadcrumbs.item>
                </x-components.breadcrumbs>
            </x-devdojo-components::demo>

            <x-devdojo-components::demo name="pagination" title="Pagination"
                description="Page navigation with previous/next controls and a windowed list of page numbers.">
                <x-components.pagination :current-page="4" :total-pages="10" base-url="" />
            </x-devdojo-components::demo>
        </section>

        {{-- ============================ DISPLAY ============================ --}}
        <section id="display" class="scroll-mt-32 space-y-8">
            <x-devdojo-components::section title="Display" subtitle="Compact elements that label and categorize." />

            <x-devdojo-components::demo name="badge" title="Badge"
                description="Status, count and category labels. Solid, soft and outline variants, three sizes, optional icon and pill shape.">
                <div class="flex flex-col items-center gap-5">
                    <div class="flex flex-wrap items-center justify-center gap-2">
                        <x-components.badge>Primary</x-components.badge>
                        <x-components.badge variant="secondary">Secondary</x-components.badge>
                        <x-components.badge variant="outline">Outline</x-components.badge>
                        <x-components.badge variant="success">Success</x-components.badge>
                        <x-components.badge variant="info">Info</x-components.badge>
                        <x-components.badge variant="warning">Warning</x-components.badge>
                        <x-components.badge variant="destructive">Destructive</x-components.badge>
                    </div>
                    <div class="flex flex-wrap items-center justify-center gap-2">
                        <x-components.badge size="sm">Small</x-components.badge>
                        <x-components.badge size="md">Medium</x-components.badge>
                        <x-components.badge size="lg">Large</x-components.badge>
                        <x-components.badge variant="success" pill>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5" /></svg>
                            Verified
                        </x-components.badge>
                        <x-components.badge variant="secondary" pill>
                            <span class="size-1.5 rounded-full bg-green-500"></span>
                            Online
                        </x-components.badge>
                    </div>
                </div>
            </x-devdojo-components::demo>

            <x-devdojo-components::demo name="avatar" title="Avatar"
                description="Photo, auto-generated initials or an icon fallback. Five sizes, circle or square, status dots and deterministic colors.">
                <div class="flex flex-col items-center gap-6">
                    <div class="flex flex-wrap items-center justify-center gap-4">
                        <x-components.avatar size="xs" name="Tony Lea" circle />
                        <x-components.avatar size="sm" name="Ada Lovelace" color="auto" circle />
                        <x-components.avatar size="md" src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=120&h=120&fit=crop&crop=faces" circle status="online" />
                        <x-components.avatar size="lg" name="Grace Hopper" color="auto" />
                        <x-components.avatar size="xl" circle status="busy" />
                    </div>
                    <div class="flex flex-wrap items-center justify-center gap-3">
                        <x-components.avatar name="Blue" color="blue" circle />
                        <x-components.avatar name="Green" color="green" circle />
                        <x-components.avatar name="Amber" color="amber" circle />
                        <x-components.avatar name="Rose" color="rose" circle />
                        <x-components.avatar name="Violet" color="violet" circle />
                    </div>
                </div>
            </x-devdojo-components::demo>

            <x-devdojo-components::demo name="table" title="Table"
                description="A styled, horizontally scrollable data table composed from header and row sub-components.">
                <x-components.table>
                    <x-components.table.columns>
                        <x-components.table.column>Name</x-components.table.column>
                        <x-components.table.column>Role</x-components.table.column>
                        <x-components.table.column>Status</x-components.table.column>
                    </x-components.table.columns>
                    <x-components.table.row>
                        <x-components.table.cell class="font-medium text-foreground">Tony Lea</x-components.table.cell>
                        <x-components.table.cell>Founder</x-components.table.cell>
                        <x-components.table.cell><x-components.badge variant="success" size="sm">Active</x-components.badge></x-components.table.cell>
                    </x-components.table.row>
                    <x-components.table.row>
                        <x-components.table.cell class="font-medium text-foreground">Ada Lovelace</x-components.table.cell>
                        <x-components.table.cell>Engineer</x-components.table.cell>
                        <x-components.table.cell><x-components.badge variant="secondary" size="sm">Invited</x-components.badge></x-components.table.cell>
                    </x-components.table.row>
                    <x-components.table.row>
                        <x-components.table.cell class="font-medium text-foreground">Grace Hopper</x-components.table.cell>
                        <x-components.table.cell>Advisor</x-components.table.cell>
                        <x-components.table.cell><x-components.badge variant="warning" size="sm">Away</x-components.badge></x-components.table.cell>
                    </x-components.table.row>
                </x-components.table>
            </x-devdojo-components::demo>

            <x-devdojo-components::demo name="empty-state" title="Empty State"
                description="A friendly placeholder for when there's nothing to show yet.">
                <x-components.empty-state title="No projects yet" description="Create your first project to get started — it only takes a minute.">
                    <x-components.button size="sm">New project</x-components.button>
                </x-components.empty-state>
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

            <x-devdojo-components::demo name="drawer" title="Drawer"
                description="A teleported slide-in panel from the left or right, with header, content and footer slots.">
                <div class="flex flex-wrap gap-3">
                    <x-components.drawer position="right">
                        <x-slot:trigger><x-components.button>Open right drawer</x-components.button></x-slot:trigger>
                        <x-slot:header>Edit profile</x-slot:header>
                        <x-slot:content>
                            <div class="grid gap-4">
                                <x-components.input label="Name" value="Tony Lea" />
                                <x-components.input label="Email" type="email" value="tony@devdojo.com" />
                                <x-components.toggle label="Public profile" checked />
                            </div>
                        </x-slot:content>
                        <x-slot:footer>
                            <x-components.button variant="ghost" x-on:click="open = false">Cancel</x-components.button>
                            <x-components.button x-on:click="open = false">Save changes</x-components.button>
                        </x-slot:footer>
                    </x-components.drawer>

                    <x-components.drawer position="left">
                        <x-slot:trigger><x-components.button variant="outline">Open left drawer</x-components.button></x-slot:trigger>
                        <x-slot:header>Navigation</x-slot:header>
                        <x-slot:content>
                            <nav class="grid gap-1">
                                <a href="#" class="rounded-medium px-3 py-2 text-sm text-foreground/80 hover:bg-secondary">Dashboard</a>
                                <a href="#" class="rounded-medium px-3 py-2 text-sm text-foreground/80 hover:bg-secondary">Projects</a>
                                <a href="#" class="rounded-medium px-3 py-2 text-sm text-foreground/80 hover:bg-secondary">Settings</a>
                            </nav>
                        </x-slot:content>
                    </x-components.drawer>
                </div>
            </x-devdojo-components::demo>

            @php
                $commandItems = [
                    ['group' => 'Suggestions', 'title' => 'Calendar', 'value' => 'calendar', 'icon' => '<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>'],
                    ['group' => 'Suggestions', 'title' => 'Search Docs', 'value' => 'docs', 'icon' => '<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>'],
                    ['group' => 'Settings', 'title' => 'Profile', 'value' => 'profile', 'shortcut' => '⌘P', 'icon' => '<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>'],
                    ['group' => 'Settings', 'title' => 'Billing', 'value' => 'billing', 'shortcut' => '⌘B', 'icon' => '<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>'],
                    ['group' => 'Settings', 'title' => 'Settings', 'value' => 'settings', 'shortcut' => '⌘S', 'icon' => '<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>'],
                ];
            @endphp

            <x-devdojo-components::demo name="command" title="Command Palette"
                description="A ⌘K command palette with search, grouped items and full keyboard navigation. Press ⌘K or click the trigger.">
                <x-components.command :items="$commandItems" />
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

            <x-devdojo-components::demo name="progress" title="Progress"
                description="A completion bar in three sizes, plus an indeterminate state for unknown-duration work.">
                <div class="grid w-full max-w-md gap-5">
                    <x-components.progress :value="25" size="sm" />
                    <x-components.progress :value="60" />
                    <x-components.progress :value="90" size="lg" />
                    <x-components.progress indeterminate />
                </div>
            </x-devdojo-components::demo>

            <x-devdojo-components::demo name="skeleton" title="Skeleton"
                description="Pulsing placeholders for content that's still loading.">
                <div class="flex w-full max-w-md items-center gap-4">
                    <x-components.skeleton circle class="size-12 shrink-0" />
                    <div class="flex-1">
                        <x-components.skeleton :lines="3" />
                    </div>
                </div>
            </x-devdojo-components::demo>
        </section>

        {{-- ============================ EDITORS ============================ --}}
        <section id="editors" class="scroll-mt-32 space-y-8">
            <x-devdojo-components::section title="Editors" subtitle="Powerful editing surfaces that lazy-load on demand." />

            <x-devdojo-components::demo name="monaco-editor" title="Monaco Editor"
                description="The VS Code editor as a Blade component — syntax highlighting, themes and auto dark mode.">
                <div class="w-full">
                    <x-components.monaco-editor language="javascript" :height="260" :content="'// The VS Code editor, in a Blade component.
function greet(name) {
    return `Hello, ${name}!`;
}

console.log(greet(\'DevDojo\'));'" />
                </div>
            </x-devdojo-components::demo>

            <x-devdojo-components::demo name="tiptap" title="Tiptap Editor"
                description="A rich-text WYSIWYG editor with a configurable toolbar. Works with wire:model.">
                <div class="w-full">
                    <x-components.tiptap :content="'<h2>Rich text, the Blade way</h2><p>Select some text to format it, add <strong>bold</strong>, <em>italic</em>, or a <a href=\'https://devdojo.com\'>link</a>.</p><ul><li>Bullet lists</li><li>Numbered lists</li></ul>'" />
                </div>
            </x-devdojo-components::demo>
        </section>

    </main>

    <footer class="border-t border-foreground/10 py-10 text-center text-sm text-foreground/50">
        Built with care by <a href="https://devdojo.com" class="font-medium text-foreground/70 underline-offset-4 hover:underline">DevDojo</a>.
        Add any component with <code class="rounded bg-secondary px-1.5 py-0.5 font-mono text-xs text-foreground/80">php artisan components:add</code>
    </footer>

    @if (class_exists(\Livewire\Livewire::class))
        @livewireScripts
    @endif
</body>

</html>
