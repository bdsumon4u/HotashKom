<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LandingPageProItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'landing_page_pro_id',
        'product_id',
        'free_delivery',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'free_delivery' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function landingPagePro(): BelongsTo
    {
        return $this->belongsTo(LandingPagePro::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
