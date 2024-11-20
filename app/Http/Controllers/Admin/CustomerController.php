<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        return view('admin.customers.index', [
            'customers' => User::whereHas('orders')->withCount('orders')->orderBy('orders_count', 'desc')->paginate(20),
        ]);
    }
}
