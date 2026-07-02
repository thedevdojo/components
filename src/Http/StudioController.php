<?php

namespace DevDojo\Components\Http;

use DevDojo\Components\CodeGenerator;
use DevDojo\Components\Components;
use DevDojo\Components\Publisher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Str;

class StudioController
{
    /**
     * The browse page: every component grouped by category.
     */
    public function index()
    {
        return view('devdojo-components::showcase', [
            'categories' => Components::byCategory(),
        ]);
    }

    /**
     * A component's documentation + playground page.
     */
    public function show(Request $request, string $name)
    {
        abort_unless(Components::exists($name), 404);

        $component = Components::get($name);

        [$attrs, $slotValues] = $this->playgroundState($component, $request);

        $examples = collect($component['examples'])->map(function (array $example) use ($name) {
            $file = Components::sourcePath($name.'/examples/'.$example['file']);
            $source = is_file($file) ? (string) file_get_contents($file) : '';

            return $example + ['code' => trim(app(Publisher::class)->transform($source))];
        });

        return view('devdojo-components::studio.show', [
            'component' => $component,
            'categories' => Components::byCategory(),
            'attrs' => $attrs,
            'slotValues' => $slotValues,
            'initialCode' => CodeGenerator::generate(
                $name,
                $this->withoutDefaults($component, $attrs),
                $slotValues,
                published: true,
            ),
            'examples' => $examples,
        ]);
    }

    /**
     * The document rendered inside every preview iframe.
     */
    public function preview(Request $request, string $name)
    {
        abort_unless(Components::exists($name), 404);

        $component = Components::get($name);

        if ($example = $this->exampleSource($component, $request->query('example'))) {
            $code = $example;
        } else {
            [$attrs, $slotValues] = $this->playgroundState($component, $request);

            $code = CodeGenerator::generate($name, $attrs, $this->verbatimSlots($slotValues));

            if ($container = $component['studio']['container'] ?? null) {
                $code = str_replace('{{component}}', $code, $container);
            }
        }

        try {
            $rendered = Blade::render($code);
        } catch (\Throwable $e) {
            $rendered = '<div class="w-full max-w-lg rounded-medium border border-destructive/30 bg-destructive/5 p-4">'
                .'<p class="text-sm font-semibold text-destructive">Render error</p>'
                .'<pre class="mt-2 overflow-auto whitespace-pre-wrap font-mono text-xs text-destructive/80">'.e($e->getMessage()).'</pre>'
                .'</div>';
        }

        return view('devdojo-components::studio.preview', [
            'rendered' => $rendered,
            'theme' => $request->query('theme') === 'dark' ? 'dark' : 'light',
        ]);
    }

    /**
     * Current playground state: declared props/slots only, query values
     * overlaid on studio defaults then prop defaults. Each value is
     * constrained by the prop's declared type so scalar props can never
     * receive array-shaped query input.
     *
     * @param  array<string, mixed>  $component
     * @return array{0: array<string, mixed>, 1: array<string, string>}
     */
    protected function playgroundState(array $component, Request $request): array
    {
        $studioDefaults = $component['studio']['defaults'] ?? [];
        $queryAttrs = (array) $request->query('attrs', []);
        $querySlots = (array) $request->query('slots', []);

        $attrs = [];

        foreach ($component['props'] as $prop) {
            $key = $prop['name'];
            $default = $studioDefaults[$key] ?? $prop['default'] ?? '';
            $value = $queryAttrs[$key] ?? $default;
            $type = $prop['type'] ?? 'text';

            if ($type === 'boolean') {
                // Booleans arrive from the query string as "true"/"false".
                $value = $value === true || $value === 'true';
            } elseif ($type !== 'array') {
                // Scalar props: reject array-shaped input outright.
                $value = is_scalar($value) ? (string) $value : '';
            }

            $attrs[$key] = $value;
        }

        $slotValues = [];

        foreach ($component['slots'] as $slot) {
            $key = $slot['name'];
            $value = $querySlots[$key] ?? $slot['default'] ?? '';
            $slotValues[$key] = is_scalar($value) ? (string) $value : '';
        }

        if (! array_key_exists('slot', $slotValues)) {
            $fallback = $component['slots'] === [] ? '' : $component['label'];
            $value = $querySlots['slot'] ?? $fallback;
            $slotValues['slot'] = is_scalar($value) ? (string) $value : '';
        }

        return [$attrs, $slotValues];
    }

    /**
     * Drop attrs whose value equals the prop default, so generated code stays minimal.
     *
     * @param  array<string, mixed>  $component
     * @param  array<string, mixed>  $attrs
     * @return array<string, mixed>
     */
    protected function withoutDefaults(array $component, array $attrs): array
    {
        $defaults = collect($component['props'])->keyBy('name')->map(fn ($prop) => $prop['default'] ?? '');

        return array_filter(
            $attrs,
            fn ($value, $key) => (string) $value !== (string) ($defaults[$key] ?? '')
                && ! ($value === false && ($defaults[$key] ?? false) === false),
            ARRAY_FILTER_USE_BOTH
        );
    }

    /**
     * Neutralize Blade in user-supplied slot content for the server-render
     * path: markup passes through, but directives, echoes and PHP blocks
     * render as literal text. Never applied to displayed code.
     *
     * @param  array<string, string>  $slots
     * @return array<string, string>
     */
    protected function verbatimSlots(array $slots): array
    {
        return array_map(function (string $content): string {
            if (trim($content) === '') {
                return $content;
            }

            $content = str_ireplace(['@verbatim', '@endverbatim'], '', $content);

            return '@verbatim'.$content.'@endverbatim';
        }, $slots);
    }

    /**
     * The source of a declared example file, or null when not requested/known.
     *
     * @param  array<string, mixed>  $component
     */
    protected function exampleSource(array $component, ?string $requested): ?string
    {
        if ($requested === null) {
            return null;
        }

        foreach ($component['examples'] as $example) {
            if (Str::of($example['file'])->basename('.blade.php')->value() === $requested) {
                $file = Components::sourcePath($component['name'].'/examples/'.$example['file']);

                return is_file($file) ? (string) file_get_contents($file) : null;
            }
        }

        return null;
    }
}
