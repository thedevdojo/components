<?php

namespace DevDojo\Components\Tests;

use DevDojo\Components\ComponentsServiceProvider;
use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase as Orchestra;
use TailwindMerge\Laravel\TailwindMergeServiceProvider;

class TestCase extends Orchestra
{
    /**
     * @param  Application  $app
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            TailwindMergeServiceProvider::class,
            ComponentsServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['env'] = 'local';
        $app['config']->set('app.env', 'local');
    }
}
