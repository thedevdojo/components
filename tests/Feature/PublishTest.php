<?php

use DevDojo\Components\Livewire\ComponentCard;
use DevDojo\Components\Publisher;
use Illuminate\Support\Facades\File;
use Livewire\Livewire;

beforeEach(function () {
    $this->target = resource_path('views/components');
    File::deleteDirectory($this->target);
});

afterEach(function () {
    File::deleteDirectory($this->target);
});

it('detects an unpublished component and publishes it', function () {
    $publisher = app(Publisher::class);

    expect($publisher->isPublished('button'))->toBeFalse();

    expect($publisher->publish('button'))->toBe('added')
        ->and($publisher->isPublished('button'))->toBeTrue()
        ->and(File::exists(resource_path('views/components/button/index.blade.php')))->toBeTrue();
});

it('skips an existing component unless forced, then overwrites', function () {
    $path = resource_path('views/components/button/index.blade.php');
    File::ensureDirectoryExists(dirname($path));
    File::put($path, 'CUSTOM');

    $publisher = app(Publisher::class);

    expect($publisher->publish('button'))->toBe('skipped')
        ->and(File::get($path))->toBe('CUSTOM');

    expect($publisher->publish('button', force: true))->toBe('overwritten')
        ->and(File::get($path))->not->toBe('CUSTOM');
});

it('rewrites the preview namespace when publishing', function () {
    app(Publisher::class)->publish('input');

    expect(File::get(resource_path('views/components/input/index.blade.php')))
        ->toContain('<x-label')
        ->not->toContain('x-components.');
});

it('publishes a component and its dependencies via the livewire card', function () {
    Livewire::test(ComponentCard::class, ['name' => 'input'])
        ->assertSet('published', false)
        ->call('add')
        ->assertSet('published', true)
        ->assertDispatched('pop-toast');

    expect(File::exists(resource_path('views/components/input/index.blade.php')))->toBeTrue()
        ->and(File::exists(resource_path('views/components/label/index.blade.php')))->toBeTrue();
});

it('re-adds (overwrites) a component via the livewire card', function () {
    $path = resource_path('views/components/button/index.blade.php');
    File::ensureDirectoryExists(dirname($path));
    File::put($path, 'CUSTOM');

    Livewire::test(ComponentCard::class, ['name' => 'button'])
        ->assertSet('published', true)
        ->call('reAdd')
        ->assertSet('published', true);

    expect(File::get($path))->not->toBe('CUSTOM');
});
