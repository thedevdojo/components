@props([
    'tab' => null,
])

{{-- The content shown while its matching tab is active. --}}
<div
    role="tabpanel"
    x-show="activeTab === @js($tab)"
    x-cloak
    {{ $attributes->twMerge('mt-3') }}
>
    {{ $slot }}
</div>
