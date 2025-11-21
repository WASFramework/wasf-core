<?php
namespace Wasf\Database;

class Blueprint
{
    public string $table;
    public array $columns = [];

    public function __construct(string $table)
    {
        $this->table = $table;
    }

    public function id(string $name = 'id'): ColumnDefinition
    {
        return $this->addColumn($name, 'id');
    }

    public function string(string $name, int $length = 255): ColumnDefinition
    {
        return $this->addColumn($name, "VARCHAR($length)");
    }

    public function integer(string $name): ColumnDefinition
    {
        return $this->addColumn($name, "INT");
    }

    public function text(string $name): ColumnDefinition
    {
        return $this->addColumn($name, "TEXT");
    }

    public function boolean(string $name): ColumnDefinition
    {
        return $this->addColumn($name, "TINYINT(1)");
    }

    public function timestamps(): void
    {
        $this->addColumn('created_at', "TIMESTAMP")->default('CURRENT_TIMESTAMP');
        $this->addColumn('updated_at', "TIMESTAMP")
             ->default('CURRENT_TIMESTAMP')
             ->onUpdate();
    }

    public function addColumn(string $name, string $type): ColumnDefinition
    {
        $col = new ColumnDefinition($name, $type);
        $this->columns[$name] = $col;
        return $col;
    }
}
