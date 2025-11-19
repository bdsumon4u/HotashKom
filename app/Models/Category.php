<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'parent_id', 'image_id', 'name', 'slug', 'order', 'is_enabled',
    ];

    #[\Override]
    public static function booted(): void
    {
        static::saved(function ($category): void {
            cacheMemo()->forget('categories:nested:');
            cacheMemo()->forget('categories:nested:1');
            cacheMemo()->forget('homesections');
            cacheMemo()->forget('product_filter_data');
            cacheMemo()->forget('product_filter_data:category:'.$category->id);
            cacheMemo()->forget('api_categories:all');
            cacheMemo()->forget('api_category:'.$category->slug);
            for ($i = 0; $i <= 5; $i++) {
                cacheMemo()->forget("api_categories:nested:{$i}");
            }

            // Dispatch job to copy category to reseller databases
            if (isOninda() && $category->wasRecentlyCreated) {
                dispatch(new \App\Jobs\CopyResourceToResellers($category));
            }
        });

        static::deleting(function ($category): void {
            // throw_if(isReseller() && $category->source_id !== null, \Exception::class, 'Cannot delete a resource that has been sourced.');

            // Dispatch job to remove category from reseller databases
            if (isOninda()) {
                dispatch(new \App\Jobs\RemoveResourceFromResellers($category->getTable(), $category->id));
            }
            $category->childrens->each->delete();
        });

        static::deleted(function ($category): void {
            cacheMemo()->forget('categories:nested:');
            cacheMemo()->forget('categories:nested:1');
            cacheMemo()->forget('homesections');
            cacheMemo()->forget('product_filter_data');
            cacheMemo()->forget('product_filter_data:category:'.$category->id);
            cacheMemo()->forget('api_categories:all');
            cacheMemo()->forget('api_category:'.$category->slug);
            for ($i = 0; $i <= 5; $i++) {
                cacheMemo()->forget("api_categories:nested:{$i}");
            }
        });
    }

    public function childrens()
    {
        return $this->hasMany(static::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(static::class, 'parent_id');
    }

    public function image()
    {
        return $this->belongsTo(Image::class);
    }

    public static function nested($count = 0, $enabledOnly = true)
    {
        $query = self::whereNull('parent_id')
            ->when($enabledOnly, fn ($query) => $query->where('is_enabled', true))
            ->with(['childrens' => function ($category) use ($enabledOnly): void {
                $category->when($enabledOnly, fn ($query) => $query->where('is_enabled', true))->with('childrens')->orderBy('order');
            }])
            ->withCount('childrens')
            ->orderBy('order');
        $count && $query->take($count);

        if ($count) {
            return $query->get();
        }

        return cacheMemo()->rememberForever('categories:nested:'.$enabledOnly, fn () => $query->get());
    }

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

    public function categoryMenu()
    {
        return $this->hasOne(CategoryMenu::class);
    }

    /**
     * Retrieve the model for route model binding.
     *
     * @param  mixed  $value
     * @param  string|null  $field
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        $field = $field ?: 'slug';

        // Decode URL-encoded slug
        $decodedValue = rawurldecode((string) $value);

        return $this->where($field, $decodedValue)->first();
    }
}
