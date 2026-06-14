{{-- The segmented control that holds the tab buttons. --}}
<div role="tablist" {{ $attributes->twMerge('inline-flex items-center gap-1 rounded-medium bg-secondary p-1') }}>
    {{ $slot }}
</div>
