@props([
    'direction' => 'horizontal',
    'gutterSize' => 8,
    'minSize' => 100,
])

@php
    $baseClasses = 'flex h-full w-full items-stretch justify-stretch';
@endphp

@once
    {{-- Split.js powers the drag-to-resize behavior; it loads once from a CDN. --}}
    <script src="https://cdn.jsdelivr.net/npm/split.js@1.6.5/dist/split.min.js" defer></script>
@endonce

<div
    x-data="{
        splitInstance: null,
        direction: '{{ $direction }}',
        _splitRetries: 0,

        initializeSplitter() {
            // Split.js loads async from the CDN above, while Alpine fires
            // x-init synchronously — so the first call can run before
            // window.Split exists. Poll briefly until it does, then build.
            if (typeof window.Split === 'undefined') {
                this._splitRetries = (this._splitRetries || 0) + 1;
                if (this._splitRetries < 100) {
                    return setTimeout(() => this.initializeSplitter(), 50);
                }
                console.warn('Split.js unavailable after retry; splitter init aborted.');
                return;
            }
            this._splitRetries = 0;

            const panes = [...this.$el.querySelectorAll(':scope > .dd-split-pane')];
            if (panes.length < 2) return;

            const sizes = panes.map(p => parseFloat(p.dataset.size) || (100 / panes.length));

            // Tear down any prior instance so re-init swaps orientations cleanly.
            if (this.splitInstance) {
                try { this.splitInstance.destroy(); } catch (e) {}
                this.splitInstance = null;
            }
            // Strip inline sizes / stray gutters a previous instance left behind.
            panes.forEach(p => { p.style.removeProperty('width'); p.style.removeProperty('height'); });
            this.$el.querySelectorAll(':scope > .gutter').forEach(g => g.remove());

            this.splitInstance = Split(panes, {
                direction: this.direction,
                sizes: sizes,
                gutterSize: {{ (int) $gutterSize }},
                minSize: {{ (int) $minSize }},
                gutter: (index, gutterDirection) => {
                    const gutter = document.createElement('div');
                    gutter.setAttribute('wire:ignore', '');
                    gutter.className = `gutter gutter-${gutterDirection}`;
                    return gutter;
                },
                onDragStart: (sizes, gutter) => {
                    if (gutter && gutter.classList) gutter.classList.add('gutter-dragging');
                    document.body.classList.add('gutter-dragging-body');
                    if (gutter && gutter.classList && gutter.classList.contains('gutter-vertical')) {
                        document.body.classList.add('gutter-dragging-vertical');
                    }
                },
                onDragEnd: (sizes, gutter) => {
                    if (gutter && gutter.classList) gutter.classList.remove('gutter-dragging');
                    document.body.classList.remove('gutter-dragging-body', 'gutter-dragging-vertical');
                },
            });
        },
    }"
    x-init="initializeSplitter()"
    @splitter-init.window="initializeSplitter()"
    @splitter-set-direction.window="
        const d = $event.detail?.direction;
        if ((d === 'horizontal' || d === 'vertical') && d !== direction) {
            direction = d;
            $nextTick(() => initializeSplitter());
        }
    "
    :class="direction === 'vertical' ? 'flex-col' : ''"
    {{ $attributes->twMerge($baseClasses) }}
    wire:ignore.self
>
    {{ $slot }}
</div>

@once
    <style>
        /*
         * Gutter: a wide invisible hit zone with a hairline painted in the
         * middle. The gutter element is transparent and sized to the gutterSize
         * prop (Split.js relies on that for pane math); the visible line is a
         * centered ::after that grows on hover/drag via transform (paint-only,
         * so Split.js never sees a size delta). ::before extends the clickable
         * area a few px past the gutter on each side.
         *
         * Colors use theme tokens, so the seam adapts to dark mode for free.
         */
        .gutter {
            position: relative;
            z-index: 5;
            background: transparent;
        }
        .gutter::before,
        .gutter::after {
            content: '';
            position: absolute;
        }
        .gutter::before {
            pointer-events: auto;
        }
        .gutter::after {
            pointer-events: none;
            background-color: var(--border);
            transform-origin: center center;
            transition: background-color 150ms ease, transform 150ms ease;
        }

        /* Horizontal: a vertical seam between side-by-side panes. */
        .gutter.gutter-horizontal {
            cursor: col-resize;
            width: {{ (int) $gutterSize }}px;
        }
        .gutter.gutter-horizontal::before {
            top: 0; bottom: 0; left: -6px; right: -6px;
        }
        .gutter.gutter-horizontal::after {
            top: 0; bottom: 0; left: 50%; width: 1px;
            transform: translateX(-50%);
        }
        .gutter.gutter-horizontal:hover::after {
            transform: translateX(-50%) scaleX(3);
            background-color: var(--muted-foreground);
        }
        .gutter.gutter-horizontal.gutter-dragging::after {
            transform: translateX(-50%) scaleX(3);
            background-color: var(--primary);
        }

        /* Vertical: a horizontal seam between stacked panes. */
        .gutter.gutter-vertical {
            cursor: row-resize;
            height: {{ (int) $gutterSize }}px;
        }
        .gutter.gutter-vertical::before {
            left: 0; right: 0; top: -6px; bottom: -6px;
        }
        .gutter.gutter-vertical::after {
            left: 0; right: 0; top: 50%; height: 1px;
            transform: translateY(-50%);
        }
        .gutter.gutter-vertical:hover::after {
            transform: translateY(-50%) scaleY(3);
            background-color: var(--muted-foreground);
        }
        .gutter.gutter-vertical.gutter-dragging::after {
            transform: translateY(-50%) scaleY(3);
            background-color: var(--primary);
        }

        /* While dragging, suppress text selection and hold the resize cursor
           even when the pointer briefly leaves the gutter. */
        .gutter-dragging-body { user-select: none; cursor: col-resize; }
        .gutter-dragging-body.gutter-dragging-vertical { cursor: row-resize; }
    </style>
@endonce
