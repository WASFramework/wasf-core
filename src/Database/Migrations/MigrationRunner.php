<?php
namespace Wasf\Database\Migrations;

use PDO;
use Wasf\Database\Schema;
use Wasf\Database\DB;

class MigrationRunner
{
    protected PDO $pdo;
    protected string $migrationPath;

    public function __construct(PDO $pdo, string $migrationPath)
    {
        $this->pdo = $pdo;
        $this->migrationPath = rtrim($migrationPath, '/');
    }

    /**
     * Run all migration *.php files
     */
    public static function run()
    {
        $base = getcwd();

        $dbConf = require $base . "/config/database.php";
        $pdo = DB::connect($dbConf);
        Schema::setConnection($pdo);

        // ensure migrations table
        $pdo->exec("CREATE TABLE IF NOT EXISTS migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255) UNIQUE,
            batch INT
        )");

        $files = glob($base . "/database/migrations/*.php");

        foreach ($files as $file) {

            $name = basename($file);

            $exists = $pdo->prepare("SELECT * FROM migrations WHERE migration=?");
            $exists->execute([$name]);

            if ($exists->fetch()) continue;

            echo "Running: {$name}\n";

            $migration = require $file;

            try {
                $migration->up();  // MIGRASI DIJALANKAN
                $pdo->prepare("INSERT INTO migrations (migration, batch) VALUES (?, 1)")
                    ->execute([$name]);
                echo "✓ OK\n";
            } catch (\Throwable $e) {
                echo "✗ Failed: {$e->getMessage()}\n";
            }
        }
    }

    /**
     * Rollback last batch
     */
    public static function rollback()
    {
        $base = getcwd();

        $dbConf = require $base . "/config/database.php";
        $pdo = DB::connect($dbConf);
        Schema::setConnection($pdo);

        $files = glob($base . "/database/migrations/*.php");

        foreach (array_reverse($files) as $file) {

            $name = basename($file);

            $exists = $pdo->prepare("SELECT * FROM migrations WHERE migration=?");
            $exists->execute([$name]);

            if (!$exists->fetch()) continue;

            echo "Rollback: {$name}\n";

            $migration = require $file;

            try {
                $migration->down(); // ROLLBACK
                $pdo->prepare("DELETE FROM migrations WHERE migration=?")
                    ->execute([$name]);
                echo "✓ Rolled back\n";
            } catch (\Throwable $e) {
                echo "✗ Failed: {$e->getMessage()}\n";
            }
        }
    }
}
