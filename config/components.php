<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Preview Components
    |--------------------------------------------------------------------------
    |
    | Before you publish a component you can preview and use it under the
    | "components" namespace, e.g. <x-components.button />. Once published,
    | the component lives in your app and is used as <x-button />. Disable
    | this to stop the bundled components from being registered at all.
    |
    */

    'preview' => true,

    /*
    |--------------------------------------------------------------------------
    | Publish Path
    |--------------------------------------------------------------------------
    |
    | The directory (relative to your resource path) where components are
    | copied when you run `php artisan components:add`. Each component
    | is written to its own folder as an anonymous index component, e.g.
    | resources/views/components/button/index.blade.php → <x-button />.
    |
    */

    'path' => 'views/components',

    /*
    |--------------------------------------------------------------------------
    | Showcase
    |--------------------------------------------------------------------------
    |
    | A built-in gallery that renders every component so you can see exactly
    | what you are getting before you publish. It is only ever registered in
    | the "local" environment and can be toggled off entirely here.
    |
    */

    'showcase' => [
        'enabled' => env('COMPONENTS_SHOWCASE', true),
        'route' => '/components',
    ],

];
