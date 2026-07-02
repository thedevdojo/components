<?php

use DevDojo\Components\CodeHighlighter;

it('escapes every angle bracket so nothing renders as real markup', function () {
    $html = CodeHighlighter::highlight('<x-button>Submit</x-button>');

    expect($html)
        ->toContain('&lt;')
        ->not->toContain('<x-button>')          // never emit a live component tag
        ->toContain('Submit');                   // visible content survives
});

it('wraps recognised tokens in their tok-* spans', function () {
    $html = CodeHighlighter::highlight('<x-input label="Email" />');

    expect($html)
        ->toContain('tok-tag')                   // <x-input
        ->toContain('tok-attr')                  // label
        ->toContain('tok-str');                  // "Email"
});

it('preserves multi-line snippets', function () {
    $html = CodeHighlighter::highlight("<x-card>\n    Hello\n</x-card>");

    expect($html)->toContain("\n")->toContain('Hello');
});
