<?php
namespace Wasf\Database;
use PDO;
class DB
{
    protected static ?PDO $pdo = null;
    public static function connect(array $conf): PDO
    {
        if (self::$pdo) return self::$pdo;

        $driver  = $conf['driver'] ?? 'mysql';
        $host    = $conf['host'] ?? '127.0.0.1';
        $port    = $conf['port'] ?? 3306;
        $dbname  = $conf['database'] ?? '';
        $user    = $conf['username'] ?? 'root';
        $pass    = $conf['password'] ?? '';

        // === 1. Auto-create database (MySQL saja) ===
        if ($driver === 'mysql' && !empty($dbname)) {
            try {
                // connect tanpa dbname
                $pdo = new PDO(
                    "{$driver}:host={$host};port={$port}",
                    $user,
                    $pass,
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                );

                // CREATE DATABASE jika belum ada
                $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbname}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            } catch (\Throwable $e) {
                throw new \Exception("Failed creating database '{$dbname}': " . $e->getMessage());
            }
        }

        // === 2. Connect normal ===
        if ($driver === 'sqlite') {
            $dsn = 'sqlite:' . ($conf['database'] ?? ':memory:');
            $pdo = new PDO($dsn);
        } else {
            $dsn = "{$driver}:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";
            $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        }

        return self::$pdo = $pdo;
    }
    public static function pdo(): ?PDO
    {
        return self::$pdo;
    }
}
