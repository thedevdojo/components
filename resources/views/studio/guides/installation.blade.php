@php
    $importSnippet = "@import 'tailwindcss';\n@import './components.css';";
    $sourceSnippet = "@source '../../vendor/devdojo/components/resources/components';";
    $useSnippet = '<x-button>Get started</x-button>';
@endphp

<h1>Installation</h1>
<p class="lede">Add the package, point Tailwind at the components, and publish your first component. Takes about two minutes.</p>

<h2>1. Require the package</h2>
<p>Pull DevDojo Components in with Composer:</p>
<x-devdojo-components::studio.code-block class="mt-4" lang="shellscript" code="composer require devdojo/components" />

<h2>2. Publish the theme</h2>
<p>Publish the CSS theme tokens (the semantic colors and radii every component references) into your app so you can tweak them later:</p>
<x-devdojo-components::studio.code-block class="mt-4" lang="shellscript" code="php artisan vendor:publish --tag=components-theme" />
<p>This writes <code>resources/css/components.css</code>. Import it from your main stylesheet, above your own styles:</p>
<x-devdojo-components::studio.code-block class="mt-4" lang="css" :code="$importSnippet" />

<h2>3. Tell Tailwind where to look</h2>
<p>So Tailwind compiles the classes used inside the components, add them as a source in your <code>resources/css/app.css</code>:</p>
<x-devdojo-components::studio.code-block class="mt-4" lang="css" :code="$sourceSnippet" />
<p>Then rebuild your assets with <code>npm run build</code> (or <code>npm run dev</code> while developing).</p>

<h2>4. Add your first component</h2>
<p>Publish a component into your app. Its files land in <code>resources/views/components/{name}</code> and it becomes available as a normal anonymous Blade component:</p>
<x-devdojo-components::studio.code-block class="mt-4" lang="shellscript" code="php artisan components:add button" />
<p>Now use it anywhere:</p>
<x-devdojo-components::studio.code-block class="mt-4" :code="$useSnippet" />

<p>Dependencies are resolved automatically — adding a component that relies on another pulls that one in too.</p>
