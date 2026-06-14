@props([
    'value' => 0,
    'max' => 100,
    'size' => 'md',
    'indeterminate' => false,
])

@php
    $pct = (float) $max > 0 ? min(100, max(0, ((float) $value / (float) $max) * 100)) : 0;
    $track = match ($size) {
        'sm' => 'h-1',
        'lg' => 'h-3',
        default => 'h-2',
    };
@endphp

@once
    <style>
        @keyframes dd-progress-indeterminate {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(400%); }
        }
        .dd-progress-bar-indeterminate { animation: dd-progress-indeterminate 1.2s ease-in-out infinite; }
    </style>
@endonce

<div
    role="progressbar"
    aria-valuemin="0"
    aria-valuemax="{{ $max }}"
    @unless ($indeterminate) aria-valuenow="{{ $value }}" @endunless
    {{ $attributes->twMerge('w-full') }}
>
    <div class="w-full overflow-hidden rounded-full bg-secondary {{ $track }}">
        @if ($indeterminate)
            <div class="dd-progress-bar-indeterminate h-full w-1/3 rounded-full bg-primary"></div>
        @else
            <div class="h-full rounded-full bg-primary transition-[width] duration-300 ease-out" style="width: {{ $pct }}%"></div>
        @endif
    </div>
</div>
