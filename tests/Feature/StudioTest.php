<?php

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
