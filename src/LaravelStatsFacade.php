<?php

namespace ToneflixCode\Stats;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Test\Test\Skeleton\SkeletonClass
 */
class LaravelStatsFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel-stats';
    }
}
