<?php

namespace App\Pathao\Manage;

use App\Pathao\Apis\AreaApi;
use App\Pathao\Apis\OrderApi;
use App\Pathao\Apis\StoreApi;

class Manage
{
    public function __construct(private readonly AreaApi $area, private readonly StoreApi $store, private readonly OrderApi $order) {}

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
