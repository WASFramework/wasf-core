<?php
namespace Wasf\Support\Facades;

use Wasf\Support\Facade;

class DB extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Wasf\Database\DB::class;
    }
}
