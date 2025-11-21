<?php
namespace Wasf\Console\Commands;

class MakeRequest extends Command
{
    public function signature(): string { return 'make:request'; }
    public function description(): string { return 'Create a request class'; }

    public function handle(array $args): void
    {
        if (!isset($args[0])) {
            $this->error("Request name required. Example: php wasf make:request StoreUserRequest");
            return;
        }

        $name = $args[0];
        $path = getcwd() . '/app/Http/Requests/' . $name . '.php';
        if (file_exists($path)) {
            $this->warn("Request already exists: {$path}");
            return;
        }

        $stub = file_get_contents(__DIR__ . '/../Stubs/request.stub');
        $stub = str_replace('{{name}}', $name, $stub);

        @mkdir(dirname($path), 0777, true);
        file_put_contents($path, $stub);
        $this->info("Created request: app/Http/Requests/{$name}.php");
    }
}
