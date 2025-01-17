<?php

namespace App\Events;

use App\Models\Product;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var mixed[]
     */
    public $data;

    /**
     * @var \App\Models\Product
     */
    public $product;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Product $product, array $data = [])
    {
        $this->data = $data;
        $this->product = $product;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn(): \Illuminate\Broadcasting\PrivateChannel
    {
        return new PrivateChannel('channel-name');
    }
}
