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
        info('placeOrder', ['request' => $request->all()]);
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|array',
            'order_id.*' => 'required|integer',
            'domain' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Dispatch job to place order on Oninda
        info('dispatching job', ['request' => $request->all()]);
        foreach ($request->order_id as $orderId) {
            PlaceOnindaOrder::dispatch($orderId, $request->domain);
        }

        return response()->json([
            'message' => 'Order placement initiated successfully',
            'order_id' => $request->order_id,
        ]);
    }
}
