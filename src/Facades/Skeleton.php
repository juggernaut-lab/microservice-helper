<?php

namespace Gopaddi\PaddiHelper\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Gopaddi\PaddiHelper\Skeleton
 */
class Skeleton extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Gopaddi\PaddiHelper\Skeleton::class;
    }
}
