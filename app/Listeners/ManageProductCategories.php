<?php

namespace App\Listeners;

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
     */
    public function handle($event): void
    {
        $event->product->categories()->sync($event->data['categories']);
    }
}
