@props(['component'])

@php
    $baseUrl = rtrim(route('devdojo-components.showcase'), '/');
    $previewMarkup = $component['studio']['card']
        ?? '<x-components.'.$component['name'].' />';

    // One misbehaving component must not 500 the whole index grid.
    try {
        $previewHtml = \Illuminate\Support\Facades\Blade::render($previewMarkup);
    } catch (\Throwable $e) {
        $previewHtml = '<p class="text-xs text-foreground/40">Preview unavailable</p>';
    }
@endphp

<div class="flex flex-col overflow-hidden rounded-large border border-foreground/10 bg-card shadow-xs transition hover:border-foreground/20">
    <div class="flex items-center justify-between gap-3 border-b border-foreground/10 px-5 py-4">
        <div class="min-w-0">
            <div class="flex items-center gap-2">
                <a href="{{ $baseUrl }}/{{ $component['name'] }}" class="truncate font-semibold text-foreground hover:underline">
                    {{ $component['label'] }}
                </a>
                <code class="rounded-small bg-secondary px-1.5 py-0.5 font-mono text-[11px] text-foreground/60">x-{{ $component['name'] }}</code>
            </div>
            @if ($component['description'])
                <p class="mt-0.5 line-clamp-1 text-sm text-foreground/55">{{ $component['description'] }}</p>
            @endif
        </div>

        @if (class_exists(\Livewire\Livewire::class))
            <livewire:devdojo-components.card :name="$component['name']" :key="'card-'.$component['name']" />
        @else
            {{-- Fallback when Livewire isn't installed: copy the add command. --}}
            <button type="button"
                x-data="{ copied: false }"
                @click="navigator.clipboard.writeText('php artisan components:add {{ $component['name'] }}'); copied = true; setTimeout(() => copied = false, 1500)"
                class="inline-flex shrink-0 items-center gap-1.5 self-start rounded-medium border border-foreground/10 bg-background px-2.5 py-1.5 font-mono text-xs text-foreground/70 transition hover:bg-secondary hover:text-foreground">
                <template x-if="!copied">
                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="14" height="14" x="8" y="8" rx="2" /><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2" /></svg>
                </template>
                <template x-if="copied">
                    <svg class="h-3.5 w-3.5 text-green-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5" /></svg>
                </template>
                <span x-text="copied ? 'Copied!' : 'add {{ $component['name'] }}'"></span>
            </button>
        @endif
    </div>

    <div class="flex min-h-[9rem] flex-1 items-center justify-center bg-[radial-gradient(var(--color-border)_1px,transparent_1px)] [background-size:16px_16px] p-6">
        <div class="flex w-full max-w-md flex-col items-center">
            {!! $previewHtml !!}
        </div>
    </div>

    <a href="{{ $baseUrl }}/{{ $component['name'] }}"
        class="flex items-center justify-between border-t border-foreground/10 px-5 py-2.5 text-sm font-medium text-foreground/60 transition hover:bg-secondary/60 hover:text-foreground">
        Docs &amp; playground
        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14" /><path d="m12 5 7 7-7 7" /></svg>
    </a>
</div>
