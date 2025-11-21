<?php
namespace Wasf\Console\Commands;

class MakeProvider extends Command
{
    public function signature(): string { return 'make:provider'; }
    public function description(): string { return 'Create a service provider'; }

    public function handle(array $args): void
    {
        if (!isset($args[0])) {
            $this->error("Provider name required. Example: php wasf make:provider AppServiceProvider");
            return;
        }

        $name = $args[0];
        $path = getcwd() . '/app/Providers/' . $name . '.php';
        if (file_exists($path)) {
            $this->warn("Provider already exists: {$path}");
            return;
        }

        $stub = file_get_contents(__DIR__ . '/../Stubs/provider.stub');
        $stub = str_replace('{{name}}', $name, $stub);

        @mkdir(dirname($path), 0777, true);
        file_put_contents($path, $stub);

        $this->info("Created provider: app/Providers/{$name}.php");
    }
}
