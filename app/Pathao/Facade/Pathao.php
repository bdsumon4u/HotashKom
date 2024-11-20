<?php

namespace App\Pathao\Facade;

use Illuminate\Support\Facades\Facade;

/**
 * @method static mixed area()
 * @method static mixed store()
 * @method static mixed order()
 *
 * @see Manage
 */
class Pathao extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'pathao';
    }
}
