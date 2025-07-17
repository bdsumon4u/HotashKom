<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'purchase_date',
        'supplier_name',
        'supplier_phone',
        'notes',
        'invoice_number',
        'total_amount',
    ];

    protected $casts = [
        'purchase_date' => 'date',
    ];

    public function productPurchases(): HasMany
    {
        return $this->hasMany(ProductPurchase::class);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_purchase')
            ->withPivot(['price', 'quantity', 'subtotal'])
            ->withTimestamps();
    }
}
