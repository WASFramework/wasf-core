<?php
namespace Wasf\Support;

abstract class Facade
{
    protected static $resolved = [];

    /**
     * Setiap facade harus mengembalikan nama service container binding.
     */
    abstract protected static function getFacadeAccessor();

    /**
     * Resolve instance dari container
     */
    protected static function resolve()
    {
        $name = static::getFacadeAccessor();

        if (isset(static::$resolved[$name])) {
            return static::$resolved[$name];
        }

        if (!isset($GLOBALS['app'])) {
            throw new \Exception("Application container not initialized.");
        }

        $instance = $GLOBALS['app']->make($name);

        static::$resolved[$name] = $instance;

        return $instance;
    }

    /**
     * Magic call → panggil method instance seperti static call
     */
    public static function __callStatic($method, $args)
    {
        $instance = static::resolve();

        if (!$instance) {
            throw new \Exception("Facade root for [" . static::class . "] is null.");
        }

        return $instance->$method(...$args);
    }
}
