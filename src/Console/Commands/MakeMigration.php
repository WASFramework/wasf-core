<?php
namespace Wasf\Console\Commands;

class MakeMigration extends Command
{
    public function signature(): string { return 'make:migration'; }
    public function description(): string { return 'Create a new migration file (PHP Schema Based)'; }

    public function handle(array $args): void
    {
        if (!isset($args[0])) {
            $this->error("Migration name required. Example:\nphp wasf make:migration create_users_table");
            return;
        }

        $name = $args[0];

        // Determine table name automatically: create_users_table → users
        $table = $this->extractTableName($name);

        // Create file name
        $timestamp = date('YmdHis');
        $filename = "{$timestamp}_{$name}.php";

        $dir = getcwd() . '/database/migrations';
        $path = $dir . '/' . $filename;

        // Ensure folder exists
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        // Load stub
        $stubPath = __DIR__ . '/../Stubs/migration.stub';

        if (!file_exists($stubPath)) {
            $this->error("Migration stub not found at: {$stubPath}");
            return;
        }

        $stub = file_get_contents($stubPath);

        // Replace variables
        $stub = str_replace('{{table}}', $table, $stub);

        // Write file
        file_put_contents($path, $stub);

        $this->info("Migration created: database/migrations/{$filename}");
    }

    /**
     * Extract table name from migration name.
     * Example: create_users_table → users
     * Example: create_products_categories_table → products_categories
     */
    protected function extractTableName(string $name): string
    {
        if (preg_match('/create_(.*)_table/', $name, $matches)) {
            return $matches[1];
        }
        return $name; // fallback
    }
}
