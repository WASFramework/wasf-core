<?php
namespace Wasf\Session;

class Session
{
    public static function start()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function get(string $key, $default = null)
    {
        static::start();
        return $_SESSION[$key] ?? $default;
    }

    public static function put(string $key, $value)
    {
        static::start();
        $_SESSION[$key] = $value;
    }

    public static function forget(string $key)
    {
        static::start();
        unset($_SESSION[$key]);
    }
}
