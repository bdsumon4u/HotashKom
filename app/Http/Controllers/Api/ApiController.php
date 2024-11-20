<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Pathao\Facade\Pathao;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function areas($city_id)
    {
        return Pathao::area()->zone($city_id)->data;
    }

    public function categories()
    {
        return response()->json(Category::all()->toArray());
    }

    public function products($search)
    {
        $products = Product::where('name', 'like', "%$search%")->take(5)->get();

        return view('admin.orders.searched', compact('products'))->render();
    }

    public function pendingCount(Admin $admin)
    {
        return Order::where('status', 'PENDING')->when($admin->role_id == Admin::SALESMAN, function ($query) use (&$admin) {
            $query->where('admin_id', $admin->id);
        })->count();
    }

    public function pathaoWebhook(Request $request)
    {
        $Pathao = setting('Pathao');
        if ($request->header('X-PATHAO-Signature') != $Pathao->store_id) {
            return;
        }

        if (! $order = Order::find($request->merchant_order_id)/*->orWhere('data->consignment_id', $request->consignment_id)->first()*/) {
            return;
        }

        // $courier = $request->only([
        //     'consignment_id',
        //     'order_status',
        //     'reason',
        //     'invoice_id',
        //     'payment_status',
        //     'collected_amount',
        // ]);
        // $order->forceFill(['courier' => ['booking' => 'Pathao'] + $courier]);

        if ($request->order_status_slug == 'Pickup_Requested') {
            $order->fill([
                'status' => 'SHIPPING',
                'data' => [
                    'consignment_id' => $request->consignment_id,
                ],
            ]);
        } elseif ($request->order_status_slug == 'Pickup_Cancelled') {
            $order->status = 'CANCELLED';
            $order->status_at = now();
        } elseif ($request->order_status_slug == 'On_Hold') {
            $order->status = 'WAITING';
            $order->status_at = now();
        } elseif ($request->order_status_slug == 'Delivered') {
            $order->status = 'COMPLETED';
            $order->status_at = now();
        } elseif ($request->order_status_slug == 'Payment_Invoice') {

        } elseif ($request->order_status_slug == 'Return') {
            $order->status = 'RETURNED';
            $order->status_at = now();
            // TODO: add to stock
        }

        $order->save();
    }
}
