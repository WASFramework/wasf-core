<?php
namespace Wasf\Console\Commands;

class MakeModel extends Command
{
    public function signature(): string { return 'make:model'; }
    public function description(): string { return 'Create a new model'; }

    public function handle(array $args): void
    {
        if (!isset($args[0])) {
            $this->error("Model name required. Example: php wasf make:model User");
            return;
        }

        $name = $args[0];
        $path = getcwd() . '/app/Models/' . $name . '.php';
        if (file_exists($path)) {
            $this->warn("Model already exists: {$path}");
            return;
        }

        $stub = file_get_contents(__DIR__ . '/../Stubs/model.stub');
        $stub = str_replace('{{name}}', $name, $stub);

        @mkdir(dirname($path), 0777, true);
        file_put_contents($path, $stub);

        $this->info("Created model: app/Models/{$name}.php");
    }
}
