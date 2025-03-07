<?php

namespace App\Redx\Facade;

use App\Redx\Manage\Manage;
use Illuminate\Support\Facades\Facade;

/**
 * @method static mixed area()
 * @method static mixed store()
 * @method static mixed order()
 *
 * @see Manage
 */
class Redx extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'redx';
    }
}
