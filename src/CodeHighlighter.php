<?php

namespace DevDojo\Components;

class CodeHighlighter
{
    /**
     * Turn a Blade/HTML snippet into safe, syntax-highlighted markup.
     *
     * Every character is HTML-escaped; recognised tokens (tags, attribute
     * names, quoted strings and punctuation) are wrapped in <span> elements
     * with `tok-*` classes that the showcase styles for light & dark mode.
     */
    public static function highlight(string $code): string
    {
        $pattern = '~
            (?P<string>"[^"]*"|\'[^\']*\')          # quoted attribute values
            | (?P<tag></?[A-Za-z][\w.\-]*)          # a tag, incl. its name: <x-button, </x-button, <div
            | (?P<attr>[A-Za-z_:@][\w:@.\-]*(?==))  # an attribute name, e.g. label=, :value=, x-on:click=
            | (?P<punct>/?>|[<>=/])                 # tag punctuation: < > = / />
            | (?P<text>[\s\S])                      # anything else, one character at a time
        ~x';

        return (string) preg_replace_callback($pattern, function (array $m): string {
            foreach (['string', 'tag', 'attr', 'punct'] as $token) {
                if (($m[$token] ?? '') !== '') {
                    return '<span class="tok-'.$token.'">'.htmlspecialchars($m[$token], ENT_QUOTES).'</span>';
                }
            }

            return htmlspecialchars($m['text'] ?? '', ENT_QUOTES);
        }, $code);
    }
}
