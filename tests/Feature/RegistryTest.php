<?php

use DevDojo\Components\Components;

it('discovers every bundled component', function () {
    $names = Components::names();

    expect($names)->toContain(
        'button', 'input', 'label', 'card', 'checkbox', 'radio', 'toggle',
        'modal', 'dropdown', 'popover', 'tooltip', 'alert', 'toast',
        'drawer', 'monaco-editor', 'tiptap',
    );
});

it('loads metadata for each component', function () {
    foreach (Components::all() as $name => $meta) {
        expect($meta)->toHaveKeys(['name', 'label', 'description', 'category'])
            ->and($meta['name'])->toBe($name)
            ->and($meta['description'])->not->toBe('')
            ->and(is_file(Components::sourcePath($name.'/index.blade.php')))->toBeTrue();
    }
});

it('resolves dependencies before the component that needs them', function () {
    $resolved = Components::withDependencies(['input', 'modal']);

    // label is required by input, button is required by modal
    expect($resolved)->toContain('label', 'input', 'button', 'modal')
        ->and(array_search('label', $resolved))->toBeLessThan(array_search('input', $resolved))
        ->and(array_search('button', $resolved))->toBeLessThan(array_search('modal', $resolved));
});

it('groups components by category in display order', function () {
    expect(Components::byCategory()->keys()->all())
        ->toBe(['Forms', 'Layout', 'Navigation', 'Display', 'Overlays', 'Feedback', 'Editors']);
});
