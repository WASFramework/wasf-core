<?php
namespace Wasf\Support\Facades;

use Wasf\Support\Facade;

class Blade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Wasf\View\Blade::class;
    }
}
