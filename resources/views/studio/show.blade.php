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

        {{-- ================= PLAYGROUND TAB ================= --}}
        <div x-show="tab === 'playground'" x-cloak
            x-data="studioPlayground({
                name: @js($meta['name']),
                previewBase: @js($previewBase),
                attrs: @js($attrs),
                slots: @js($slotValues),
                defaults: @js(collect($meta['props'])->mapWithKeys(fn ($p) => [$p['name'] => $p['default'] ?? ''])->all()),
                types: @js(collect($meta['props'])->mapWithKeys(fn ($p) => [$p['name'] => $p['type'] ?? 'text'])->all()),
                height: @js($meta['studio']['height'] ?? '24rem'),
            })"
            class="flex flex-col gap-6">

            {{-- Stage --}}
            <div class="sticky top-0 z-10 flex flex-col overflow-hidden rounded-medium border border-foreground/10 bg-card">
                <div class="flex items-center justify-between gap-3 border-b border-foreground/10 px-3 py-2">
                    <div class="flex items-center gap-1">
                        <button type="button" @click="stageWidth = '375px'" :class="stageWidth === '375px' ? 'bg-secondary text-foreground' : 'text-foreground/40 hover:text-foreground'" class="inline-flex h-7 w-7 items-center justify-center rounded-small transition" aria-label="Mobile width">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="14" height="20" x="5" y="2" rx="2" /><path d="M12 18h.01" /></svg>
                        </button>
                        <button type="button" @click="stageWidth = '768px'" :class="stageWidth === '768px' ? 'bg-secondary text-foreground' : 'text-foreground/40 hover:text-foreground'" class="inline-flex h-7 w-7 items-center justify-center rounded-small transition" aria-label="Tablet width">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="14" x="3" y="5" rx="2" /><path d="M12 17h.01" /></svg>
                        </button>
                        <button type="button" @click="stageWidth = '100%'" :class="stageWidth === '100%' ? 'bg-secondary text-foreground' : 'text-foreground/40 hover:text-foreground'" class="inline-flex h-7 w-7 items-center justify-center rounded-small transition" aria-label="Full width">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="3" rx="2" /><path d="M8 21h8M12 17v4" /></svg>
                        </button>
                    </div>
                    <div class="flex items-center gap-1">
                        <button type="button" @click="stageDark = ! stageDark" class="inline-flex h-7 w-7 items-center justify-center rounded-small text-foreground/40 transition hover:text-foreground" aria-label="Toggle preview theme">
                            <svg x-show="!stageDark" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4" /><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41" /></svg>
                            <svg x-show="stageDark" x-cloak class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z" /></svg>
                        </button>
                        <button type="button" @click="reset()" class="inline-flex h-7 items-center gap-1.5 rounded-small px-2 text-xs font-medium text-foreground/40 transition hover:text-foreground" aria-label="Reset playground">
                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8" /><path d="M3 3v5h5" /></svg>
                            Reset
                        </button>
                    </div>
                </div>
                <div class="flex justify-center bg-[radial-gradient(var(--color-border)_1px,transparent_1px)] [background-size:16px_16px] p-4">
                    <iframe x-ref="stage" title="{{ $meta['label'] }} playground" :src="previewSrc()"
                        class="rounded-small border border-foreground/10 bg-background transition-all"
                        :style="'width: ' + stageWidth + '; height: ' + height"></iframe>
                </div>
            </div>

            {{-- Knobs --}}
            @if (count($meta['props']))
                <div class="overflow-hidden rounded-medium border border-foreground/10 bg-card">
                    <div class="border-b border-foreground/10 bg-secondary/50 px-4 py-2 text-xs font-medium uppercase tracking-wider text-foreground/50">Props</div>
                    @foreach ($meta['props'] as $prop)
                        <x-devdojo-components::studio.knob :prop="$prop" />
                    @endforeach
                </div>
            @endif

            <div class="overflow-hidden rounded-medium border border-foreground/10 bg-card">
                <div class="border-b border-foreground/10 bg-secondary/50 px-4 py-2 text-xs font-medium uppercase tracking-wider text-foreground/50">Slots</div>
                @foreach (array_keys($slotValues) as $slotName)
                    <div class="grid grid-cols-1 items-start gap-2 border-b border-foreground/10 px-4 py-3 last:border-0 sm:grid-cols-[10rem_1fr]">
                        <div class="flex items-center gap-2 pt-1.5">
                            <code class="rounded-small bg-secondary px-1.5 py-0.5 font-mono text-[12px] text-foreground/80">{{ $slotName }}</code>
                            @if ($slotName === 'slot')<span class="text-[10px] text-foreground/40">default</span>@endif
                        </div>
                        <textarea x-model="slots['{{ $slotName }}']" @input.debounce.300ms="update()" rows="2"
                            class="w-full rounded-medium border border-input bg-card px-2.5 py-1.5 font-mono text-xs text-foreground focus:outline-none focus:ring-2 focus:ring-ring"></textarea>
                    </div>
                @endforeach
            </div>

            {{-- Generated code --}}
            <div class="relative overflow-hidden rounded-medium border border-foreground/10 bg-card">
                <div class="flex items-center justify-between border-b border-foreground/10 px-4 py-2">
                    <span class="font-mono text-xs text-foreground/50">blade</span>
                    <button type="button" @click="copy()"
                        class="inline-flex h-7 items-center gap-1.5 rounded-small px-2 text-xs font-medium text-foreground/50 transition hover:text-foreground">
                        <svg x-show="!copied" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="14" height="14" x="8" y="8" rx="2" /><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2" /></svg>
                        <svg x-show="copied" x-cloak class="h-3.5 w-3.5 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5" /></svg>
                        <span x-text="copied ? 'Copied!' : 'Copy'"></span>
                    </button>
                </div>
                <pre class="m-0 max-h-96 overflow-auto p-4 font-mono text-[13px] leading-relaxed text-foreground/90"><code x-text="code">{{ $initialCode }}</code></pre>
            </div>
        </div>
    </div>

    <script>
        function studioPlayground(config) {
            return {
                attrs: config.attrs,
                slots: config.slots,
                code: '',
                copied: false,
                stageDark: document.documentElement.classList.contains('dark'),
                stageWidth: '100%',
                height: config.height,
                _initial: JSON.parse(JSON.stringify({ attrs: config.attrs, slots: config.slots })),
                _timer: null,

                init() {
                    // Booleans arrive as query strings — normalize before binding knobs.
                    for (const key in this.attrs) {
                        if (config.types[key] === 'boolean') {
                            this.attrs[key] = this.attrs[key] === true || this.attrs[key] === 'true';
                        }
                    }
                    this._initial = JSON.parse(JSON.stringify({ attrs: this.attrs, slots: this.slots }));
                    this.generateCode();
                    this.$watch('stageDark', () => this.refresh(true));
                },

                update() {
                    this.generateCode();
                    clearTimeout(this._timer);
                    this._timer = setTimeout(() => this.refresh(), 350);
                    this.syncUrl();
                },

                refresh() {
                    clearTimeout(this._timer);
                    this.$refs.stage.src = this.previewSrc();
                },

                previewSrc() {
                    const params = this.stateParams();
                    params.set('theme', this.stageDark ? 'dark' : 'light');
                    return config.previewBase + '?' + params.toString();
                },

                stateParams() {
                    const params = new URLSearchParams();
                    for (const key in this.attrs) {
                        const value = this.attrs[key];
                        if (value === '' || value === null || value === undefined) continue;
                        params.set('attrs[' + key + ']', typeof value === 'boolean' ? String(value) : value);
                    }
                    for (const key in this.slots) {
                        if (this.slots[key]) params.set('slots[' + key + ']', this.slots[key]);
                    }
                    return params;
                },

                syncUrl() {
                    const params = this.stateParams();
                    params.set('tab', 'playground');
                    history.replaceState(null, '', window.location.pathname + '?' + params.toString());
                },

                copy() {
                    ddCopy(this.code);
                    this.copied = true;
                    setTimeout(() => this.copied = false, 1500);
                },

                reset() {
                    this.attrs = JSON.parse(JSON.stringify(this._initial.attrs));
                    this.slots = JSON.parse(JSON.stringify(this._initial.slots));
                    this.update();
                },

                // ----- code generation: MUST mirror DevDojo\Components\CodeGenerator -----

                escapeHtml(value) {
                    return String(value)
                        .replace(/&/g, '&amp;').replace(/"/g, '&quot;')
                        .replace(/</g, '&lt;').replace(/>/g, '&gt;')
                        .replace(/'/g, '&#039;');
                },

                phpLiteral(value) {
                    if (Array.isArray(value)) {
                        return '[' + value.map(v => this.phpLiteral(v)).join(', ') + ']';
                    }
                    if (value !== null && typeof value === 'object') {
                        return '[' + Object.entries(value).map(([k, v]) =>
                            "'" + this.phpStringEscape(String(k)) + "' => " + this.phpLiteral(v)).join(', ') + ']';
                    }
                    if (typeof value === 'string') return "'" + this.phpStringEscape(value) + "'";
                    if (typeof value === 'boolean') return value ? 'true' : 'false';
                    if (value === null) return 'null';
                    return String(value);
                },

                // Mirrors PHP's addslashes(): backslashes first, then single quotes.
                phpStringEscape(value) {
                    return value.replace(/\\/g, '\\\\').replace(/'/g, "\\'");
                },

                // A raw double quote inside a :key="literal" bound attribute would
                // terminate the attribute early in Blade's component tag compiler —
                // mirrors CodeGenerator::boundAttribute() dropping such attributes.
                boundAttribute(key, literal) {
                    return literal.includes('"') ? null : ':' + key + '="' + literal + '"';
                },

                attributeLine(key, value) {
                    if (value === '' || value === null || value === undefined || value === false || value === 'false') return null;
                    if (value === true || value === 'true') return key;
                    if (Array.isArray(value) || (typeof value === 'object' && value !== null)) {
                        return this.boundAttribute(key, this.phpLiteral(value));
                    }
                    if (typeof value === 'string' && (value.startsWith('[') || value.startsWith('{'))) {
                        try { return this.boundAttribute(key, this.phpLiteral(JSON.parse(value))); } catch (e) { /* fall through */ }
                    }
                    return key + '="' + this.escapeHtml(value) + '"';
                },

                generateCode() {
                    const tag = 'x-' + config.name;
                    const attrLines = [];

                    for (const key in this.attrs) {
                        // Omit values equal to the prop default so the snippet stays minimal.
                        if (String(this.attrs[key]) === String(config.defaults[key] ?? '')) continue;
                        const line = this.attributeLine(key, this.attrs[key]);
                        if (line !== null) attrLines.push('    ' + line);
                    }

                    const open = attrLines.length === 0 ? '<' + tag : '<' + tag + '\n' + attrLines.join('\n') + '\n';
                    const defaultSlot = (this.slots['slot'] || '').trim();
                    const named = Object.entries(this.slots)
                        .filter(([name, content]) => name !== 'slot' && (content || '').trim() !== '');

                    if (!defaultSlot && named.length === 0) {
                        this.code = open + (attrLines.length === 0 ? ' />' : '/>');
                        return;
                    }

                    // Concatenated below so Blade's ComponentTagCompiler never sees the
                    // raw slot-tag token in this file's source (see brief note).
                    const slotOpen = '<' + 'x-slot:';
                    const slotClose = '</' + 'x-slot:';

                    const lines = [open + '>'];
                    if (defaultSlot) lines.push('    ' + defaultSlot);
                    for (const [name, content] of named) {
                        lines.push('    ' + slotOpen + name + '>' + content.trim() + slotClose + name + '>');
                    }
                    lines.push('</' + tag + '>');
                    this.code = lines.join('\n');
                }
            };
        }
    </script>
</x-devdojo-components::studio.layout>
