<?php

return [
    /**
     * Map local category IDs to official Google taxonomy IDs or full paths.
     * Example: 12 => 'Apparel & Accessories > Clothing > Shirts'.
     *
     * @var array<int, int|string>
     */
    'category_mapping' => [],

    /**
     * Manufacturer-assigned identifiers keyed by product or variation ID.
     * Example: 123 => ['gtin' => '00012345678905', 'mpn' => 'ABC-123'].
     * Keep GTINs as strings to preserve leading zeroes. A store SKU is not an MPN.
     *
     * @var array<int, array{gtin?: string, mpn?: string}>
     */
    'product_identifiers' => [],
];
