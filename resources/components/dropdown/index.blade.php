@props([
    'position' => 'bottom',
    'align' => 'left',
    'gap' => '2',
])

<div x-data="{
    dropdownOpen: false,
    triggerPosition: { top: 0, left: 0, width: 0, height: 0 },

    init() {
        this.$watch('dropdownOpen', value => {
            if (value) {
                this.calculateTriggerPosition();
            }
        });
    },

    calculateTriggerPosition() {
        const triggerRect = this.$refs.trigger.getBoundingClientRect();
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        const scrollLeft = window.pageXOffset || document.documentElement.scrollLeft;

        this.triggerPosition = {
            top: triggerRect.top + scrollTop,
            left: triggerRect.left + scrollLeft,
            width: triggerRect.width,
            height: triggerRect.height
        };
    },

    getDropdownStyles() {
        let top = this.triggerPosition.top;
        let left = this.triggerPosition.left;
        let transformX = '';
        let transformY = '';
        let transformOrigin = 'top left';

        if ('{{ $position }}' === 'bottom') {
            top += this.triggerPosition.height + {{ (int) $gap * 4 }};
        } else if ('{{ $position }}' === 'top') {
            top -= {{ (int) $gap * 4 }};
            transformY = 'translateY(-100%)';
            transformOrigin = 'bottom left';
        } else if ('{{ $position }}' === 'right') {
            left += this.triggerPosition.width + {{ (int) $gap * 4 }};
        } else if ('{{ $position }}' === 'left') {
            left -= {{ (int) $gap * 4 }};
            transformX = 'translateX(-100%)';
        }

        if ('{{ $align }}' === 'center' && ('{{ $position }}' === 'top' || '{{ $position }}' === 'bottom')) {
            left += (this.triggerPosition.width / 2);
            transformX = 'translateX(-50%)';
        } else if ('{{ $align }}' === 'right' && ('{{ $position }}' === 'top' || '{{ $position }}' === 'bottom')) {
            left += this.triggerPosition.width;
            transformX = 'translateX(-100%)';
        } else if ('{{ $align }}' === 'center' && ('{{ $position }}' === 'right' || '{{ $position }}' === 'left')) {
            top += (this.triggerPosition.height / 2);
            transformY = 'translateY(-50%)';
        } else if ('{{ $align }}' === 'bottom' && ('{{ $position }}' === 'right' || '{{ $position }}' === 'left')) {
            top += this.triggerPosition.height;
            transformY = 'translateY(-100%)';
        }

        return {
            position: 'absolute',
            top: top + 'px',
            left: left + 'px',
            transform: [transformX, transformY].filter(t => t).join(' '),
            transformOrigin: transformOrigin,
            zIndex: 50
        };
    },

    openDropdown() {
        this.dropdownOpen = true;
    }
}"
@resize.window="if (dropdownOpen) calculateTriggerPosition()"
@scroll.window="if (dropdownOpen) calculateTriggerPosition()"
@class([
    'relative inline-flex w-auto items-start',
    'flex-col' => $position === 'bottom' || $position === 'top',
    'flex-row' => $position === 'right' || $position === 'left',
])>

    <div x-ref="trigger" x-on:click="openDropdown()">
        @if (isset($trigger))
            {{ $trigger }}
        @else
            <div class="relative inline-flex items-center justify-center rounded-medium border border-foreground/10 bg-background p-2 text-sm font-medium text-foreground transition-colors hover:bg-secondary focus:outline-none disabled:pointer-events-none disabled:opacity-50">
                Menu
            </div>
        @endif
    </div>

    {{-- Teleported to <body> so the menu is never clipped by an overflow-hidden
         or scrollable ancestor (cards, tables, the showcase preview, etc.). --}}
    <template x-teleport="body">
        <div x-show="dropdownOpen"
            x-on:click.away="dropdownOpen = false"
            x-on:keydown.escape.window="dropdownOpen = false"
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            :style="getDropdownStyles()"
            x-cloak
            class="w-max">
            @if (isset($menu))
                <div {{ $menu->attributes->twMerge('min-w-44 overflow-hidden rounded-medium border border-foreground/10 bg-popover p-1 text-popover-foreground shadow-lg dark:border-foreground/15') }}>
                    {{ $menu }}
                </div>
            @else
                {{ $slot }}
            @endif
        </div>
    </template>
</div>
