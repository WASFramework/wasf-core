<?php
namespace Wasf\ORM;
use Wasf\Database\DB;
class QueryBuilder
{
    protected string $modelClass;
    protected array $wheres = [];
    protected array $bindings = [];
    public function __construct(string $modelClass)
    {
        $this->modelClass = $modelClass;
    }
    public function where(string $col, $val): self
    {
        $this->wheres[] = "{$col} = ?";
        $this->bindings[] = $val;
        return $this;
    }
    public function first()
    {
        $table = $this->modelClass::table();
        $sql = "SELECT * FROM {$table}" . ($this->wheres ? ' WHERE '.implode(' AND ', $this->wheres) : '') . ' LIMIT 1';
        $stmt = DB::pdo()->prepare($sql);
        $stmt->execute($this->bindings);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$row) return null;
        return new $this->modelClass($row);
    }
    public function get()
    {
        $table = $this->modelClass::table();
        $sql = "SELECT * FROM {$table}" . ($this->wheres ? ' WHERE '.implode(' AND ', $this->wheres) : '');
        $stmt = DB::pdo()->prepare($sql);
        $stmt->execute($this->bindings);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $res = [];
        foreach ($rows as $r) $res[] = new $this->modelClass($r);
        return $res;
    }
    public function exists(): bool
    {
        $table = $this->modelClass::table();
        $sql = "SELECT 1 FROM {$table}" . ($this->wheres ? ' WHERE '.implode(' AND ', $this->wheres) : '') . " LIMIT 1";

        $stmt = DB::pdo()->prepare($sql);
        $stmt->execute($this->bindings);

        return (bool)$stmt->fetchColumn();
    }
}
