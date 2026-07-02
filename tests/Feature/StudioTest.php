<?php

use DevDojo\Components\Components;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    $this->withoutVite();
});

it('registers the studio routes', function () {
    expect(Route::has('devdojo-components.showcase'))->toBeTrue()
        ->and(Route::has('devdojo-components.show'))->toBeTrue()
        ->and(Route::has('devdojo-components.preview'))->toBeTrue();
});

it('serves the index at the configured route', function () {
    $this->get('/components')->assertOk();
});

it('renders a component preview with default state', function () {
    $this->get('/components/button/preview')
        ->assertOk()
        ->assertSee('<button', false);
});

it('applies structured attrs to the preview', function () {
    $this->get('/components/button/preview?attrs[variant]=destructive&slots[slot]=Delete')
        ->assertOk()
        ->assertSee('bg-destructive', false)
        ->assertSee('Delete');
});

it('ignores undeclared attrs instead of rendering them', function () {
    $this->get('/components/button/preview?attrs[onclick]=alert(1)&attrs[variant]=ghost')
        ->assertOk()
        ->assertDontSee('onclick', false)
        ->assertDontSee('alert(1)', false);
});

it('neutralizes array-shaped and JSON-shaped attr values on scalar props', function () {
    $this->get('/components/button/preview?attrs[variant][]=x')
        ->assertOk()
        ->assertDontSee('syntax error', false)
        ->assertSee('<button', false);

    $this->get('/components/button/preview?'.http_build_query([
        'attrs' => ['variant' => '["x\" <x-evil z="]'],
    ]))->assertOk()->assertDontSee('x-evil', false)->assertDontSee('syntax error', false);
});

it('applies the dark theme class to the preview document', function () {
    $this->get('/components/button/preview?theme=dark')
        ->assertOk()
        ->assertSee('class="dark"', false);
});

it('wraps overlay components in their studio container', function () {
    // modal declares studio.container in Task 7; until then this asserts the
    // pass-through path renders. Strengthened in Task 7 Step 4.
    $this->get('/components/modal/preview')->assertOk();
});

it('returns 404 for unknown components', function () {
    $this->get('/components/nope/preview')->assertNotFound();
    $this->get('/components/nope')->assertNotFound();
});

it('never executes Blade or PHP smuggled into slot content', function () {
    // The marker "pw"."ned" only appears concatenated if the code EXECUTED;
    // rendered-as-literal-text output contains the source, never the marker.
    $this->get('/components/button/preview?'.http_build_query([
        'slots' => ['slot' => '@php echo "pw"."ned"; @endphp'],
    ]))->assertOk()->assertDontSee('pwned', false)->assertSee('@php', false);

    $this->get('/components/button/preview?'.http_build_query([
        'slots' => ['slot' => '@endverbatim @php echo "pw"."ned"; @endphp @verbatim'],
    ]))->assertOk()->assertDontSee('pwned', false);

    $this->get('/components/button/preview?'.http_build_query([
        'slots' => ['slot' => '{{ "pw"."ned" }}'],
    ]))->assertOk()->assertDontSee('pwned', false);
});

it('lists every component on the index with links to their pages', function () {
    $response = $this->get('/components')->assertOk();

    foreach (Components::all() as $component) {
        $response->assertSee($component['label']);
        $response->assertSee('/components/'.$component['name'], false);
    }
});

it('renders the command palette items on the index', function () {
    $this->get('/components')->assertOk()->assertSee('command-select', false);
});

it('renders every component detail page', function (string $name) {
    $this->get('/components/'.$name)->assertOk();
})->with(fn () => array_map(fn ($n) => [$n], Components::names()));

it('shows install command, docs examples and reference tables for button', function () {
    $this->get('/components/button')
        ->assertOk()
        ->assertSee('php artisan components:add button')
        ->assertSee('Variants')
        ->assertSee('Sizes')
        ->assertSee('&lt;x-button', false) // displayed code is published syntax, escaped in the <pre>
        ->assertSee('variant')
        ->assertSee('destructive');
});

it('renders a declared example through the preview endpoint', function () {
    $this->get('/components/button/preview?example=variants')
        ->assertOk()
        ->assertSee('Secondary');
});

it('does not leak the preview namespace into displayed code', function () {
    $this->get('/components/button')
        ->assertOk()
        ->assertDontSee('x-components.button', false);
});

it('renders playground knobs for every declared button prop and slot', function () {
    $this->get('/components/button')
        ->assertOk()
        ->assertSee('studioPlayground(', false)
        ->assertSee("x-model=\"attrs['variant']\"", false)
        ->assertSee('data-knob="loader"', false)
        ->assertSee("x-model=\"slots['slot']\"", false)
        ->assertSee('option value="ghost"', false);
});

it('seeds the playground with published-syntax code', function () {
    $this->get('/components/button')
        ->assertOk()
        ->assertSee('&lt;x-button', false);
});

it('seeds playground state from the query string', function () {
    // href is a text prop; its query value must round-trip into the
    // server-rendered playground config for Alpine to pick up.
    $this->get('/components/button?attrs[href]=/custom-path&tab=playground')
        ->assertOk()
        ->assertSee('custom-path', false);
});
