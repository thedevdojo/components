<!DOCTYPE html>
<html lang="en" @class(['dark' => $theme === 'dark'])>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Preview</title>

    @if (file_exists(public_path('hot')) || file_exists(public_path('build/manifest.json')))
        @vite(config('components.showcase.assets', ['resources/css/app.css']))
    @endif

    @if (class_exists(\Livewire\Livewire::class))
        @livewireStyles
    @else
        <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/focus@3.x.x/dist/cdn.min.js"></script>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @endif
</head>

<body class="bg-background text-foreground antialiased">
    <div class="grid min-h-screen w-full place-items-center p-6">
        {!! $rendered !!}
    </div>

    @if (class_exists(\Livewire\Livewire::class))
        @livewireScripts
    @endif
</body>

</html>
