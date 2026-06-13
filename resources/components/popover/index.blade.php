@props([
    'position' => 'bottom',
    'align' => 'left',
    'gap' => '2',
])

<div x-data="{
    popoverOpen: false,
    triggerPosition: { top: 0, left: 0, width: 0, height: 0 },

    init() {
        this.$watch('popoverOpen', value => {
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

    getPopoverStyles() {
        let top = this.triggerPosition.top;
        let left = this.triggerPosition.left;
        let transform = '';
        let transformOrigin = '';
        let transformX = '';
        let transformY = '';

        if ('{{ $position }}' === 'bottom') {
            top += this.triggerPosition.height + {{ (int) $gap * 4 }};
        } else if ('{{ $position }}' === 'top') {
            top -= {{ (int) $gap * 4 }};
            transformY = 'translateY(-100%)';
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

        transform = [transformX, transformY].filter(t => t).join(' ');

        if ('{{ $position }}' === 'bottom') {
            transformOrigin = 'top left';
        } else if ('{{ $position }}' === 'top') {
            transformOrigin = 'bottom left';
        } else if ('{{ $position }}' === 'right' || '{{ $position }}' === 'left') {
            transformOrigin = 'bottom left';
        }

        return {
            position: 'absolute',
            top: top + 'px',
            left: left + 'px',
            transform: transform,
            transformOrigin: transformOrigin,
            zIndex: 50
        };
    },

    openPopover() {
        this.popoverOpen = true;
    }
}"
x-modelable="popoverOpen"
@resize.window="if (popoverOpen) calculateTriggerPosition()"
@scroll.window="if (popoverOpen) calculateTriggerPosition()"
@class([
    'relative inline-flex w-auto items-start',
    'flex-col' => $position === 'bottom' || $position === 'top',
    'flex-row' => $position === 'right' || $position === 'left',
])
{{ $attributes }}>

    <div x-ref="trigger" x-on:click="openPopover()">
        @if (isset($trigger))
            {{ $trigger }}
        @else
            <div class="relative inline-flex items-center justify-center rounded-medium border border-foreground/10 bg-background p-2 text-sm font-medium text-foreground transition-colors hover:bg-secondary focus:outline-none disabled:pointer-events-none disabled:opacity-50">
                Popover
            </div>
        @endif
    </div>

    <template x-teleport="body">
        <div
            x-show="popoverOpen"
            x-on:click.away="popoverOpen = false"
            x-on:keydown.escape.window="popoverOpen = false"
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            :style="getPopoverStyles()"
            x-cloak
            class="w-max max-w-sm">
            @if (isset($content))
                <div {{ $content->attributes->twMerge('overflow-hidden rounded-medium border border-foreground/10 bg-popover p-4 text-popover-foreground shadow-lg dark:border-foreground/15') }}>
                    {{ $content }}
                </div>
            @else
                {{ $slot }}
            @endif
        </div>
    </template>
</div>
