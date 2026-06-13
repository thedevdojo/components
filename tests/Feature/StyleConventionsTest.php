<?php

use DevDojo\Components\Components;

/**
 * Enforces the styling rules in this package's CLAUDE.md so components stay
 * fully themeable. See "Sanctioned color exceptions" there for context.
 */
dataset('component blades', function () {
    return collect(Components::names())
        ->mapWithKeys(fn ($name) => [$name => Components::sourcePath($name.'/index.blade.php')])
        ->all();
});

it('uses theme radius tokens only — no bare or Tailwind-default radii', function (string $file) {
    $contents = file_get_contents($file);

    preg_match_all('/\brounded(-[a-z0-9\[\]]+)?\b/', $contents, $matches);

    $allowed = ['rounded-small', 'rounded-medium', 'rounded-large', 'rounded-full'];
    $violations = array_values(array_unique(array_diff($matches[0], $allowed)));

    expect($violations)->toBe([], 'Use rounded-small|medium|large|full only. Found: '.implode(', ', $violations));
})->with('component blades');

it('uses color tokens — no gray/neutral/stone/zinc/slate families', function (string $file) {
    $contents = file_get_contents($file);

    preg_match_all(
        '/\b(?:text|bg|border|ring|fill|stroke|placeholder|from|via|to|divide|outline|ring-offset|shadow|accent|caret|decoration)-(?:gray|neutral|stone|zinc|slate)-\d+\b/',
        $contents,
        $matches
    );

    expect(array_values(array_unique($matches[0])))
        ->toBe([], 'Use theme color tokens instead of raw gray/neutral/stone/zinc/slate.');
})->with('component blades');
