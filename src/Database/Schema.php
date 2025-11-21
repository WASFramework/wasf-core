<?php
namespace Wasf\Database;

use PDO;

class Schema
{
    protected static PDO $pdo;

    protected static array $debugSql = [];
    protected static bool $debug = false;

    public static function enableDebug()
    {
        self::$debug = true;
    }

    protected static function pushDebug(string $sql)
    {
        if (self::$debug) {
            self::$debugSql[] = $sql;
        }
    }

    public static function setConnection(PDO $pdo)
    {
        self::$pdo = $pdo;
    }

    public static function create(string $table, callable $callback)
    {
    $blueprint = new Blueprint($table);
        $callback($blueprint);

        $sql = self::buildCreateSQL($blueprint);

        try {
            self::$pdo->exec($sql);
        } catch (\Throwable $e) {
            echo "ERROR: " . $e->getMessage() . "\n";
        }
    }

    public static function dropIfExists(string $table)
    {
        self::$pdo->exec("DROP TABLE IF EXISTS `$table`");
    }


    /**
     * Convert Blueprint type → MySQL type
     */
    protected static function mapType(string $type): string
    {
        return match($type) {
            'string'    => 'VARCHAR(255)',
            'text'      => 'TEXT',
            'integer'   => 'INT',
            'boolean'   => 'TINYINT(1)',
            'datetime'  => 'DATETIME',
            'timestamp' => 'TIMESTAMP',
            default     => $type, // fallback
        };
    }


    protected static function buildCreateSQL(Blueprint $bp): string
    {
        $cols = [];

        foreach ($bp->columns as $col) {

            // Handle primary ID
            if ($col->type === 'id') {
                $cols[] = "`{$col->name}` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY";
                continue;
            }

            // Konversi tipe
            $sqlType = self::mapType($col->type);

            $line = "`{$col->name}` {$sqlType}";

            // Nullable
            $line .= !empty($col->modifiers['nullable']) ? " NULL" : " NOT NULL";

            // Unique
            if (!empty($col->modifiers['unique'])) {
                $line .= " UNIQUE";
            }

            // Default
            if (isset($col->modifiers['default'])) {
                $def = $col->modifiers['default'];

                if ($def === 'CURRENT_TIMESTAMP') {
                    $line .= " DEFAULT CURRENT_TIMESTAMP";
                } else {
                    $line .= " DEFAULT " . self::$pdo->quote($def);
                }
            }

            // ON UPDATE
            if (!empty($col->modifiers['on_update'])) {
                $line .= " ON UPDATE CURRENT_TIMESTAMP";
            }

            $cols[] = $line;
        }

        $columnsSQL = implode(",\n    ", $cols);

        return "
CREATE TABLE IF NOT EXISTS `{$bp->table}` (
    {$columnsSQL}
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";
    }
}
