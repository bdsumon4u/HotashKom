<?php

namespace App\Listeners;

use App\Events\ProductCreated;
use App\Events\ProductUpdated;

class ManageProductImages
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
        $order = 0;
        $images = [$event->data['base_image'] => ['img_type' => 'base']];
        foreach ($event->data['additional_images'] ?? [] as $additional_image) {
            $additional_image != $event->data['base_image'] && (
                $images[$additional_image] = ['img_type' => 'additional', 'order' => ++$order]
            );
        }

        $event->product->images()->sync($images);
    }
}
