@props([
    'title' => '',
    'subtitle' => '',
])

<div class="flex items-end justify-between border-b border-foreground/10 pb-4">
    <div>
        <h2 class="text-2xl font-semibold tracking-tight">{{ $title }}</h2>
        @if ($subtitle)
            <p class="mt-1 text-sm text-foreground/55">{{ $subtitle }}</p>
        @endif
    </div>
</div>
