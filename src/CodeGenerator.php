<?php

namespace DevDojo\Components;

class CodeGenerator
{
    /**
     * Build a Blade component tag from structured attribute and slot values.
     *
     * @param  array<string, mixed>  $attrs
     * @param  array<string, string>  $slots  keyed by slot name; "slot" is the default slot
     */
    public static function generate(string $name, array $attrs = [], array $slots = [], bool $published = false): string
    {
        $tag = ($published ? 'x-' : 'x-'.Components::NAMESPACE.'.').$name;

        $attrLines = [];

        foreach ($attrs as $key => $value) {
            $attribute = static::attribute((string) $key, $value);

            if ($attribute !== null) {
                $attrLines[] = '    '.$attribute;
            }
        }

        $open = $attrLines === []
            ? "<{$tag}"
            : "<{$tag}\n".implode("\n", $attrLines)."\n";

        $defaultSlot = trim((string) ($slots['slot'] ?? ''));
        $namedSlots = array_filter(
            $slots,
            fn ($content, $slotName) => $slotName !== 'slot' && trim((string) $content) !== '',
            ARRAY_FILTER_USE_BOTH
        );

        if ($defaultSlot === '' && $namedSlots === []) {
            return $open.($attrLines === [] ? ' />' : '/>');
        }

        $lines = [$open.'>'];

        if ($defaultSlot !== '') {
            $lines[] = '    '.$defaultSlot;
        }

        foreach ($namedSlots as $slotName => $content) {
            $lines[] = "    <x-slot:{$slotName}>".trim((string) $content)."</x-slot:{$slotName}>";
        }

        $lines[] = "</{$tag}>";

        return implode("\n", $lines);
    }

    /**
     * Render a single attribute line, or null when the value should be omitted.
     */
    protected static function attribute(string $key, mixed $value): ?string
    {
        if ($value === '' || $value === null || $value === false || $value === 'false') {
            return null;
        }

        if ($value === true || $value === 'true') {
            return $key;
        }

        if (is_array($value)) {
            return ':'.$key.'="'.static::phpLiteral($value).'"';
        }

        if (is_string($value)
            && (str_starts_with($value, '[') || str_starts_with($value, '{'))
            && json_decode($value, true) !== null) {
            return ':'.$key.'="'.static::phpLiteral(json_decode($value, true)).'"';
        }

        return $key.'="'.e((string) $value).'"';
    }

    /**
     * Convert a decoded value into PHP array-literal syntax for :prop bindings.
     */
    protected static function phpLiteral(mixed $value): string
    {
        if (is_array($value)) {
            if (array_is_list($value)) {
                return '['.implode(', ', array_map(fn ($item) => static::phpLiteral($item), $value)).']';
            }

            $pairs = [];

            foreach ($value as $key => $item) {
                $pairs[] = "'".addslashes((string) $key)."' => ".static::phpLiteral($item);
            }

            return '['.implode(', ', $pairs).']';
        }

        return match (true) {
            is_string($value) => "'".addslashes($value)."'",
            is_bool($value) => $value ? 'true' : 'false',
            $value === null => 'null',
            default => (string) $value,
        };
    }
}
