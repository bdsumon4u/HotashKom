<?php

namespace App\Redx\Manage;

use App\Redx\Apis\AreaApi;
use App\Redx\Apis\OrderApi;
use App\Redx\Apis\StoreApi;

class Manage
{
    public function __construct(private readonly AreaApi $area, private readonly StoreApi $store, private readonly OrderApi $order)
    {
    }

    public function area(): AreaApi
    {
        return $this->area;
    }

    public function store(): StoreApi
    {
        return $this->store;
    }

    public function order(): OrderApi
    {
        return $this->order;
    }
}
