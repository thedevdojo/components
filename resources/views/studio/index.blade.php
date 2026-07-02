<x-devdojo-components::studio.layout :categories="$categories">
    <div class="border-b border-foreground/10 pb-10">
        <span class="inline-flex items-center gap-1.5 rounded-full border border-foreground/10 bg-secondary px-3 py-1 text-xs font-medium text-foreground/70">
            <span class="h-1.5 w-1.5 rounded-full bg-primary"></span>
            v1 · Blade + Alpine
        </span>
        <h1 class="mt-4 text-3xl font-bold tracking-tight sm:text-4xl">DevDojo Components</h1>
        <p class="mt-3 max-w-2xl text-foreground/60">
            Copy-paste Blade components you own. Browse, tune the props in a live playground,
            then publish any component straight into <code class="rounded-small bg-secondary px-1.5 py-0.5 font-mono text-[12px]">resources/views/components</code>.
        </p>
    </div>

    <div class="flex flex-col gap-14 pt-10">
        @foreach ($categories as $category => $components)
            <section id="{{ Str::slug($category) }}" class="scroll-mt-28 lg:scroll-mt-10">
                <h2 class="text-lg font-semibold tracking-tight">{{ $category }}</h2>
                <div class="mt-5 grid grid-cols-1 gap-5 xl:grid-cols-2">
                    @foreach ($components as $component)
                        <div x-show="matches(@js($component['label'].' '.$component['name'].' '.$component['description']))">
                            <x-devdojo-components::studio.card :component="$component" />
                        </div>
                    @endforeach
                </div>
            </section>
        @endforeach
    </div>
</x-devdojo-components::studio.layout>
