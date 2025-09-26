<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Product;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class FeedController extends Controller
{
    public function catalog(): StreamedResponse
    {
        try {
            // Get all active products (both parent products and variants)
            $products = Product::with(['brand', 'categories', 'images', 'parent'])
                ->where('is_active', true)
                ->get()
                ->flatMap(function ($product) {
                    // If product has variants, return the variants
                    if ($product->variations->isNotEmpty()) {
                        return $product->variations->map(function ($variant) use ($product) {
                            // Set the parent's brand and categories on the variant for easier access
                            $variant->brand = $product->brand;
                            $variant->categories = $product->categories;

                            return $variant;
                        });
                    }

                    // If product has no variants, return the product itself
                    return collect([$product]);
                });
        } catch (\Exception $e) {
            // If database is not available, return empty CSV with headers
            $products = collect();
        }

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="catalog_products.csv"',
        ];

        $callback = function () use ($products) {
            $file = fopen('php://output', 'w');

            // Add CSV column headers
            fputcsv($file, [
                'id', 'title', 'description', 'availability', 'condition', 'price', 'link', 'image_link', 'brand',
                'google_product_category', 'fb_product_category', 'quantity_to_sell_on_facebook', 'sale_price',
                'sale_price_effective_date', 'item_group_id', 'gender', 'color', 'size', 'age_group', 'material',
                'pattern', 'shipping', 'shipping_weight', 'gtin', 'video[0].url', 'video[0].tag[0]',
                'product_tags[0]', 'product_tags[1]', 'style[0]',
            ]);

            // Add product data
            foreach ($products as $product) {
                fputcsv($file, [
                    $product->id,
                    $product->var_name, // Use var_name for variants
                    strip_tags($product->description),
                    $product->in_stock ? 'in stock' : 'out of stock',
                    'new',
                    number_format($product->price, 2).' BDT',
                    route('products.show', $product->slug),
                    $product->base_image?->url ?? '',
                    $product->brand?->name ?? 'Unknown',
                    $product->category,
                    $product->category,
                    $product->stock_count ?? 1,
                    number_format($product->selling_price, 2).' BDT',
                    now()->addDays(30)->format('Y-m-d\TH:i\Z').'/'.now()->addDays(60)->format('Y-m-d\TH:i\Z'),
                    $product->parent_id ?? $product->id, // Use parent ID for item_group_id to group variants
                    'unisex',
                    '',
                    '',
                    'adult',
                    '',
                    '',
                    'BD:Dhaka::Courier:'.$product->shipping_inside.' BDT;BD:Other::Courier:'.$product->shipping_outside.' BDT',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
