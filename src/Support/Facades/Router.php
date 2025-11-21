<?php
namespace Wasf\Support\Facades;

use Wasf\Support\Facade;

class Router extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Wasf\Routing\Router::class;
    }
}
