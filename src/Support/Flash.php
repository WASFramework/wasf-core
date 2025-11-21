<?php

namespace Wasf\Support;

class Flash
{
    public static function set(string $key, string $value)
    {
        $_SESSION['flash'][$key] = $value;
    }

    public static function get(string $key)
    {
        if (!isset($_SESSION['flash'][$key])) {
            return null;
        }

        $msg = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]); // auto-clear
        return $msg;
    }
}
