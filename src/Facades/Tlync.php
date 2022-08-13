<?php

namespace Elshaden\Tlync\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Elshaden\Tlync\Tlync
 */
class Tlync extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Elshaden\Tlync\Tlync::class;
    }
}
