<?php
namespace Wasf\ORM;

use Wasf\Database\DB;
use ReflectionClass;

abstract class Model
{
    protected static string $table = '';
    protected array $attributes = [];
    protected array $fillable = []; // protection for request input ONLY

    /* ----------------------------------------------------------
     * CONSTRUCTOR
     * ---------------------------------------------------------- */
    public function __construct(array $attrs = [], bool $fromDb = false)
    {
        if ($fromDb) {
            $this->loadAttributes($attrs);  // direct load from DB
        } else {
            $this->fill($attrs);           // request input (fillable)
        }
    }

    /* ----------------------------------------------------------
     * TABLE NAME
     * ---------------------------------------------------------- */
    public static function table(): string
    {
        if (static::$table) return static::$table;

        $class = (new ReflectionClass(static::class))->getShortName();
        return strtolower($class) . 's';
    }

    /* ----------------------------------------------------------
     * PDO CONNECTION
     * ---------------------------------------------------------- */
    protected static function db()
    {
        return DB::pdo();
    }

    /* ----------------------------------------------------------
     * LOAD DB ATTRIBUTES (IGNORES FILLABLE)
     * ---------------------------------------------------------- */
    protected function loadAttributes(array $data)
    {
        $this->attributes = $data;
    }

    /* ----------------------------------------------------------
     * FIND
     * ---------------------------------------------------------- */
    public static function find($id)
    {
        $table = static::table();
        $pdo = DB::pdo();

        $stmt = $pdo->prepare("SELECT * FROM {$table} WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$row) return null;

        return new static($row, true); // flag "from DB"
    }

    /* ----------------------------------------------------------
     * CREATE
     * ---------------------------------------------------------- */
    public static function create(array $data)
    {
        $model = new static();
        $data = $model->filterFillable($data);

        $table = static::table();

        $fields = array_keys($data);
        $placeholders = array_map(fn($f) => ':' . $f, $fields);

        $sql = "INSERT INTO {$table} (" . implode(',', $fields) . ")
                VALUES (" . implode(',', $placeholders) . ")";

        $stmt = self::db()->prepare($sql);
        $stmt->execute($data);

        $id = self::db()->lastInsertId();

        return static::find($id);
    }

    /* ----------------------------------------------------------
     * UPDATE
     * ---------------------------------------------------------- */
    public function update(array $data)
    {
        $data = $this->filterFillable($data);

        $table = static::table();
        $sets = implode(', ', array_map(fn($f) => "{$f} = :{$f}", array_keys($data)));

        $data['id'] = $this->id;

        $sql = "UPDATE {$table} SET {$sets} WHERE id = :id";
        $stmt = self::db()->prepare($sql);
        $stmt->execute($data);

        return $this->refresh();
    }

    /* ----------------------------------------------------------
     * DELETE
     * ---------------------------------------------------------- */
    public function delete()
    {
        $table = static::table();

        $stmt = self::db()->prepare("DELETE FROM {$table} WHERE id = ?");
        return $stmt->execute([$this->id]);
    }

    /* ----------------------------------------------------------
     * SAVE → INSERT OR UPDATE
     * ---------------------------------------------------------- */
    public function save()
    {
        // if record has ID, update
        if (!empty($this->attributes['id'])) {
            return $this->update($this->attributes);
        }

        // else create new
        return static::create($this->attributes);
    }

    /* ----------------------------------------------------------
     * FILL (request input only)
     * ---------------------------------------------------------- */
    public function fill(array $data)
    {
        $allowed = $this->filterFillable($data);

        foreach ($allowed as $key => $value) {
            $this->attributes[$key] = $value;
        }

        return $this;
    }

    protected function filterFillable(array $data)
    {
        if (empty($this->fillable)) {
            return $data;
        }

        return array_filter(
            $data,
            fn($key) => in_array($key, $this->fillable),
            ARRAY_FILTER_USE_KEY
        );
    }

    /* ----------------------------------------------------------
     * MAGIC GET/SET
     * ---------------------------------------------------------- */
    public function __get($key)
    {
        return $this->attributes[$key] ?? null;
    }

    public function __set($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    /* ----------------------------------------------------------
     * REFRESH FROM DB
     * ---------------------------------------------------------- */
    public function refresh()
    {
        return static::find($this->id);
    }

    /* ----------------------------------------------------------
     * QUERY BUILDER
     * ---------------------------------------------------------- */
    public static function query()
    {
        return new QueryBuilder(static::class);
    }

    public static function all()
    {
        return static::query()->get();
    }

    public static function where($column, $value)
    {
        return static::query()->where($column, $value);
    }

    /* ----------------------------------------------------------
     * TO ARRAY
     * ---------------------------------------------------------- */
    public function toArray()
    {
        return $this->attributes;
    }
}
