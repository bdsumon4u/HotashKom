<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\PlaceOnindaOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ResellerOrderController extends Controller
{
    /**
     * Handle the incoming request to place an order from reseller.
     */
    public function placeOrder(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|integer',
            'domain' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Dispatch job to place order on Oninda
        PlaceOnindaOrder::dispatch($request->order_id, $request->domain);

        return response()->json([
            'message' => 'Order placement initiated successfully',
            'order_id' => $request->order_id,
        ]);
    }
}
