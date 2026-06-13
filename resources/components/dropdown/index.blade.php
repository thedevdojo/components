@props([
    'position' => 'bottom',
    'align' => 'left',
    'gap' => '2',
])

<div x-data="{ dropdownOpen: false }"
    @class([
        'relative inline-flex w-auto items-start',
        'flex-col' => $position === 'bottom' || $position === 'top',
        'flex-row' => $position === 'right' || $position === 'left',
    ])>

    <div x-on:click="dropdownOpen = ! dropdownOpen">
        @if (isset($trigger))
            {{ $trigger }}
        @else
            <div class="relative inline-flex items-center justify-center rounded-medium border border-foreground/10 bg-background p-2 text-sm font-medium text-foreground transition-colors hover:bg-secondary focus:outline-none disabled:pointer-events-none disabled:opacity-50">
                Menu
            </div>
        @endif
    </div>

    <div class="relative w-full max-w-md">
        <div x-show="dropdownOpen"
            x-on:click.away="dropdownOpen = false"
            x-on:keydown.escape.window="dropdownOpen = false"
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
            @class([
                'absolute z-50 w-max',
                'left-0' => $align === 'left',
                'right-0' => $align === 'right',
                'left-1/2 -translate-x-1/2' => $align === 'center',
            ])
            @style([
                'margin-top: ' . ((int) $gap * 0.25) . 'rem' => $position === 'bottom',
                'margin-bottom: ' . ((int) $gap * 0.25) . 'rem; bottom: 100%' => $position === 'top',
                'margin-left: ' . ((int) $gap * 0.25) . 'rem; left: 100%; top: 0' => $position === 'right',
                'margin-right: ' . ((int) $gap * 0.25) . 'rem; right: 100%; top: 0' => $position === 'left',
            ])
            x-cloak>
            @if (isset($menu))
                <div {{ $menu->attributes->twMerge('min-w-44 overflow-hidden rounded-medium border border-foreground/10 bg-popover p-1 text-popover-foreground shadow-lg dark:border-foreground/15') }}>
                    {{ $menu }}
                </div>
            @else
                {{ $slot }}
            @endif
        </div>
    </div>
</div>
