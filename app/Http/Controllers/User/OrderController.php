<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use function auth;

class OrderController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return Response
     */
    public function __invoke(Request $request)
    {
        return view('user.orders', [
            'orders' => auth('user')->user()->orders()->latest('id')->paginate(6),
        ]);
    }
}
