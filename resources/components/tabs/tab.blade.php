@props([
    'tab' => null,
])

{{-- A single tab button. The first one rendered becomes active by default. --}}
<button
    type="button"
    role="tab"
    x-init="if (activeTab === null) activeTab = @js($tab)"
    @click="activeTab = @js($tab)"
    :aria-selected="activeTab === @js($tab) ? 'true' : 'false'"
    :class="activeTab === @js($tab) ? 'bg-background text-foreground shadow-xs' : 'text-foreground/60 hover:text-foreground'"
    {{ $attributes->twMerge('inline-flex cursor-pointer items-center justify-center gap-2 whitespace-nowrap rounded-small px-3 py-1.5 text-sm font-medium transition') }}
>
    {{ $slot }}
</button>
