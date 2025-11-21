<?php
namespace Wasf\Support;

class Dotenv
{
    public static function load(string $basePath): void
    {
        $file = $basePath . DIRECTORY_SEPARATOR . '.env';
        if (!file_exists($file)) return;

        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            if (str_starts_with(trim($line), '#')) continue;
            if (!str_contains($line, '=')) continue;

            [$key, $value] = explode('=', $line, 2);

            $key   = trim($key);
            $value = trim($value);

            // remove quotes
            $value = trim($value, "\"'");

            putenv("$key=$value");
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }
}
