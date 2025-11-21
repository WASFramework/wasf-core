<?php
namespace Wasf\Database;
use PDO;
class DB
{
    protected static ?PDO $pdo = null;
    public static function connect(array $conf): PDO
    {
        if (self::$pdo) return self::$pdo;
        $driver = $conf['driver'] ?? 'mysql';
        if ($driver === 'sqlite') {
            $dsn = 'sqlite:' . ($conf['database'] ?? ':memory:');
            $pdo = new PDO($dsn);
        } else {
            $host = $conf['host'] ?? '127.0.0.1';
            $port = $conf['port'] ?? 3306;
            $dbname = $conf['database'] ?? '';
            $user = $conf['username'] ?? 'root';
            $pass = $conf['password'] ?? '';
            $dsn = "{$driver}:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";
            $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        }
        self::$pdo = $pdo;
        return $pdo;
    }
    public static function pdo(): ?PDO
    {
        return self::$pdo;
    }
}
