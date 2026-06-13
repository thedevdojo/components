@props([
    'size' => 'md',
])

@php
    switch ($size ?? 'md') {
        case 'sm':
            $sizeClasses = 'rounded-small p-4 text-sm';
            break;
        case 'md':
            $sizeClasses = 'rounded-medium p-5 text-sm';
            break;
        case 'lg':
            $sizeClasses = 'rounded-large p-6 text-base';
            break;
        default:
            $sizeClasses = 'rounded-medium p-5 text-sm';
            break;
    }
@endphp

<div {{ $attributes->twMerge($sizeClasses . ' bg-card text-card-foreground border border-foreground/10 dark:border-foreground/12 shadow-xs w-full') }}>
    @isset($header)
        <div {{ $attributes->twMergeFor('header', 'mb-3 flex flex-col gap-1') }}>
            {{ $header }}
        </div>
    @endisset

    {{ $slot }}

    @isset($footer)
        <div {{ $attributes->twMergeFor('footer', 'mt-4 flex items-center gap-2') }}>
            {{ $footer }}
        </div>
    @endisset
</div>
