@props([
    'name' => '',
    'title' => '',
    'description' => '',
])

<div class="overflow-hidden rounded-large border border-foreground/10 bg-card shadow-xs">
    <div class="flex flex-col gap-3 border-b border-foreground/10 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2">
                <h3 class="font-semibold text-foreground">{{ $title }}</h3>
                <code class="rounded bg-secondary px-1.5 py-0.5 font-mono text-[11px] text-foreground/60">x-{{ $name }}</code>
            </div>
            @if ($description)
                <p class="mt-0.5 text-sm text-foreground/55">{{ $description }}</p>
            @endif
        </div>

        @if (class_exists(\Livewire\Livewire::class))
            {{-- Publishes the component into resources/views/components and tracks its state. --}}
            <livewire:devdojo-components.card :name="$name" :key="$name" />
        @else
            {{-- Fallback when Livewire isn't installed: copy the add command. --}}
            <button type="button"
                x-data="{ copied: false }"
                @click="ddCopy('php artisan components:add {{ $name }}'); copied = true; setTimeout(() => copied = false, 1500)"
                class="inline-flex shrink-0 items-center gap-1.5 self-start rounded-md border border-foreground/10 bg-background px-2.5 py-1.5 font-mono text-xs text-foreground/70 transition hover:bg-secondary hover:text-foreground">
                <template x-if="!copied">
                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="14" height="14" x="8" y="8" rx="2" /><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2" /></svg>
                </template>
                <template x-if="copied">
                    <svg class="h-3.5 w-3.5 text-green-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5" /></svg>
                </template>
                <span x-text="copied ? 'Copied!' : 'add {{ $name }}'"></span>
            </button>
        @endif
    </div>

    <div class="flex min-h-[7rem] items-center justify-center bg-[radial-gradient(var(--color-border)_1px,transparent_1px)] [background-size:16px_16px] p-8">
        <div class="flex w-full max-w-2xl flex-col items-center">
            {{ $slot }}
        </div>
    </div>

    @php($example = \DevDojo\Components\Components::get($name)['example'] ?? null)
    @if ($example)
        {{-- The simplest published-form usage, with a one-click copy. --}}
        <div class="group/code relative border-t border-foreground/10 bg-secondary/40"
            x-data="{ copied: false, code: @js($example) }">
            <button type="button" aria-label="Copy code"
                @click="ddCopy(code); copied = true; setTimeout(() => copied = false, 1500)"
                class="absolute right-2.5 top-2.5 z-10 inline-flex h-8 w-8 items-center justify-center rounded-small border border-foreground/10 bg-card/80 text-foreground/55 shadow-xs backdrop-blur transition hover:bg-card hover:text-foreground">
                <svg x-show="!copied" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="14" height="14" x="8" y="8" rx="2" /><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2" /></svg>
                <svg x-show="copied" x-cloak class="h-3.5 w-3.5 text-green-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5" /></svg>
            </button>
            <pre class="dd-syntax overflow-x-auto px-5 py-4 pr-12 text-[13px] leading-relaxed"><code class="font-mono text-foreground/85">{!! \DevDojo\Components\CodeHighlighter::highlight($example) !!}</code></pre>
        </div>
    @endif
</div>
