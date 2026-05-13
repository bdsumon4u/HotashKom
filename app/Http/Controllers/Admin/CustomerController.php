<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CustomerController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return Response
     */
    public function __invoke(Request $request)
    {
        return view('admin.customers.index', [
            'customers' => User::whereHas('orders')->withCount('orders')->orderBy('orders_count', 'desc')->paginate(20),
        ]);
    }
}
