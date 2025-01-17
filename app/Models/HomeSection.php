<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeSection extends Model
{
    protected $fillable = [
        'title', 'type', 'items', 'order', 'data',
    ];

    protected $with = ['categories'];

    protected $casts = [
        'items' => 'array',
        'data' => 'object',
    ];

    public static function booted(): void
    {
        static::deleted(function (): void {
            cache()->forget('homesections');
        });
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function products($paginate = 0, $category = null)
    {
        $ids = $this->items ?? [];
        $rows = $this->data->rows ?? 3;
        $cols = $this->data->cols ?? 5;
        $sorted = setting('show_option')->product_sort ?? 'random';
        if ($this->type == 'carousel-grid') {
            $rows *= $cols;
        }
        $query = Product::whereIsActive(1)->whereNull('parent_id');
        if ($category) {
            $query->whereHas('categories', function ($query) use ($category): void {
                $query->where('categories.id', $category);
            });
        } else if (($this->data->source ?? false) == 'specific') {
            $query->whereHas('categories', function ($query): void {
                $query->whereIn('categories.id', $this->categories->pluck('id')->toArray());
            })
                ->orWhereIn('id', $ids);
        }
        $query
            // ->inRandomOrder()
            ->when(! $paginate, function ($query) use ($rows, $cols): void {
                $query->take($rows * $cols);
            });
        if ($ids) {
            if ($sorted == 'random') {
                $query->orderByRaw('CASE WHEN id IN ('.implode(',', $ids).') THEN 0 ELSE RAND()*(10-1)+1 END');
            } elseif ($sorted == 'updated_at') {
                $query->orderByRaw('CASE WHEN id IN ('.implode(',', $ids).') THEN 2038 ELSE updated_at END DESC');
            } elseif ($sorted == 'selling_price') {
                $query->orderByRaw('CASE WHEN id IN ('.implode(',', $ids).') THEN 0 ELSE selling_price END');
            }
        } else {
            if ($sorted == 'random') {
                $query->inRandomOrder();
            } elseif ($sorted == 'updated_at') {
                $query->latest('updated_at');
            } elseif ($sorted == 'selling_price') {
                $query->orderBy('selling_price');
            }
        }

        return $paginate
            ? $query->paginate($paginate)
            : $query->get();
    }
}
