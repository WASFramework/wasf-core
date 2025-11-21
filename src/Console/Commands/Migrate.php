<?php
namespace Wasf\Console\Commands;

use Wasf\Database\DB;
use PDO;

class Migrate extends Command
{
    public function signature(): string { return 'migrate'; }
    public function description(): string { return 'Run SQL migrations from database/migrations'; }

    public function handle(array $args): void
    {
        $project = getcwd();
        $this->info("Using project: {$project}");

        // ===========================
        // 1. Load DB config
        // ===========================
        $confFile = $project . '/config/database.php';
        if (!file_exists($confFile)) {
            $this->error("Database config not found: {$confFile}");
            return;
        }

        $conf = require $confFile;

        $dbName = $conf['database'];
        $host   = $conf['host'];
        $user   = $conf['username'];
        $pass   = $conf['password'];
        $port   = $conf['port'] ?? 3306;

        // ===========================
        // 2. Connect WITHOUT database
        //    (to create database if missing)
        // ===========================
        $this->info("Checking database '{$dbName}'...");

        $pdoRoot = new PDO(
            "mysql:host={$host};port={$port}",
            $user,
            $pass,
            [ PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION ]
        );

        // Create database if not exists
        $pdoRoot->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

        $this->info("✓ Database exists / created");

        // ===========================
        // 3. Connect to the database
        // ===========================
        $pdo = DB::connect($conf);
        \Schema::setConnection($pdo);

        // ===========================
        // 4. Ensure migrations table exists
        // ===========================
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS migrations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                migration VARCHAR(255) NOT NULL,
                batch INT NOT NULL,
                run_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");

        $this->info("✓ Table 'migrations' OK");

        // ===========================
        // 5. Scan migration files
        // ===========================
        $dir = $project . '/database/migrations';
        if (!is_dir($dir)) {
            $this->error("No migrations directory found at {$dir}");
            return;
        }

        $files = glob($dir . '/*.php');
        if (!$files) {
            $this->warn("No migration files found.");
            return;
        }

        // ===========================
        // 6. Load already executed
        // ===========================
        $done = $pdo->query("SELECT migration FROM migrations")
                    ->fetchAll(PDO::FETCH_COLUMN);

        $batch = (int)$pdo->query("SELECT MAX(batch) FROM migrations")->fetchColumn();
        $batch = $batch + 1;

        // ===========================
        // 7. Run pending migrations
        // ===========================
        $files = glob($dir . '/*.php');

        foreach ($files as $file) {

            $name = basename($file);

            if (in_array($name, $done)) {
                $this->warn("Skipped (already migrated): {$name}");
                continue;
            }

            $this->info("Running: {$name}");

            try {
                $runner = require $file;

                if (is_object($runner) && method_exists($runner, 'up')) {
                    $runner->up();
                } else {
                    $this->error("Invalid migration format: {$name}");
                    continue;
                }

                // simpan record migration
                $stmt = $pdo->prepare("INSERT INTO migrations (migration, batch) VALUES (?, ?)");
                $stmt->execute([$name, $batch]);

                $this->info("✓ OK");
            }
            catch (\Throwable $e) {
                $this->error("✗ Failed: " . $e->getMessage());
            }
        }

        $this->info("✓ Migration completed");
    }
}
