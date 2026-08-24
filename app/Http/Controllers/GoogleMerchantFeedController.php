<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class GoogleMerchantFeedController extends Controller
{
    public function __invoke(): StreamedResponse
    {
        return response()->stream(function (): void {
            $included = 0;
            $skipped = 0;

            echo '<?xml version="1.0" encoding="UTF-8"?>'."\n";
            echo '<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">'."\n";
            echo "  <channel>\n";
            echo '    <title>'.$this->xml((config('app.name') ?: 'Store').' Product Feed')."</title>\n";
            echo '    <link>'.$this->xml(url('/'))."</link>\n";
            echo '    <description>'.$this->xml('Live product data for Google Merchant Center')."</description>\n";

            Product::query()
                ->with([
                    'brand',
                    'categories',
                    'images',
                    'variations' => fn ($query) => $query
                        ->where('is_active', true)
                        ->with(['images', 'options.attribute']),
                ])
                ->where('is_active', true)
                ->whereNull('parent_id')
                ->chunkById(100, function ($products) use (&$included, &$skipped): void {
                    foreach ($products as $parent) {
                        $offers = $parent->variations->isNotEmpty()
                            ? $parent->variations
                            : collect([$parent]);

                        foreach ($offers as $offer) {
                            try {
                                $errors = $this->validateOffer($offer, $parent);

                                if ($errors !== []) {
                                    $skipped++;

                                    Log::warning('Google Merchant feed product skipped', [
                                        'product_id' => $offer->id,
                                        'parent_id' => $parent->id,
                                        'reasons' => $errors,
                                    ]);

                                    continue;
                                }

                                $this->writeItem($offer, $parent);
                                $included++;
                            } catch (\Throwable $exception) {
                                $skipped++;

                                Log::error('Google Merchant feed product failed', [
                                    'product_id' => $offer->id,
                                    'parent_id' => $parent->id,
                                    'error' => $exception->getMessage(),
                                ]);
                            }
                        }
                    }
                });

            echo "  </channel>\n";
            echo "</rss>\n";

            Log::info('Google Merchant feed generated', [
                'included_products' => $included,
                'skipped_products' => $skipped,
            ]);
        }, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'Content-Disposition' => 'inline; filename="google-merchant-feed.xml"',
            'Cache-Control' => 'public, max-age=900',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    private function validateOffer(Product $offer, Product $parent): array
    {
        $errors = [];

        if ($this->offerTitle($offer, $parent) === '') {
            $errors[] = 'missing_title';
        }

        if ($this->productDescription($parent) === '') {
            $errors[] = 'missing_description';
        }

        if ((float) ($offer->selling_price ?? 0) <= 0) {
            $errors[] = 'invalid_selling_price';
        }

        if (blank($offer->slug)) {
            $errors[] = 'missing_slug';
        }

        $image = $this->mainImage($offer, $parent);

        if ($image === '' || ! Str::startsWith($image, ['https://', 'http://'])) {
            $errors[] = 'missing_or_invalid_image';
        }

        if ($offer->parent_id && $offer->options->isEmpty()) {
            $errors[] = 'variant_missing_option';
        }

        return $errors;
    }

    private function writeItem(Product $offer, Product $parent): void
    {
        $isVariant = $offer->parent_id !== null;
        $title = $this->offerTitle($offer, $parent);
        $description = $this->productDescription($parent);
        $link = route('products.show', ['product' => $offer->slug]);
        $image = $this->mainImage($offer, $parent);

        $availability = (bool) $offer->should_track
            && (int) $offer->stock_count < 1
                ? 'out_of_stock'
                : 'in_stock';

        $price = number_format(
            (float) $offer->selling_price,
            2,
            '.',
            ''
        ).' BDT';

        $productType = $this->cleanText(
            $parent->categories
                ->pluck('name')
                ->filter()
                ->unique()
                ->implode(' > '),
            750
        );

        $googleProductCategory = $this->googleProductCategory(
            $parent,
            $productType
        );

        $brand = $this->validBrand($parent->brand?->name);

        $prefix = (string) setting('google_merchant_id_prefix', 'hk-');

        echo "    <item>\n";
        echo '      <g:id>'.$this->xml($prefix.$offer->id)."</g:id>\n";
        echo '      <g:title>'.$this->xml($title)."</g:title>\n";
        echo '      <g:description>'.$this->xml($description)."</g:description>\n";
        echo '      <g:link>'.$this->xml($link)."</g:link>\n";
        echo '      <g:image_link>'.$this->xml($image)."</g:image_link>\n";

        foreach ($this->additionalImages($offer, $parent, $image) as $additionalImage) {
            echo '      <g:additional_image_link>'
                .$this->xml($additionalImage)
                ."</g:additional_image_link>\n";
        }

        echo '      <g:availability>'.$availability."</g:availability>\n";
        echo "      <g:condition>new</g:condition>\n";
        echo '      <g:price>'.$this->xml($price)."</g:price>\n";

        if ($productType !== '') {
            echo '      <g:product_type>'
                .$this->xml($productType)
                ."</g:product_type>\n";
        }

        echo '      <g:google_product_category>'
            .$this->xml($googleProductCategory)
            ."</g:google_product_category>\n";

        if ($brand !== null) {
            echo '      <g:brand>'.$this->xml($brand)."</g:brand>\n";
        } else {
            echo "      <g:identifier_exists>no</g:identifier_exists>\n";
        }

        if ($isVariant) {
            echo '      <g:item_group_id>'
                .$this->xml($prefix.$parent->id)
                ."</g:item_group_id>\n";

            echo '      <g:item_group_title>'
                .$this->xml($this->cleanText((string) $parent->name, 150))
                ."</g:item_group_title>\n";

            $this->writeVariantOptions($offer);
        }

        echo "    </item>\n";
    }

    private function googleProductCategory(
        Product $product,
        string $productType
    ): string {
        /*
         * Specific product matching uses product name/slug only.
         * Category matching is handled separately below.
         */
        $nameText = Str::lower(trim(
            (string) $product->name.' '.
            (string) $product->slug
        ));

        $categoryText = Str::lower($productType);

        /*
         * Google Product Taxonomy IDs
         * More-specific matches come first.
         */
        $rules = [

            // Baby
            [['potty', 'baby toilet', 'toilet trainer'], '552'],
            [['swaddle', 'swaddling blanket'], '543665'],
            [['baby blanket', 'receiving blanket', 'baby sleeping bag'], '6899'],

            // Remote-control toys
            [['remote control tank', 'rc tank'], '5969'],
            [['remote control car', 'rc car', 'remote car'], '3601'],
            [['remote control helicopter', 'rc helicopter'], '3554'],
            [['remote control plane', 'rc plane'], '3677'],
            [['remote control boat', 'rc boat'], '3532'],
            [['remote control robot', 'rc robot'], '6059'],

            // Drawing toys
            [['drawing tablet'], '3079'],
            [['projection drawing', 'drawing board', 'doodle board'], '3731'],

            // Kitchen
            [['oil dispenser', 'oil sprayer', 'vinegar dispenser'], '6526'],
            [['vegetable cutter', 'kitchen slicer'], '3206'],
            [['manual chopper', 'vegetable chopper'], '668'],
            [['pitha maker', 'kitchen mold', 'kitchen mould'], '8006'],

            // Lighting
            [['solar wall light', 'wall light'], '6073'],
            [['torch light', 'flashlight'], '543689'],
            [['solar lamp', 'solar light'], '594'],

            // Islamic
            [['tasbeeh', 'tasbih', 'prayer bead'], '3923'],

            // Health / personal care
            [['toothpaste dispenser'], '5154'],
            [['hair trimmer', 'hair clipper'], '533'],
            [['epilator'], '4510'],
            [['hair remover', 'hair removal'], '4507'],
            [['anti snoring', 'anti-snoring', 'nose clip'], '6017'],
            [['massager', 'massage gun'], '471'],
            [['hot water bag'], '516'],

            // Electronics / content tools
            [['microphone'], '234'],
            [['speaker'], '249'],
            [['mobile phone stand', 'phone stand'], '5566'],
            [['mobile accessory', 'mobile accessories'], '264'],
            [['mini fan', 'portable fan', 'table fan'], '608'],

            // Camera
            [['tripod', 'tripod stand'], '150'],
            [['ip camera', 'wifi camera', 'wireless camera', 'spy camera'], '362'],

            // Fashion / clocks
            [['sunglasses'], '178'],
            [['wall clock', 'alarm clock', 'clock'], '3890'],
            [['smart watch', 'smartwatch', 'wrist watch', 'watch'], '201'],

            // Home
            [['wall sticker', 'foil sticker', 'kitchen foil sticker'], '2334'],

            // Garden
            [['garden sprayer', 'pressure water sprayer'], '1967'],
            [['garden tool', 'gardening tool'], '3173'],
        ];

        foreach ($rules as [$keywords, $category]) {
            if (Str::contains($nameText, $keywords)) {
                return $category;
            }
        }

        /*
         * Category taxonomy fallbacks
         */

        if (Str::contains($categoryText, ['baby care'])) {
            return '537';
        }

        if (Str::contains($categoryText, ['kids toy', 'kids zone'])) {
            return '1253';
        }

        if (Str::contains($categoryText, ['islamic corner'])) {
            return '97';
        }

        if (Str::contains($categoryText, ['kitchen accessories'])) {
            return '638';
        }

        if (Str::contains($categoryText, ['garden accessories'])) {
            return '689';
        }

        if (Str::contains($categoryText, ['fan item'])) {
            return '608';
        }

        if (Str::contains($categoryText, ['home appliance'])) {
            return '604';
        }

        if (Str::contains($categoryText, ['solar lamp'])) {
            return '594';
        }

        if (Str::contains($categoryText, ['torch light'])) {
            return '543689';
        }

        if (Str::contains($categoryText, ['mobile accessories'])) {
            return '264';
        }

        if (Str::contains($categoryText, ['speaker'])) {
            return '249';
        }

        if (Str::contains($categoryText, ['computer item'])) {
            return '278';
        }

        if (Str::contains(
            $categoryText,
            ['gadget & electronics', 'gadget and electronics']
        )) {
            return '222';
        }

        if (Str::contains($categoryText, ['microphone'])) {
            return '234';
        }

        if (Str::contains(
            $categoryText,
            ['tripod & stand', 'tripod and stand']
        )) {
            return '150';
        }

        if (Str::contains($categoryText, ['content tools'])) {
            return '222';
        }

        if (Str::contains($categoryText, ['smart watch', 'watches'])) {
            return '201';
        }

        if (Str::contains($categoryText, ['clocks'])) {
            return '3890';
        }

        if (Str::contains(
            $categoryText,
            ['shaver & trimmer', 'shaver and trimmer']
        )) {
            return '528';
        }

        if (Str::contains(
            $categoryText,
            ['manicure and pedicure', 'manicure']
        )) {
            return '6300';
        }

        if (Str::contains(
            $categoryText,
            ['health & beauty', 'health and beauty']
        )) {
            return '2915';
        }

        if (Str::contains(
            $categoryText,
            ['sports & gym', 'sports and gym']
        )) {
            return '988';
        }

        if (Str::contains($categoryText, ['camera'])) {
            return '142';
        }

        if (Str::contains($categoryText, ['foods', 'food'])) {
            return '422';
        }

        if (Str::contains($categoryText, ['fashion'])) {
            return '166';
        }

        if (Str::contains($categoryText, ['sunglasses'])) {
            return '178';
        }

        if (Str::contains($categoryText, ['rain item'])) {
            return '166';
        }

        if (Str::contains(
            $categoryText,
            ['home & lifestyle', 'home and lifestyle']
        )) {
            return '536';
        }

        /*
         * Final fallback:
         * Ensures google_product_category is never missing.
         */
        return '536';
    }

    private function writeVariantOptions(Product $offer): void
    {
        foreach ($offer->options as $option) {
            $name = $this->cleanText(
                (string) ($option->attribute?->name ?? 'Option'),
                70
            );

            $value = $this->cleanText((string) $option->name, 70);
            $standardAttribute = $this->standardVariantAttribute($name);

            if ($standardAttribute !== null) {
                echo '      <g:'.$standardAttribute.'>'
                    .$this->xml($value)
                    .'</g:'.$standardAttribute.">\n";
            }

            echo "      <g:variant_option>\n";
            echo '        <g:name>'
                .$this->xml(Str::lower($name))
                ."</g:name>\n";
            echo '        <g:value>'
                .$this->xml($value)
                ."</g:value>\n";
            echo "      </g:variant_option>\n";
        }
    }

    private function standardVariantAttribute(string $name): ?string
    {
        return match (Str::lower(trim($name))) {
            'color', 'colour' => 'color',
            'size' => 'size',
            'material' => 'material',
            'pattern' => 'pattern',
            'gender' => 'gender',
            'age group', 'age_group' => 'age_group',
            default => null,
        };
    }

    private function offerTitle(Product $offer, Product $parent): string
    {
        $title = (string) $parent->name;

        if ($offer->parent_id) {
            $options = $offer->options
                ->pluck('name')
                ->filter()
                ->implode(' - ');

            if ($options !== '') {
                $title .= ' - '.$options;
            }
        }

        return $this->cleanText($title, 150);
    }

    private function productDescription(Product $parent): string
    {
        $description = (string) (
            $parent->short_description
            ?: $parent->description
            ?: ''
        );

        return $this->cleanText($description, 5000);
    }

    private function mainImage(Product $offer, Product $parent): string
    {
        $source = $offer->base_image?->src
            ?: $parent->base_image?->src;

        return $this->imageUrl($source);
    }

    private function additionalImages(
        Product $offer,
        Product $parent,
        string $mainImage
    ): array {
        return $offer->images
            ->concat($parent->images)
            ->map(fn ($image): string => $this->imageUrl($image->src ?? null))
            ->filter(fn (string $url): bool => $url !== '' && $url !== $mainImage)
            ->unique()
            ->take(10)
            ->values()
            ->all();
    }

    private function imageUrl(?string $url): string
    {
        $url = trim((string) $url);

        if ($url === '') {
            return '';
        }

        if (Str::startsWith($url, ['https://', 'http://'])) {
            return $url;
        }

        return asset(ltrim($url, '/'));
    }

    private function validBrand(?string $brand): ?string
    {
        $brand = $this->cleanText((string) $brand, 70);

        if ($brand === '') {
            return null;
        }

        $placeholders = [
            'china',
            'unknown',
            'generic',
            'no brand',
            'n/a',
            'none',
        ];

        return in_array(Str::lower($brand), $placeholders, true)
            ? null
            : $brand;
    }

    private function cleanText(string $value, int $limit): string
    {
        $value = strip_tags($value);

        $value = html_entity_decode(
            $value,
            ENT_QUOTES | ENT_HTML5,
            'UTF-8'
        );

        $value = preg_replace(
            '/[\x{0000}-\x{0008}\x{000B}\x{000C}\x{000E}-\x{001F}\x{007F}]/u',
            '',
            $value
        ) ?? '';

        $value = preg_replace('/\s+/u', ' ', $value) ?? '';

        return mb_substr(trim($value), 0, $limit);
    }

    private function xml(string $value): string
    {
        return htmlspecialchars(
            $value,
            ENT_XML1 | ENT_QUOTES,
            'UTF-8'
        );
    }
}
