<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'parent_id', 'image_id', 'name', 'slug', 'order',
    ];

    public static function booted()
    {
        static::saved(function ($category) {
            cache()->forget('categories:nested');
            cache()->forget('homesections');
            // cache()->forget('catmenu:nested');
            // cache()->forget('catmenu:nestedwithparent');
        });

        static::deleting(function ($category) {
            $category->childrens->each->delete();
            // optional($category->categoryMenu)->delete();
            cache()->forget('categories:nested');
            cache()->forget('homesections');
            // cache()->forget('catmenu:nested');
            // cache()->forget('catmenu:nestedwithparent');
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

    public static function nested($count = 0)
    {
        $query = self::whereNull('parent_id')
            ->with(['childrens' => function ($category) {
                $category->with('childrens')->orderBy('order');
            }])
            ->withCount('childrens')
            ->orderBy('order');
        $count && $query->take($count);

        if ($count) {
            return $query->get();
        }

        return cache()->rememberForever('categories:nested', function () use ($query) {
            return $query->get();
        });
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
