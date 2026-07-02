@php
    $previewSnippet = '<x-components.button>Preview me</x-components.button>';
    $propsSnippet = "<x-button variant=\"outline\" size=\"lg\">\n    Save changes\n</x-button>";
    $livewireSnippet = "<x-input wire:model=\"email\" label=\"Email\" type=\"email\" />\n<x-checkbox wire:model=\"terms\" label=\"Accept the terms\" />";
    $loaderSnippet = '<x-button wire:click="save" loader>Save</x-button>';
@endphp

<h1>Usage</h1>
<p class="lede">How to preview components before you commit to them, publish the ones you want, and use them in your Blade and Livewire views.</p>

<h2>Preview before publishing</h2>
<p>Before a component lives in your app, you can already use it under the <code>components</code> preview namespace. This is exactly what this documentation site does:</p>
<x-devdojo-components::studio.code-block class="mt-4" :code="$previewSnippet" />
<p>When you're happy, run <code>php artisan components:add button</code>. The publish command copies the source into your app <strong>and rewrites the namespace</strong>, so <code>&lt;x-components.button&gt;</code> becomes plain <code>&lt;x-button&gt;</code>. The Build tab's code panel always shows this final, published syntax — copy it verbatim.</p>

<h2>Props and slots</h2>
<p>Every component is documented with its props and slots. Pass props as attributes and content through the default slot:</p>
<x-devdojo-components::studio.code-block class="mt-4" :code="$propsSnippet" />
<p>Use the <a href="{{ route('devdojo-components.showcase') }}">gallery</a> and each component's <strong>Build</strong> tab to discover every available prop, flip it live, and watch the generated code update.</p>

<h2>Livewire</h2>
<p>Form components bind with <code>wire:model</code> like any native input:</p>
<x-devdojo-components::studio.code-block class="mt-4" :code="$livewireSnippet" />
<p>The button understands Livewire loading states — add <code>loader</code> and it shows a spinner while its <code>wire:click</code> (or an explicit <code>wire:target</code>) is running:</p>
<x-devdojo-components::studio.code-block class="mt-4" :code="$loaderSnippet" />

<h2>Own the code</h2>
<p>Once published, a component is yours. Open its file in <code>resources/views/components</code> and change anything — the markup, the defaults, the classes. Nothing in the package will overwrite it unless you explicitly re-add it. To make styling changes that apply everywhere instead, see <a href="{{ route('devdojo-components.guide', ['page' => 'styling']) }}">Styling</a>.</p>
