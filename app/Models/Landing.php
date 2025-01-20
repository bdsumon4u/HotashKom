<?php

namespace App\Models;

use Z3d0X\FilamentFabricator\Models\Page;

class Landing extends Page
{
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
