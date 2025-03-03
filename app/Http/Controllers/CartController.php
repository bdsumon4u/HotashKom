<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function index(Request $request)
    {
        return view('admin.carts.index', [
            'carts' => DB::table('shopping_cart')
                ->orderBy('updated_at')
                ->get(),
        ]);
    }

    public function destroy(string $identifier)
    {
        DB::table('shopping_cart')->where('identifier', $identifier)->delete();

        return back()->with('success', 'Cart Has Been Deleted.');
    }
}
