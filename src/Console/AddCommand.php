<?php

namespace DevDojo\Components\Console;

use DevDojo\Components\Components;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

use function Laravel\Prompts\multiselect;

class AddCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'components:add
        {component?* : One or more component names to add}
        {--all : Add every available component}
        {--force : Overwrite components that already exist}';

    /**
     * @var string
     */
    protected $description = 'Add DevDojo components to your application';

    public function __construct(protected Filesystem $files)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $selected = $this->resolveSelection();

        if ($selected === null) {
            return self::FAILURE;
        }

        if ($selected === []) {
            $this->components->warn('No components selected. Nothing to add.');

            return self::SUCCESS;
        }

        $resolved = Components::withDependencies($selected);
        $pulledInDeps = array_values(array_diff($resolved, $selected));

        if ($pulledInDeps !== []) {
            $this->components->info('Including required dependencies: '.implode(', ', $pulledInDeps));
        }

        $added = 0;
        $skipped = 0;

        foreach ($resolved as $name) {
            $result = $this->addComponent($name);

            $result === 'skipped' ? $skipped++ : $added++;
        }

        $this->newLine();
        $this->components->info("Done. {$added} added".($skipped ? ", {$skipped} skipped" : '').'.');
        $this->outputUsageHint($resolved);

        return self::SUCCESS;
    }

    /**
     * Determine which components the user wants to add. Returns null when the
     * request is invalid (e.g. an unknown component was named).
     *
     * @return array<int, string>|null
     */
    protected function resolveSelection(): ?array
    {
        if ($this->option('all')) {
            return Components::names();
        }

        $arguments = array_values(array_filter((array) $this->argument('component')));

        if ($arguments !== []) {
            $unknown = array_diff($arguments, Components::names());

            if ($unknown !== []) {
                $this->components->error('Unknown component(s): '.implode(', ', $unknown));

                return null;
            }

            return $arguments;
        }

        return multiselect(
            label: 'Which components would you like to add?',
            options: collect(Components::all())
                ->mapWithKeys(fn ($meta, $name) => [$name => $meta['label'].' — '.Str::limit($meta['description'], 50)])
                ->all(),
            hint: 'Use the space bar to select, then press enter. Dependencies are added automatically.',
            scroll: 15,
        );
    }

    /**
     * Add a single component, returning 'added' or 'skipped'.
     */
    protected function addComponent(string $name): string
    {
        $source = Components::sourcePath($name.'/index.blade.php');
        $destination = $this->destinationPath($name);

        if ($this->files->exists($destination) && ! $this->option('force')) {
            $this->components->twoColumnDetail(
                "<fg=yellow>$name</>",
                '<fg=yellow>exists, skipped (use --force)</>'
            );

            return 'skipped';
        }

        $this->files->ensureDirectoryExists(dirname($destination));
        $this->files->put($destination, $this->transform($this->files->get($source)));

        $this->components->twoColumnDetail(
            "<fg=green>$name</>",
            '<fg=gray>'.$this->relativePath($destination).'</>'
        );

        return 'added';
    }

    /**
     * Rewrite the preview namespace references to root anonymous components,
     * e.g. <x-components.button /> becomes <x-button /> once added.
     */
    protected function transform(string $contents): string
    {
        $namespace = Components::NAMESPACE;

        return str_replace(
            ["<x-{$namespace}.", "</x-{$namespace}."],
            ['<x-', '</x-'],
            $contents
        );
    }

    protected function destinationPath(string $name): string
    {
        return resource_path(trim((string) config('components.path', 'views/components'), '/')."/{$name}/index.blade.php");
    }

    protected function relativePath(string $path): string
    {
        return Str::after($path, base_path().DIRECTORY_SEPARATOR);
    }

    /**
     * @param  array<int, string>  $added
     */
    protected function outputUsageHint(array $added): void
    {
        $example = $added[0] ?? 'button';

        $this->newLine();
        $this->line("  Use them right away, e.g. <fg=cyan><x-{$example} /></>");

        if (in_array('toast', $added, true)) {
            $this->line('  <fg=gray>Tip: place <x-toast /> once in your layout to enable notifications.</>');
        }
    }
}
