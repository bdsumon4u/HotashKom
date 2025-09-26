<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Product;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class FeedController extends Controller
{
    public function catalog(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="catalog_products.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');

            // Add CSV column headers
            fputcsv($file, [
                'id', 'title', 'description', 'availability', 'condition', 'price', 'link', 'image_link', 'brand',
                'google_product_category', 'fb_product_category', 'quantity_to_sell_on_facebook', 'sale_price',
                'sale_price_effective_date', 'item_group_id', 'gender', 'color', 'size', 'age_group', 'material',
                'pattern', 'shipping', 'shipping_weight', 'gtin', 'video[0].url', 'video[0].tag[0]',
                'product_tags[0]', 'product_tags[1]', 'style[0]',
            ]);

            try {
                // Process products in chunks for better memory management
                Product::with(['brand', 'categories', 'images', 'variations'])
                    ->where('is_active', true)
                    ->chunk(100, function ($products) use ($file) {
                        foreach ($products as $product) {
                            // If product has variants, process the variants
                            if ($product->variations->isNotEmpty()) {
                                foreach ($product->variations as $variant) {
                                    // Set the parent's brand and categories on the variant for easier access
                                    $variant->brand = $product->brand;
                                    $variant->categories = $product->categories;

                                    $this->writeProductRow($file, $variant, $product->id);
                                }
                            } else {
                                // If product has no variants, process the product itself
                                $this->writeProductRow($file, $product, $product->id);
                            }
                        }
                    });
            } catch (\Exception $e) {
                // If database is not available, just write headers
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function writeProductRow($file, $product, $itemGroupId): void
    {
        // Format title: max 200 characters, remove HTML, convert to plain text
        $title = $this->formatTitle($product->var_name);

        // Format description: max 9999 characters, remove HTML, convert to plain text, no all caps
        $description = $this->formatDescription($product->description);

        fputcsv($file, [
            $product->id,
            $title,
            $description,
            $product->in_stock ? 'in stock' : 'out of stock',
            'new',
            number_format($product->price, 2).' BDT',
            route('products.show', $product->slug),
            $product->base_image?->src ?? '',
            $product->brand?->name ?? 'Unknown',
            $product->category,
            $product->category,
            $product->stock_count ?? 1,
            number_format($product->selling_price, 2).' BDT',
            now()->addDays(30)->format('Y-m-d\TH:i\Z').'/'.now()->addDays(60)->format('Y-m-d\TH:i\Z'),
            $itemGroupId, // Use parent ID for item_group_id to group variants
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

    private function formatTitle(string $title): string
    {
        // Remove HTML tags and decode entities
        $title = strip_tags($title);
        $title = html_entity_decode($title, ENT_QUOTES, 'UTF-8');

        // Trim whitespace
        $title = trim($title);

        // Limit to 200 characters
        return mb_substr($title, 0, 200);
    }

    private function formatDescription(string $description): string
    {
        // Remove HTML tags and decode entities
        $description = strip_tags($description);
        $description = html_entity_decode($description, ENT_QUOTES, 'UTF-8');

        // Remove extra whitespace and normalize line breaks
        $description = preg_replace('/\s+/', ' ', $description);
        $description = trim($description);

        // Check if description is all caps and convert to proper case
        if (mb_strtoupper($description) === $description && mb_strlen($description) > 3) {
            $description = mb_convert_case($description, MB_CASE_TITLE, 'UTF-8');
        }

        // Limit to 9999 characters
        return mb_substr($description, 0, 9999);
    }
}
