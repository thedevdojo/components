@props([
    'code',
    'label' => null,
])

<div class="relative overflow-hidden rounded-medium border border-foreground/10 bg-card">
    @if ($label)
        <div class="border-b border-foreground/10 px-4 py-2 font-mono text-xs text-foreground/50">{{ $label }}</div>
    @endif

    <button type="button"
        x-data="{ copied: false }"
        @click="ddCopy($el.closest('div.relative').querySelector('pre').dataset.rawCode ?? $el.closest('div.relative').querySelector('code').textContent); copied = true; setTimeout(() => copied = false, 1500)"
        class="absolute top-2 right-2 z-10 inline-flex h-8 w-8 items-center justify-center rounded-small border border-foreground/10 bg-background/80 text-foreground/60 backdrop-blur transition hover:text-foreground"
        aria-label="Copy code">
        <svg x-show="!copied" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="14" height="14" x="8" y="8" rx="2" /><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2" /></svg>
        <svg x-show="copied" x-cloak class="h-3.5 w-3.5 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5" /></svg>
    </button>

    <pre data-studio-code {{ $attributes->twMerge('m-0 max-h-96 overflow-auto p-4 font-mono text-[13px] leading-relaxed text-foreground/90') }}><code>{{ $code }}</code></pre>
</div>
