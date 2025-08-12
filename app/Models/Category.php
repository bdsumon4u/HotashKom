<?php

namespace App\Models;

use App\Jobs\CopyResourceToResellers;
use App\Jobs\RemoveResourceFromResellers;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'parent_id', 'image_id', 'name', 'slug', 'order', 'is_enabled',
    ];

    public static function booted(): void
    {
        static::saved(function ($category): void {
            cache()->forget('categories:nested:');
            cache()->forget('categories:nested:1');
            cache()->forget('homesections');

            // Dispatch job to copy category to reseller databases
            if (isOninda() && $category->wasRecentlyCreated) {
                CopyResourceToResellers::dispatch($category);
            }
        });

        static::deleting(function ($category): void {
            if (! isOninda() && $category->source_id !== null) {
                throw new \Exception('Cannot delete a resource that has been sourced.');
            }

            // Dispatch job to remove category from reseller databases
            if (isOninda()) {
                RemoveResourceFromResellers::dispatch($category->getTable(), $category->id);
            }
            $category->childrens->each->delete();
        });

        static::deleted(function ($category): void {
            cache()->forget('categories:nested:');
            cache()->forget('categories:nested:1');
            cache()->forget('homesections');
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

        return cache()->rememberForever('categories:nested:'.$enabledOnly, fn () => $query->get());
    }

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

    public function categoryMenu()
    {
        return $this->hasOne(CategoryMenu::class);
    }
}
