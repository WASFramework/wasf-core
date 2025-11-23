<?php

namespace Wasf\Filesystem;

class File
{
    public static function exists(string $path): bool
    {
        return file_exists($path);
    }

    public static function delete(string $path): bool
    {
        return file_exists($path) ? unlink($path) : false;
    }

    public static function makeDirectory(string $path, int $mode = 0755): bool
    {
        if (!is_dir($path)) {
            return mkdir($path, $mode, true);
        }
        return true;
    }

    public static function move(string $from, string $to): bool
    {
        return rename($from, $to);
    }
}
