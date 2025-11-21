<?php
namespace Wasf\Console\Commands;

class MakeView extends Command
{
    public function signature(): string { return 'make:view'; }
    public function description(): string { return 'Create a blade view (dot notation)'; }

    public function handle(array $args): void
    {
        if (!isset($args[0])) {
            $this->error("View name required. Example: php wasf make:view pages.home");
            return;
        }

        $view = $args[0];
        $file = str_replace('.', DIRECTORY_SEPARATOR, $view) . '.blade.php';
        $path = getcwd() . '/app/Views/' . $file;

        if (file_exists($path)) {
            $this->warn("View already exists: {$path}");
            return;
        }

        @mkdir(dirname($path), 0777, true);
        $stub = file_get_contents(__DIR__ . '/../Stubs/view.stub');
        $stub = str_replace('{{view}}', $view, $stub);
        file_put_contents($path, $stub);
        $this->info("Created view: app/Views/{$file}");
    }
}
