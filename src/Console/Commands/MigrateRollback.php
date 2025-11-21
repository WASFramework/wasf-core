<?php
namespace Wasf\Console\Commands;

use Wasf\Database\DB;
use Wasf\Database\Schema;
use PDO;

class MigrateRollback extends Command
{
    public function signature(): string { return 'migrate:rollback'; }
    public function description(): string { return 'Rollback last migration batch'; }

    public function handle(array $args): void
    {
        $project = getcwd();
        $confFile = $project . '/config/database.php';

        if (!file_exists($confFile)) {
            $this->error("Database config not found at {$confFile}");
            return;
        }

        $conf = require $confFile;
        $pdo = DB::connect($conf);

        // Set Schema connection
        Schema::setConnection($pdo);

        // Get last batch
        $batch = $pdo->query("SELECT MAX(batch) FROM migrations")->fetchColumn();

        if (!$batch) {
            $this->warn("No migrations to rollback.");
            return;
        }

        $this->info("Rolling back batch {$batch}...");

        // Get migration files in reverse order
        $stmt = $pdo->prepare("SELECT migration FROM migrations WHERE batch = ? ORDER BY id DESC");
        $stmt->execute([$batch]);
        $migrations = $stmt->fetchAll(PDO::FETCH_COLUMN);

        if (!$migrations) {
            $this->warn("Nothing to rollback.");
            return;
        }

        foreach ($migrations as $fileName) {

            $filePath = $project . "/database/migrations/" . $fileName;

            if (!file_exists($filePath)) {
                $this->error("Migration file missing: {$filePath}");
                continue;
            }

            $this->info("Rolling back: {$fileName}");

            $migration = require $filePath;

            try {
                if (is_object($migration) && method_exists($migration, 'down')) {
                    $migration->down();
                } else {
                    $this->warn("Migration does not support rollback: {$fileName}");
                    continue;
                }

                // Remove from migrations table
                $del = $pdo->prepare("DELETE FROM migrations WHERE migration=?");
                $del->execute([$fileName]);

                $this->info("✓ Rolled back");
            }
            catch (\Throwable $e) {
                $this->error("✗ Error: " . $e->getMessage());
            }
        }

        $this->info("✓ Rollback completed");
    }
}
