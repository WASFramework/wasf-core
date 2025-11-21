<?php
namespace Wasf\Console\Commands;

use Wasf\Database\DB;
use Wasf\Database\Schema;

class MigrateFresh extends Command
{
    public function signature(): string { return 'migrate:fresh'; }
    public function description(): string { return 'Drop all tables and re-run migrations (MySQL/SQLite supported)'; }

    public function handle(array $args): void
    {
        $dbConfFile = getcwd() . '/config/database.php';
        if (!file_exists($dbConfFile)) {
            $this->error("No database config found at config/database.php");
            return;
        }

        $conf = require $dbConfFile;
        $pdo = DB::connect($conf);

        // FIX WAJIB → SET SCHEMA CONNECTION
        Schema::setConnection($pdo);

        $driver = $conf['driver'] ?? 'mysql';

        try {
            if ($driver === 'sqlite') {
                $this->info("Dropping all SQLite tables...");
                $rows = $pdo->query("SELECT name FROM sqlite_master WHERE type='table'")->fetchAll(\PDO::FETCH_COLUMN);
                foreach ($rows as $t) {
                    if ($t === 'sqlite_sequence') continue;
                    $pdo->exec("DROP TABLE IF EXISTS `{$t}`;");
                }
            }
            else {
                $db = $conf['database'];
                $this->info("Dropping all MySQL tables in database {$db}...");

                $stmt = $pdo->query(
                    "SELECT table_name FROM information_schema.tables WHERE table_schema = " . $pdo->quote($db)
                );

                $tables = $stmt->fetchAll(\PDO::FETCH_COLUMN);

                foreach ($tables as $t) {
                    $pdo->exec("DROP TABLE IF EXISTS `{$t}`;");
                }
            }

            $this->info("All tables dropped. Running migrations...");

            // CALL MIGRATE PROPERLY
            $migrate = new Migrate();
            $migrate->handle([]);

        } catch (\Throwable $e) {
            $this->error("Failed: " . $e->getMessage());
        }
    }
}
