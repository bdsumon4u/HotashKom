<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryMenu extends Model
{
    protected $guarded = ['id'];

    protected $with = ['category'];

    public static function booted()
    {
        static::saved(function ($menu) {
            cache()->forget('catmenu:nested');
            cache()->forget('catmenu:nestedwithparent');
        });

        static::deleting(function ($menu) {
            cache()->forget('catmenu:nested');
            cache()->forget('catmenu:nestedwithparent');
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function childrens()
    {
        return $this->hasMany(static::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(static::class, 'parent_id');
    }

    public static function nested($count = 0)
    {
        $query = self::whereNull('parent_id')
            ->with(['childrens' => function ($category) {
                $category->with('childrens');
            }])
            ->orderBy('order');
        $count && $query->take($count);

        return cache()->rememberForever('catmenu:nested', function () use ($query) {
            return $query->get();
        });
    }

    public static function nestedWithParent($count = 0)
    {
        $query = self::whereNull('parent_id')
            ->with(['childrens' => function ($category) {
                $category->with('parent', 'childrens');
            }])
            ->orderBy('order');
        $count && $query->take($count);

        return cache()->rememberForever('catmenu:nestedwithparent', function () use ($query) {
            return $query->get();
        });
    }
}
