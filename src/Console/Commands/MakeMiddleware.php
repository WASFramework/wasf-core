<?php
namespace Wasf\Console\Commands;

class MakeMiddleware extends Command
{
    public function signature(): string { return 'make:middleware'; }
    public function description(): string { return 'Create a new middleware'; }

    public function handle(array $args): void
    {
        if (!isset($args[0])) {
            $this->error("Middleware name required. Example: php wasf make:middleware AuthMiddleware");
            return;
        }

        $name = $args[0];
        $path = getcwd() . '/app/Http/Middleware/' . $name . '.php';
        if (file_exists($path)) {
            $this->warn("Middleware already exists: {$path}");
            return;
        }

        $stub = file_get_contents(__DIR__ . '/../Stubs/middleware.stub');
        $stub = str_replace('{{name}}', $name, $stub);

        @mkdir(dirname($path), 0777, true);
        file_put_contents($path, $stub);
        $this->info("Created middleware: app/Http/Middleware/{$name}.php");
    }
}
