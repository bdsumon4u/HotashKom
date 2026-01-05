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
        DB::table('shopping_cart')->where('updated_at', '<', now()->subDays(3))->delete();

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
