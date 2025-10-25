<?php

namespace Hotash\LaravelMultiUi\Facades;

use Illuminate\Support\Facades\Facade;

class MultiUi extends Facade
{
    /**
     * Register the typical authentication routes for an application.
     *
     * @return void
     */
    public static function routes(array $options = [])
    {
        static::$app->make('router')->auth($options);
    }
}
