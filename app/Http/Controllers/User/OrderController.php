<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use function auth;

class OrderController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        return view('user.orders', [
            'orders' => auth('user')->user()->orders()->latest('id')->paginate(6),
        ]);
    }
}
