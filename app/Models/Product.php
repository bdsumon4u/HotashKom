<?php

namespace App\Models;

use App\Jobs\RemoveResourceFromResellers;
use App\Jobs\SyncProductStockWithResellers;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Scout\Searchable;
use Nicolaslopezj\Searchable\SearchableTrait;

class Product extends Model
{
    use Searchable;
    // use SearchableTrait;

    protected $with = ['images'];

    protected $fillable = [
        'brand_id', 'name', 'slug', 'description', 'price', 'average_purchase_price', 'selling_price', 'suggested_price', 'wholesale', 'sku',
        'source_id', 'should_track', 'stock_count', 'desc_img', 'desc_img_pos', 'is_active', 'hot_sale', 'new_arrival', 'shipping_inside', 'shipping_outside', 'delivery_text',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
        'hot_sale' => 'boolean',
        'new_arrival' => 'boolean',
        'should_track' => 'boolean',
    ];

    /**
     * Searchable rules.
     *
     * @var array
     */
    protected $searchable = [
        /**
         * Columns and their priority in search results.
         * Columns with higher values are more important.
         * Columns with equal values have equal importance.
         *
         * @var array
         */
        'columns' => [
            'products.sku' => 10,
            'products.name' => 8,
            'products.description' => 5,
        ],
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    public static function booted()
    {
        static::saved(function ($product): void {
            // Dispatch job to sync stock attributes if they were changed
            if (isOninda() && $product->isDirty(['should_track', 'stock_count'])) {
                SyncProductStockWithResellers::dispatch($product);
            }
        });

        static::deleting(function ($record): void {
            if (isReseller() && $record->source_id !== null) {
                throw new \Exception('Cannot delete a resource that has been sourced.');
            }

            // Dispatch job to remove product from reseller databases
            if (! $record->parent_id && isOninda()) { // not a variation
                RemoveResourceFromResellers::dispatch($record->getTable(), $record->id);
            }
            $record->variations->each->delete();
        });

        static::addGlobalScope('latest', function (Builder $builder): void {
            $builder->latest('products.created_at');
        });
    }

    protected function varName(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: function () {
            if (! $this->parent_id) {
                return $this->name;
            }

            return $this->parent->name.' ['.$this->name.']';
        });
    }

    protected function shippingInside(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: function ($value) {
            if (! (setting('show_option')->productwise_delivery_charge ?? false)) {
                return setting('delivery_charge')->inside_dhaka;
            }

            if (! $this->parent_id) {
                return $value ?? setting('delivery_charge')->inside_dhaka;
            }

            return $this->parent->shipping_inside;
        });
    }

    protected function shippingOutside(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: function ($value) {
            if (! (setting('show_option')->productwise_delivery_charge ?? false)) {
                return setting('delivery_charge')->outside_dhaka;
            }

            if (! $this->parent_id) {
                return $value ?? setting('delivery_charge')->outside_dhaka;
            }

            return $this->parent->shipping_outside;
        });
    }

    protected function category(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: function () {
            if ($this->parent_id) {
                return $this->parent->categories()->inRandomOrder()->first(['name'])->name ?? 'Uncategorized';
            }

            return $this->categories()->inRandomOrder()->first(['name'])->name ?? 'Uncategorized';
        });
    }

    protected function inStock(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: fn () => $this->track_stock
            ? $this->stock_count
            : true);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function images()
    {
        return $this->belongsToMany(Image::class)
            ->withPivot(['img_type', 'order'])
            ->orderBy('order')
            ->withTimestamps();
    }

    public function parent()
    {
        return $this->belongsTo(Product::class, 'parent_id');
    }

    public function variations()
    {
        return $this->hasMany(Product::class, 'parent_id');
    }

    public function options()
    {
        return $this->belongsToMany(Option::class);
    }

    public function purchases()
    {
        return $this->belongsToMany(Purchase::class, 'product_purchase')
            ->withPivot(['price', 'quantity', 'subtotal'])
            ->withTimestamps();
    }

    protected function wholesale(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: function ($value) {
            $data = json_decode((string) $value, true) ?? [];
            if (empty($data) && $this->parent_id) {
                return $this->parent->wholesale;
            }

            return [
                'quantity' => array_keys($data),
                'price' => array_values($data),
            ];
        }, set: function ($value) {
            $data = [];
            foreach (($value['quantity'] ?? []) as $key => $quantity) {
                $data[$quantity] = $value['price'][$key];
            }
            ksort($data);

            return ['wholesale' => json_encode($data)];
        });
    }

    public function getPrice(int $quantity)
    {
        $wholesale = $this->wholesale;
        $price = $this->selling_price;

        foreach ($wholesale['quantity'] as $key => $value) {
            if ($quantity >= $value) {
                $price = $wholesale['price'][$key];
            }
        }

        return $price;
    }

    public function retailPrice(): int
    {
        $price = $this->suggested_price;

        if (is_string($price) && preg_match('/^\s*(\d+(?:\.\d+)?)\s*-\s*(\d+(?:\.\d+)?)\s*$/', $price, $matches)) {
            $low = (float) $matches[1];
            $high = (float) $matches[2];

            return (int) round(($low + $high) / 2);
        }

        if (is_numeric($price) && $price > 0) {
            return (int) round($price);
        }

        return (int) round($this->selling_price * 1.4);
    }

    public function suggestedRetailPrice(): string
    {
        if ($this->suggested_price) {
            return '৳'.$this->suggested_price;
        }

        return sprintf('৳%d - ৳%d', round($this->selling_price * 1.3), round($this->selling_price * 1.5));
    }

    protected function baseImage(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: function () {
            $images = $this->images ?? collect();
            if ($images->isEmpty()) {
                $images = $this->parent->images ?? collect();
            }

            return $images->first(fn (Image $image): bool => $image->pivot->img_type == 'base');
        });
    }

    protected function additionalImages(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: function () {
            $images = $this->images ?? collect();
            if ($images->isEmpty()) {
                $images = $this->parent->images ?? collect();
            }

            return $images->filter(fn (Image $image): bool => $image->pivot->img_type == 'additional');
        });
    }

    public function landings(): HasMany
    {
        return $this->hasMany(Landing::class);
    }

    /**
     * Get the indexable data array for the model.
     */
    public function toSearchableArray(): array
    {
        return [
            'sku' => $this->sku,
            'name' => $this->name,
        ];
    }

    public function shouldBeSearchable()
    {
        return $this->is_active;
    }

    public static function stockStatistics(): array
    {
        $products = static::where('should_track', true)->where('stock_count', '>', 0)->get([
            'id', 'stock_count', 'average_purchase_price', 'selling_price',
        ]);
        $totalStockCount = $products->sum('stock_count');
        $totalPurchaseValue = $products->sum(function ($product) {
            return $product->stock_count * ($product->average_purchase_price ?? $product->selling_price);
        });
        $totalSellValue = $products->sum(function ($product) {
            return $product->stock_count * $product->selling_price;
        });

        return [
            'totalStockCount' => $totalStockCount,
            'totalPurchaseValue' => theMoney($totalPurchaseValue),
            'totalSellValue' => theMoney($totalSellValue),
        ];
    }
}
