<?php
namespace Wasf\Support;

class Config
{
    protected static array $items = [];

    /**
     * Load all config/*.php files
     */
    public static function load(string $path)
    {
        if (!is_dir($path)) {
            return;
        }

        foreach (glob($path . '/*.php') as $file) {

            $key = basename($file, '.php');

            $value = require $file;

            // hanya terima array
            if (is_array($value)) {
                static::$items[$key] = $value;
            }
        }
    }

    /**
     * Get config value using dot notation
     */
    public static function get(string $key, $default = null)
    {
        if (empty(static::$items)) {
            return $default;
        }

        [$group, $item] = array_pad(explode('.', $key, 2), 2, null);

        if (!isset(static::$items[$group])) {
            return $default;
        }

        if ($item === null) {
            return static::$items[$group] ?? $default;
        }

        return static::$items[$group][$item] ?? $default;
    }
}
