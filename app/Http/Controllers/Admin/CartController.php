<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function index(Request $request)
    {
        abort_if($request->user()->is('uploader'), 403);
        if (! ($last = cache('last_cart_cleanup_at')) || $last->addHour()->isPast()) {
            $last ??= now();
            $carts = DB::table('shopping_cart')->where('updated_at', '>=', $last)->get()->keyBy('phone');
            Order::query()->whereIn('phone', $carts->keys())->where('created_at', '>=', (clone $last)->subWeek())->get()->groupBy('phone')->each(function ($orders, $phone) use (&$carts): void {
                $productIDs = DB::table('products')
                    ->whereIn('id', $orders->flatMap(fn ($order) => array_keys((array) $order->products))->unique())
                    ->selectRaw('CASE WHEN parent_id IS NOT NULL THEN parent_id ELSE id END as selected_id')
                    ->pluck('selected_id', 'selected_id');

                $content = unserialize($carts[$phone]->content)->diffKeys($productIDs);
                if ($content->isEmpty()) {
                    DB::table('shopping_cart')->where('phone', $phone)->delete();
                } else {
                    DB::table('shopping_cart')->where('phone', $phone)->update([
                        'content' => serialize($content),
                    ]);
                }
            });
        }
        cache()->memo()->rememberForever('last_cart_cleanup_at', fn (): \Carbon\CarbonInterface => now());

        return view('admin.carts.index', [
            'carts' => DB::table('shopping_cart')
                ->oldest('updated_at')
                ->get(),
        ]);
    }

    public function destroy(string $identifier)
    {
        DB::table('shopping_cart')->where('identifier', $identifier)->delete();

        return back()->with('success', 'Cart Has Been Deleted.');
    }
}
