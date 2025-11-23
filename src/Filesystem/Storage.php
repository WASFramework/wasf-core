<?php

namespace Wasf\Filesystem;

class Storage
{
    protected static string $defaultDisk = 'public';
    protected static array $disks = [];

    /**
     * Load filesystem configuration (lazy load)
     */
    protected static function loadConfig()
    {
        if (!empty(self::$disks)) return;

        $configPath = base_path('config/filesystems.php');

        if (file_exists($configPath)) {
            $config = include $configPath;

            self::$defaultDisk = $config['default'] ?? 'public';
            self::$disks = $config['disks'] ?? [];
        }
    }

    /**
     * Select disk storage
     */
    public static function disk(string $name = null): StorageDisk
    {
        self::loadConfig();

        $name = $name ?: self::$defaultDisk;

        if (!isset(self::$disks[$name])) {
            throw new \RuntimeException("Disk '{$name}' not found in config/filesystems.php");
        }

        return new StorageDisk($name, self::$disks[$name]);
    }

    /**
     * Magic call for Storage::put(...) using default disk
     */
    public static function __callStatic($method, $args)
    {
        return self::disk()->{$method}(...$args);
    }
}
