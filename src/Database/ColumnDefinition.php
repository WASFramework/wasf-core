<?php
namespace Wasf\Database;

class ColumnDefinition
{
    public string $name;
    public string $type;
    public array $modifiers = [];

    public function __construct(string $name, string $type)
    {
        $this->name = $name;
        $this->type = $type;
    }

    public function nullable(bool $val = true)
    {
        $this->modifiers['nullable'] = $val;
        return $this;
    }

    public function default($val)
    {
        $this->modifiers['default'] = $val;
        return $this;
    }

    public function unique(bool $val = true)
    {
        $this->modifiers['unique'] = $val;
        return $this;
    }

    public function unsigned(bool $val = true)
    {
        $this->modifiers['unsigned'] = $val;
        return $this;
    }

    public function onUpdate($val = 'CURRENT_TIMESTAMP'): self
    {
        $this->modifiers['on_update'] = $val;
        return $this;
    }
}
