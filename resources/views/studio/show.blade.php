{{--
    Alias $component to $meta before the layout tag opens: Blade's component-tag
    compiler reassigns the literal variable $component to an internal
    AnonymousComponent instance for the entire body of the tag (restored only
    after it closes), which would otherwise shadow the $component array the
    controller passes in.
--}}
@php
    $meta = $component;
@endphp

<x-devdojo-components::studio.layout
    :categories="$categories"
    :current="$meta['name']"
    :title="$meta['label'].' — DevDojo Components'">

    @php
        $previewBase = route('devdojo-components.preview', ['name' => $meta['name']]);
    @endphp

    <div x-data="{
            tab: new URLSearchParams(window.location.search).get('tab') === 'playground' ? 'playground' : 'docs',
            setTab(value) {
                this.tab = value;
                const params = new URLSearchParams(window.location.search);
                value === 'docs' ? params.delete('tab') : params.set('tab', value);
                history.replaceState(null, '', window.location.pathname + (params.toString() ? '?' + params.toString() : ''));
            }
        }"
        class="flex flex-col gap-8">

        {{-- ================= HEADER ================= --}}
        <div class="flex flex-col gap-4 border-b border-foreground/10 pb-8">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-3xl font-bold tracking-tight">{{ $meta['label'] }}</h1>
                        <span class="rounded-full border border-foreground/10 bg-secondary px-2.5 py-0.5 text-xs font-medium text-foreground/60">{{ $meta['category'] }}</span>
                    </div>
                    @if ($meta['description'])
                        <p class="mt-2 max-w-2xl text-foreground/60">{{ $meta['description'] }}</p>
                    @endif
                </div>
                @if (class_exists(\Livewire\Livewire::class))
                    <livewire:devdojo-components.card :name="$meta['name']" :key="'show-'.$meta['name']" />
                @endif
            </div>

            {{-- Install snippet --}}
            <div class="flex max-w-xl items-center justify-between gap-3 rounded-medium border border-foreground/10 bg-card px-4 py-2.5"
                x-data="{ copied: false }">
                <code class="truncate font-mono text-[13px] text-foreground/80">php artisan components:add {{ $meta['name'] }}</code>
                <button type="button"
                    @click="ddCopy('php artisan components:add {{ $meta['name'] }}'); copied = true; setTimeout(() => copied = false, 1500)"
                    class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-small text-foreground/50 transition hover:text-foreground" aria-label="Copy install command">
                    <svg x-show="!copied" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="14" height="14" x="8" y="8" rx="2" /><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2" /></svg>
                    <svg x-show="copied" x-cloak class="h-3.5 w-3.5 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5" /></svg>
                </button>
            </div>

            {{-- Tabs --}}
            <div class="flex items-center gap-1">
                <button type="button" @click="setTab('docs')"
                    :class="tab === 'docs' ? 'bg-secondary text-foreground' : 'text-foreground/60 hover:text-foreground'"
                    class="rounded-medium px-3.5 py-1.5 text-sm font-medium transition">Docs</button>
                <button type="button" @click="setTab('playground')"
                    :class="tab === 'playground' ? 'bg-secondary text-foreground' : 'text-foreground/60 hover:text-foreground'"
                    class="rounded-medium px-3.5 py-1.5 text-sm font-medium transition">Playground</button>
            </div>
        </div>

        {{-- ================= DOCS TAB ================= --}}
        <div x-show="tab === 'docs'" class="flex flex-col gap-12">

            @forelse ($examples as $example)
                <section class="flex flex-col gap-3" x-data="{ view: 'preview' }">
                    <div class="flex flex-wrap items-end justify-between gap-3">
                        <div>
                            <h2 class="text-lg font-semibold tracking-tight">{{ $example['title'] }}</h2>
                            @if (! empty($example['description']))
                                <p class="mt-0.5 text-sm text-foreground/55">{{ $example['description'] }}</p>
                            @endif
                        </div>
                        <div class="flex items-center gap-1 rounded-medium border border-foreground/10 bg-card p-0.5">
                            <button type="button" @click="view = 'preview'"
                                :class="view === 'preview' ? 'bg-secondary text-foreground' : 'text-foreground/50 hover:text-foreground'"
                                class="rounded-small px-2.5 py-1 text-xs font-medium transition">Preview</button>
                            <button type="button" @click="view = 'code'"
                                :class="view === 'code' ? 'bg-secondary text-foreground' : 'text-foreground/50 hover:text-foreground'"
                                class="rounded-small px-2.5 py-1 text-xs font-medium transition">Code</button>
                        </div>
                    </div>

                    <div x-show="view === 'preview'">
                        <iframe title="{{ $example['title'] }} preview" loading="lazy"
                            :src="'{{ $previewBase }}?example={{ Str::of($example['file'])->basename('.blade.php') }}&theme=' + (dark ? 'dark' : 'light')"
                            class="w-full rounded-medium border border-foreground/10 bg-card"
                            style="height: {{ $example['height'] ?? '16rem' }};"></iframe>
                    </div>
                    <div x-show="view === 'code'" x-cloak>
                        <x-devdojo-components::studio.code-block :code="$example['code']" />
                    </div>
                </section>
            @empty
                <section class="flex flex-col gap-3">
                    <h2 class="text-lg font-semibold tracking-tight">Preview</h2>
                    <iframe title="{{ $meta['label'] }} preview" loading="lazy"
                        :src="'{{ $previewBase }}?theme=' + (dark ? 'dark' : 'light')"
                        class="h-64 w-full rounded-medium border border-foreground/10 bg-card"></iframe>
                </section>
            @endforelse

            {{-- Reference tables --}}
            @if (count($meta['props']))
                <section class="flex flex-col gap-3">
                    <h2 class="text-lg font-semibold tracking-tight">Props</h2>
                    <div class="overflow-x-auto rounded-medium border border-foreground/10">
                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr class="border-b border-foreground/10 bg-secondary/50 text-xs uppercase tracking-wider text-foreground/50">
                                    <th class="px-4 py-2.5 font-medium">Prop</th>
                                    <th class="px-4 py-2.5 font-medium">Type</th>
                                    <th class="px-4 py-2.5 font-medium">Default</th>
                                    <th class="px-4 py-2.5 font-medium">Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($meta['props'] as $prop)
                                    <tr class="border-b border-foreground/10 last:border-0">
                                        <td class="px-4 py-2.5"><code class="rounded-small bg-secondary px-1.5 py-0.5 font-mono text-[12px] text-foreground/80">{{ $prop['name'] }}</code></td>
                                        <td class="px-4 py-2.5 font-mono text-[12px] text-foreground/60">
                                            {{ $prop['type'] ?? 'text' }}@if (! empty($prop['options'])): {{ implode(' | ', array_map(fn ($o) => is_bool($o) ? var_export($o, true) : $o, $prop['options'])) }}@endif
                                        </td>
                                        <td class="px-4 py-2.5 font-mono text-[12px] text-foreground/50">{{ is_bool($prop['default'] ?? null) ? var_export($prop['default'], true) : (($prop['default'] ?? '') === '' ? '—' : (is_array($prop['default']) ? json_encode($prop['default']) : $prop['default'])) }}</td>
                                        <td class="px-4 py-2.5 text-foreground/60">{{ $prop['description'] ?? '' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>
            @endif

            @if (count($meta['slots']))
                <section class="flex flex-col gap-3">
                    <h2 class="text-lg font-semibold tracking-tight">Slots</h2>
                    <div class="overflow-x-auto rounded-medium border border-foreground/10">
                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr class="border-b border-foreground/10 bg-secondary/50 text-xs uppercase tracking-wider text-foreground/50">
                                    <th class="px-4 py-2.5 font-medium">Slot</th>
                                    <th class="px-4 py-2.5 font-medium">Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($meta['slots'] as $slot)
                                    <tr class="border-b border-foreground/10 last:border-0">
                                        <td class="px-4 py-2.5"><code class="rounded-small bg-secondary px-1.5 py-0.5 font-mono text-[12px] text-foreground/80">{{ $slot['name'] }}</code>@if ($slot['name'] === 'slot') <span class="text-[11px] text-foreground/40">(default)</span>@endif</td>
                                        <td class="px-4 py-2.5 text-foreground/60">{{ $slot['description'] ?? '' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>
            @endif
        </div>

        {{-- ================= PLAYGROUND TAB (built in Task 6) ================= --}}
        <div x-show="tab === 'playground'" x-cloak class="flex flex-col gap-6">
            <p class="text-sm text-foreground/50">Playground coming in Task 6.</p>
        </div>
    </div>
</x-devdojo-components::studio.layout>
