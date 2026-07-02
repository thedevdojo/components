@props([
    'categories',
    'current' => null,
    'title' => 'DevDojo Components',
])

@php
    // Flatten byCategory() so palette groups stay contiguous and in the
    // sidebar's display order (byCategory already encodes that order).
    $paletteItems = \DevDojo\Components\Components::byCategory()
        ->flatMap(fn ($components) => $components)
        ->map(fn (array $component) => [
            'title' => $component['label'],
            'value' => $component['name'],
            'group' => $component['category'],
        ])->values()->all();
    $baseUrl = rtrim(route('devdojo-components.showcase'), '/');
@endphp

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>

    {{-- Set the theme before paint to avoid a flash of the wrong mode. --}}
    <script>
        (() => {
            const stored = localStorage.getItem('dd-theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (stored === 'dark' || (!stored && prefersDark)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>

    @if (file_exists(public_path('hot')) || file_exists(public_path('build/manifest.json')))
        @vite(config('components.showcase.assets', ['resources/css/app.css']))
    @endif

    @if (class_exists(\Livewire\Livewire::class))
        @livewireStyles
    @else
        <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/focus@3.x.x/dist/cdn.min.js"></script>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @endif
</head>

<body class="bg-background text-foreground antialiased"
    x-data="{
        dark: document.documentElement.classList.contains('dark'),
        query: '',
        toggle() {
            this.dark = !this.dark;
            document.documentElement.classList.toggle('dark', this.dark);
            localStorage.setItem('dd-theme', this.dark ? 'dark' : 'light');
            window.dispatchEvent(new CustomEvent('dd-theme-changed', { detail: { dark: this.dark } }));
        },
        matches(haystack) {
            return this.query.trim() === '' || haystack.toLowerCase().includes(this.query.trim().toLowerCase());
        }
    }"
    @command-select.window="window.location = '{{ $baseUrl }}/' + $event.detail.value">

    {{-- Toast container + ⌘K palette (single instances for the whole page). --}}
    <x-components.toast />
    <x-components.command :items="$paletteItems" placeholder="Search components…">
        <x-slot:trigger><span class="hidden"></span></x-slot:trigger>
    </x-components.command>

    {{-- Mobile top bar — the desktop layout uses the sidebar instead. --}}
    <div class="sticky top-0 z-40 flex items-center justify-between gap-3 border-b border-foreground/10 bg-background/80 px-4 py-3 backdrop-blur-md lg:hidden">
        <a href="{{ $baseUrl }}" class="flex items-center gap-2.5">
            <span class="flex h-7 w-7 items-center justify-center rounded-medium bg-primary text-primary-foreground shadow-[inset_0_1px_1px_0_rgba(255,255,255,0.3)]">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m7 11 2-2-2-2" /><path d="M11 13h4" /><rect x="3" y="3" width="18" height="18" rx="2" /></svg>
            </span>
            <p class="text-sm font-semibold">Components</p>
        </a>
        <button @click="toggle()" type="button" aria-label="Toggle theme"
            class="inline-flex h-9 w-9 items-center justify-center rounded-medium border border-foreground/10 bg-card text-foreground/70 transition hover:bg-secondary hover:text-foreground">
            <svg x-show="!dark" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4" /><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41" /></svg>
            <svg x-show="dark" x-cloak class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z" /></svg>
        </button>
    </div>

    <div class="mx-auto flex w-full max-w-[88rem] gap-8 px-4 sm:px-6 lg:gap-10 lg:px-8">

        {{-- ===================== SIDEBAR ===================== --}}
        <aside class="sticky top-0 hidden h-screen w-64 shrink-0 flex-col overflow-y-auto pt-10 pb-8 lg:flex">
            <div class="flex items-center justify-between px-2.5">
                <a href="{{ $baseUrl }}" class="flex items-center gap-2.5">
                    <span class="flex h-8 w-8 items-center justify-center rounded-medium bg-primary text-primary-foreground shadow-[inset_0_1px_1px_0_rgba(255,255,255,0.3)]">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m7 11 2-2-2-2" /><path d="M11 13h4" /><rect x="3" y="3" width="18" height="18" rx="2" /></svg>
                    </span>
                    <p class="text-sm font-semibold tracking-tight">Components</p>
                </a>
                <button @click="toggle()" type="button" aria-label="Toggle theme"
                    class="inline-flex h-8 w-8 items-center justify-center rounded-medium border border-foreground/10 bg-card text-foreground/70 transition hover:bg-secondary hover:text-foreground">
                    <svg x-show="!dark" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4" /><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41" /></svg>
                    <svg x-show="dark" x-cloak class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z" /></svg>
                </button>
            </div>

            <label class="mt-6 flex items-center gap-2 rounded-medium border border-foreground/10 bg-card px-2.5 py-1.5 text-sm text-foreground/70 focus-within:ring-2 focus-within:ring-ring">
                <svg class="h-4 w-4 shrink-0 text-foreground/40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8" /><path d="m21 21-4.3-4.3" /></svg>
                <input type="text" x-model="query" placeholder="Filter…"
                    class="w-full border-none bg-transparent p-0 text-sm text-foreground placeholder:text-foreground/40 focus:outline-none focus:ring-0" />
                <kbd class="rounded-small border border-foreground/10 bg-secondary px-1.5 font-mono text-[10px] text-foreground/50">⌘K</kbd>
            </label>

            <nav class="mt-6 flex flex-col gap-6">
                @foreach ($categories as $category => $components)
                    <div>
                        <p class="px-2.5 text-[0.7rem] font-medium uppercase tracking-wider text-foreground/40">{{ $category }}</p>
                        <div class="mt-1.5 flex flex-col gap-0.5">
                            @foreach ($components as $component)
                                <a href="{{ $baseUrl }}/{{ $component['name'] }}"
                                    x-show="matches(@js($component['label'].' '.$component['name']))"
                                    @class([
                                        'rounded-medium px-2.5 py-1.5 text-sm font-medium transition',
                                        'bg-secondary text-foreground' => $current === $component['name'],
                                        'text-foreground/60 hover:bg-secondary/60 hover:text-foreground' => $current !== $component['name'],
                                    ])>{{ $component['label'] }}</a>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </nav>

            <a href="https://devdojo.com" target="_blank" rel="noreferrer"
                class="mt-auto flex items-center gap-1.5 px-2.5 pt-8 text-sm text-foreground/50 transition hover:text-foreground">
                devdojo.com
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 7h10v10" /><path d="M7 17 17 7" /></svg>
            </a>
        </aside>

        {{-- ===================== MAIN ===================== --}}
        <main class="min-w-0 flex-1 py-10 lg:py-14">
            {{ $slot }}
        </main>
    </div>

    <script>
        // Shared copy helper for every code block and install snippet.
        window.ddCopy = function (text) {
            if (navigator.clipboard) {
                return navigator.clipboard.writeText(text).catch(() => {});
            }
            const ta = document.createElement('textarea');
            ta.value = text;
            ta.style.position = 'fixed';
            ta.style.opacity = '0';
            document.body.appendChild(ta);
            ta.select();
            document.execCommand('copy');
            ta.remove();
        };
    </script>

    {{-- Syntax highlighting: progressive enhancement, plain <pre> without network. --}}
    <script type="module">
        try {
            const { codeToHtml } = await import('https://esm.sh/shiki@1.29.2');
            const theme = () => document.documentElement.classList.contains('dark') ? 'github-dark' : 'github-light';
            const highlight = async () => {
                for (const pre of document.querySelectorAll('pre[data-studio-code]')) {
                    const code = pre.dataset.rawCode ?? pre.querySelector('code')?.textContent ?? '';
                    pre.dataset.rawCode = code;
                    const html = await codeToHtml(code, { lang: 'blade', theme: theme() });
                    const replacement = document.createElement('div');
                    replacement.innerHTML = html;
                    const shikiPre = replacement.querySelector('pre');
                    shikiPre.className = pre.className;
                    shikiPre.dataset.studioCode = '';
                    shikiPre.dataset.rawCode = code;
                    shikiPre.style.backgroundColor = 'transparent';
                    pre.replaceWith(shikiPre);
                }
            };
            await highlight();
            window.addEventListener('dd-theme-changed', highlight);
            window.addEventListener('dd-code-updated', highlight);
        } catch (e) { /* offline: keep the plain <pre> */ }
    </script>

    @if (class_exists(\Livewire\Livewire::class))
        @livewireScripts
    @endif
</body>

</html>
