<?php

namespace Egate\Tlync\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Egate\Tlync\Tlync
 */
class Tlync extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Egate\Tlync\Tlync::class;
    }
}
