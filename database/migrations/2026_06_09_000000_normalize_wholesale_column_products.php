<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Normalize wholesale column for products to canonical map {qty: price}
        DB::table('products')->orderBy('id')->chunkById(100, function ($products) {
            foreach ($products as $product) {
                $raw = $product->wholesale;

                if (is_null($raw) || $raw === '' ) {
                    continue;
                }

                // If already stored as JSON string in DB, decode
                $decoded = json_decode((string) $raw, true);

                // If decode failed but string looks like a JSON-like map with numeric keys, try to evaluate
                if (is_null($decoded)) {
                    continue;
                }

                $normalized = [];

                // Case A: {'quantity': [...], 'price': [...]} -> convert
                if (isset($decoded['quantity']) && isset($decoded['price']) && is_array($decoded['quantity']) && is_array($decoded['price'])) {
                    foreach ($decoded['quantity'] as $i => $q) {
                        if (!isset($decoded['price'][$i])) continue;
                        $qty = is_numeric($q) ? (int) $q : $q;
                        $normalized[$qty] = (string) $decoded['price'][$i];
                    }
                }

                // Case B: already map of qty => price
                if (empty($normalized) && is_array($decoded)) {
                    // ensure all keys/values are scalar
                    $allScalar = true;
                    foreach ($decoded as $k => $v) {
                        if (!is_scalar($k) || (!is_scalar($v) && !is_null($v))) { $allScalar = false; break; }
                    }
                    if ($allScalar) {
                        foreach ($decoded as $k => $v) {
                            $qty = is_numeric($k) ? (int) $k : $k;
                            $normalized[$qty] = (string) $v;
                        }
                    }
                }

                if (!empty($normalized)) {
                    ksort($normalized);
                    DB::table('products')->where('id', $product->id)->update(['wholesale' => json_encode($normalized)]);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op (data normalization migration is irreversible safely)
    }
};
