<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = Arr::except(parent::toArray($request), [
            'created_at', 'updated_at', 'brand_id',
        ]);

        if (! $request->route()->named('product')) {
            $data = Arr::except($data, ['description', 'wholesale']);
        }

        return array_merge($data, [
            'images' => $this->resource->images->pluck('src')->toArray(),
            'price' => $this->resource->selling_price,
            'compareAtPrice' => $this->resource->price,
            'badges' => [],
            'brand' => [],
            'categories' => [],
            'reviews' => 0,
            'rating' => 0,
            'attributes' => [],
            'availability' => $this->resource->should_track ? $this->resource->stock_count : 'In Stock',
        ]);
    }

    /**
     * Transform the product into a cart/order item array.
     *
     * @param int $quantity The quantity of the product
     * @return array<string, mixed>
     */
    public function toCartItem(int $quantity = 1): array
    {
        return [
            'id' => $this->resource->id,
            'parent_id' => $this->resource->parent_id ?? $this->resource->id,
            'name' => $this->resource->var_name,
            'slug' => $this->resource->slug,
            'image' => optional($this->resource->base_image)->path,
            'category' => $this->resource->category,
            'quantity' => $quantity,
            'price' => $price = $this->resource->getPrice($quantity),
            'total' => $price * $quantity,
        ];
    }
}
