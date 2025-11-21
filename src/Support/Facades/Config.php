<?php
namespace Wasf\Support\Facades;

use Wasf\Support\Facade;

class Config extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Wasf\Support\Config::class;
    }
}
