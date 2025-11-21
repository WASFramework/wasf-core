<?php
namespace Wasf\Console\Commands;

class MakeController extends Command
{
    public function signature(): string { return 'make:controller'; }
    public function description(): string { return 'Create a new controller'; }

    public function handle(array $args): void
    {
        if (!isset($args[0])) {
            $this->error("Controller name required. Example: php wasf make:controller HomeController");
            return;
        }

        $name = $args[0];
        $path = getcwd() . '/app/Controllers/' . $name . '.php';
        if (file_exists($path)) {
            $this->warn("Controller already exists: {$path}");
            return;
        }

        $stub = file_get_contents(__DIR__ . '/../Stubs/controller.stub');
        $stub = str_replace('{{name}}', $name, $stub);

        // ensure dir
        @mkdir(dirname($path), 0777, true);
        file_put_contents($path, $stub);

        $this->info("Created controller: app/Controllers/{$name}.php");
    }
}
