<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class FeedController extends Controller
{
    public function catalog(): StreamedResponse
    {
        // Log the request for debugging
        Log::info('Feed catalog requested', [
            'url' => request()->url(),
            'user_agent' => request()->userAgent(),
            'ip' => request()->ip(),
        ]);

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="catalog_products.csv"',
            'Cache-Control' => 'no-cache, must-revalidate',
            'Pragma' => 'no-cache',
        ];

        $callback = function () {
            try {
                $file = fopen('php://output', 'w');

                // Add BOM for UTF-8 to ensure proper encoding
                fwrite($file, "\xEF\xBB\xBF");

                // Add CSV column headers
                fputcsv($file, [
                    'id', 'title', 'description', 'availability', 'condition', 'price', 'link', 'image_link', 'brand',
                    'google_product_category', 'fb_product_category', 'quantity_to_sell_on_facebook', 'sale_price',
                    'sale_price_effective_date', 'item_group_id', 'gender', 'color', 'size', 'age_group', 'material',
                    'pattern', 'shipping', 'shipping_weight', 'gtin', 'video[0].url', 'video[0].tag[0]',
                    'product_tags[0]', 'product_tags[1]', 'style[0]',
                ]);

                // Process products in chunks for better memory management
                Product::with(['brand', 'categories', 'images', 'variations'])
                    ->where('is_active', true)
                    ->whereNull('parent_id') // Only parents; variants handled via parent loop
                    ->chunk(100, function ($products) use ($file) {
                        foreach ($products as $product) {
                            try {
                                $this->writeProductRow($file, $product, $product->id);
                                // If product has variants, process the variants
                                if ($product->variations->isNotEmpty()) {
                                    foreach ($product->variations as $variant) {
                                        // Set the parent's brand and categories on the variant for easier access
                                        $variant->brand = $product->brand;
                                        $variant->categories = $product->categories;

                                        $this->writeProductRow($file, $variant, $product->id);
                                    }
                                }
                            } catch (\Exception $e) {
                                Log::error('Error processing product in feed', [
                                    'product_id' => $product->id,
                                    'error' => $e->getMessage(),
                                    'trace' => $e->getTraceAsString(),
                                ]);
                                // Continue processing other products
                            }
                        }
                    });

                fclose($file);

                Log::info('Feed catalog generated successfully');

            } catch (\Exception $e) {
                Log::error('Error generating feed catalog', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);

                // Return error response instead of empty feed
                if (isset($file)) {
                    fclose($file);
                }

                // Output error as CSV
                $file = fopen('php://output', 'w');
                fputcsv($file, ['Error', 'Failed to generate catalog feed']);
                fclose($file);
            }
        };

        return response()->stream($callback, 200, $headers);
    }

    public function catalogSimple()
    {
        try {
            Log::info('Simple feed catalog requested');

            $products = Product::with(['brand', 'categories', 'images', 'variations'])
                ->where('is_active', true)
                ->whereNull('parent_id') // Only parents; variants handled via mapping
                ->get()
                ->flatMap(function ($product) {
                    if ($product->variations->isNotEmpty()) {
                        return $product->variations->map(function ($variant) use ($product) {
                            $variant->brand = $product->brand;
                            $variant->categories = $product->categories;

                            return $variant;
                        })->prepend($product);
                    }

                    return collect([$product]);
                });

            $csv = $this->generateCsvContent($products);

            return response($csv, 200, [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="catalog_products.csv"',
                'Cache-Control' => 'no-cache, must-revalidate',
                'Pragma' => 'no-cache',
            ]);

        } catch (\Exception $e) {
            Log::error('Error generating simple feed catalog', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response('Error generating catalog feed', 500);
        }
    }

    private function generateCsvContent($products): string
    {
        $output = fopen('php://temp', 'r+');

        // Add BOM for UTF-8
        fwrite($output, "\xEF\xBB\xBF");

        // Add headers
        fputcsv($output, [
            'id', 'title', 'description', 'availability', 'condition', 'price', 'link', 'image_link', 'brand',
            'google_product_category', 'fb_product_category', 'quantity_to_sell_on_facebook', 'sale_price',
            'sale_price_effective_date', 'item_group_id', 'gender', 'color', 'size', 'age_group', 'material',
            'pattern', 'shipping', 'shipping_weight', 'gtin', 'video[0].url', 'video[0].tag[0]',
            'product_tags[0]', 'product_tags[1]', 'style[0]',
        ]);

        foreach ($products as $product) {
            $this->writeProductRow($output, $product, $product->parent_id ?? $product->id);
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }

    private function writeProductRow($file, $product, $itemGroupId): void
    {
        // Format title: max 200 characters, remove HTML, convert to plain text
        $title = $this->formatTitle($product->var_name);

        // Format description: max 9999 characters, remove HTML, convert to plain text, no all caps
        $description = $this->formatDescription($product->description);

        // Safely format prices - ensure they are numeric
        $price = $this->formatPrice($product->price);
        $sellingPrice = $this->formatPrice($product->selling_price);
        $shippingInside = $this->formatPrice($product->shipping_inside);
        $shippingOutside = $this->formatPrice($product->shipping_outside);

        fputcsv($file, [
            // Use products.id as the content ID (unique per product row)
            $product->id,
            $title,
            $description,
            $product->in_stock ? 'in stock' : 'out of stock',
            'new',
            $price.' BDT',
            route('products.show', $product->slug),
            $product->base_image?->src ?? '',
            $product->brand?->name ?? 'Unknown',
            $product->category,
            $product->category,
            $product->stock_count ?? 1,
            $sellingPrice.' BDT',
            now()->addDays(30)->format('Y-m-d\TH:i\Z').'/'.now()->addDays(60)->format('Y-m-d\TH:i\Z'),
            // Keep group stable across variants. Use provided parent identifier.
            $itemGroupId,
            'unisex',
            '',
            '',
            'adult',
            '',
            '',
            'BD:Dhaka::Courier:'.$shippingInside.' BDT;BD:Other::Courier:'.$shippingOutside.' BDT',
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

    private function formatPrice($price): string
    {
        // Handle null or empty values
        if ($price === null || $price === '') {
            return '0.00';
        }

        // Convert to string if it's not already
        $price = (string) $price;

        // Remove any non-numeric characters except decimal point
        $price = preg_replace('/[^0-9.]/', '', $price);

        // Handle empty string after cleaning
        if ($price === '' || $price === '.') {
            return '0.00';
        }

        // Convert to float and format
        $numericPrice = (float) $price;

        // Ensure it's not negative
        if ($numericPrice < 0) {
            $numericPrice = 0;
        }

        return number_format($numericPrice, 2);
    }
}
