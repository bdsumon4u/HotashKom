<?php

namespace App\Models;

use App\Events\ProductCreated;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Laravel\Scout\Searchable;
use Nicolaslopezj\Searchable\SearchableTrait;

class Product extends Model
{
    use Searchable;
    // use SearchableTrait;

    protected $with = ['images'];

    protected $fillable = [
        'brand_id', 'name', 'slug', 'description', 'price', 'selling_price', 'wholesale', 'sku',
        'should_track', 'stock_count', 'desc_img', 'desc_img_pos', 'is_active', 'shipping_inside', 'shipping_outside', 'delivery_text',
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
    protected static function booted()
    {
        static::saved(function ($product): void {
            if (App::runningInConsole()) {
                if ($product->categories->isEmpty() || $product->images->isEmpty()) {
                    $categories = range(1, 30);
                    $categories = array_map(fn($key) => $categories[$key], array_rand($categories, mt_rand(2, 4)));

                    $additionals = range(47, 67);
                    $additionals = array_map(fn($key) => $additionals[$key], array_rand($additionals, mt_rand(4, 7)));

                    ProductCreated::dispatch($product, [
                        'categories' => $categories,
                        'base_image' => mt_rand(47, 67),
                        'additional_images' => $additionals,
                    ]);
                }
            }
        });

        static::deleting(function ($product): void {
            $product->variations->each->delete();
        });

        static::addGlobalScope('latest', function (Builder $builder): void {
            $builder->latest('products.created_at');
        });
    }

    public function getVarNameAttribute()
    {
        if (! $this->parent_id) {
            return $this->name;
        }

        return $this->parent->name.' ['.$this->name.']';
    }

    public function getShippingInsideAttribute($value)
    {
        if (! $this->parent_id) {
            return $value ?? setting('delivery_charge')->inside_dhaka;
        }

        return $value ?? $this->parent->shipping_inside ?? setting('delivery_charge')->inside_dhaka;
    }

    public function getShippingOutsideAttribute($value)
    {
        if (! $this->parent_id) {
            return $value ?? setting('delivery_charge')->outside_dhaka;
        }

        return $value ?? $this->parent->shipping_outside ?? setting('delivery_charge')->outside_dhaka;
    }

    public function getCategoryAttribute()
    {
        if ($this->parent_id) {
            return $this->parent->categories()->inRandomOrder()->first(['name'])->name ?? 'Uncategorized';
        }

        return $this->categories()->inRandomOrder()->first(['name'])->name ?? 'Uncategorized';
    }

    public function getInStockAttribute()
    {
        return $this->track_stock
            ? $this->stock_count
            : true;
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

    public function setWholesaleAttribute($value)
    {
        $data = [];
        foreach ($value['quantity'] as $key => $quantity) {
            $data[$quantity] = $value['price'][$key];
        }
        ksort($data);
        $this->attributes['wholesale'] = json_encode($data);
    }

    public function getWholesaleAttribute($value)
    {
        $data = json_decode((string) $value, true) ?? [];
        if (empty($data) && $this->parent_id) {
            return $this->parent->wholesale;
        }

        return [
            'quantity' => array_keys($data),
            'price' => array_values($data),
        ];
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

    public function getBaseImageAttribute()
    {
        $images = $this->images ?? collect();
        if ($images->isEmpty()) {
            $images = $this->parent->images ?? collect();
        }

        return $images->first(fn(Image $image) => $image->pivot->img_type == 'base');
    }

    public function getAdditionalImagesAttribute()
    {
        $images = $this->images ?? collect();
        if ($images->isEmpty()) {
            $images = $this->parent->images ?? collect();
        }

        return $images->filter(fn(Image $image) => $image->pivot->img_type == 'additional');
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
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
}
