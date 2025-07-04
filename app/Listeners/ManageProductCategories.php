<?php

namespace App\Listeners;

use App\Events\ProductCreated;
use App\Events\ProductUpdated;

class ManageProductCategories
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(ProductCreated|ProductUpdated $event)
    {
        $event->product->categories()->sync($event->data['categories']);
    }
}
