<?php

namespace DevDojo\Components;

use DevDojo\Components\Console\AddCommand;
use DevDojo\Components\Console\InstallCommand;
use DevDojo\Components\Console\ListCommand;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\ComponentAttributeBag;

class ComponentsServiceProvider extends ServiceProvider
{
    /**
     * Register any package services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/components.php', 'components');
    }

    /**
     * Bootstrap any package services.
     */
    public function boot(): void
    {
        $this->registerTailwindMergeFallbacks();
        $this->registerPreviewComponents();
        $this->registerShowcase();
        $this->registerPublishing();

        if ($this->app->runningInConsole()) {
            $this->commands([
                ListCommand::class,
                AddCommand::class,
                InstallCommand::class,
            ]);
        }
    }

    /**
     * Register the bundled components under the "components" preview namespace
     * so they can be used (e.g. <x-components.button />) before publishing.
     *
     * The components live in resources/components/{name}/index.blade.php, so we
     * register the parent directory and let the "components.*" dot notation map
     * straight onto that folder — the same convention Laravel uses everywhere.
     */
    protected function registerPreviewComponents(): void
    {
        if (! config('components.preview', true)) {
            return;
        }

        Blade::anonymousComponentPath(dirname(Components::sourcePath()), Components::NAMESPACE);
    }

    /**
     * Register the local-only showcase gallery route and its views.
     */
    protected function registerShowcase(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'devdojo-components');

        if (! config('components.showcase.enabled', false) || ! $this->app->environment('local')) {
            return;
        }

        Route::get(config('components.showcase.route', '/components'), function () {
            return view('devdojo-components::showcase', [
                'categories' => Components::byCategory(),
            ]);
        })->name('devdojo-components.showcase');
    }

    /**
     * Register the publishable config and CSS theme files.
     */
    protected function registerPublishing(): void
    {
        $this->publishes([
            __DIR__.'/../config/components.php' => config_path('components.php'),
        ], 'components-config');

        $this->publishes([
            __DIR__.'/../resources/css/components.css' => resource_path('css/components.css'),
        ], 'components-theme');

        // Compiled JS for the asset-backed components (Monaco, Tiptap).
        $this->publishes([
            __DIR__.'/../public/devdojo' => public_path('devdojo'),
        ], 'devdojo-assets');
    }

    /**
     * Provide graceful fallbacks for the TailwindMerge attribute macros so the
     * published components keep working even if the merge package is removed.
     * When gehrisandro/tailwind-merge-laravel is installed it registers these
     * first and the fallbacks below are skipped.
     */
    protected function registerTailwindMergeFallbacks(): void
    {
        if (! ComponentAttributeBag::hasMacro('twMerge')) {
            ComponentAttributeBag::macro('twMerge', function (string $classes = '') {
                /** @var ComponentAttributeBag $this */
                return $this->merge(['class' => $classes]);
            });
        }

        if (! ComponentAttributeBag::hasMacro('twMergeFor')) {
            ComponentAttributeBag::macro('twMergeFor', function (string $key, string $classes = '') {
                /** @var ComponentAttributeBag $this */
                $scoped = (string) ($this->get($key.':class') ?? '');

                return new ComponentAttributeBag([
                    'class' => trim($classes.' '.$scoped),
                ]);
            });
        }

        if (! ComponentAttributeBag::hasMacro('withoutTwMergeClasses')) {
            ComponentAttributeBag::macro('withoutTwMergeClasses', function () {
                /** @var ComponentAttributeBag $this */
                return $this->filter(function ($value, $key) {
                    return $key !== 'class' && ! str_ends_with((string) $key, ':class');
                });
            });
        }
    }
}
