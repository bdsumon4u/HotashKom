<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Yajra\DataTables\Facades\DataTables;

class TransactionController extends Controller
{
    /**
     * Display a listing of the transactions.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = auth('user')->user()->wallet->transactions();

            // Shared cache: order_id → Order. Each unique order loaded once, reused across all columns.
            $ordersCache = [];
            $loadOrder = function (int $orderId) use (&$ordersCache): ?Order {
                if (! array_key_exists($orderId, $ordersCache)) {
                    $ordersCache[$orderId] = Order::find($orderId);
                }

                return $ordersCache[$orderId];
            };

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('type', fn ($row): string => $row->type === 'deposit' ?
                    '<span class="badge badge-success">Deposit</span>' :
                    '<span class="badge badge-danger">Withdraw</span>')
                ->editColumn('amount', fn ($row): string => number_format($row->amount, 2))
                ->editColumn('created_at', fn ($row) => $row->created_at->format('d M Y, h:i A'))
                ->addColumn('meta', function ($row) {
                    $meta = $row->meta;

                    if (isset($meta['trx_id']) && isset($meta['admin_id'])) {
                        return '<span class="text-muted">Trx ID: '.$meta['trx_id'].' by staff #'.$meta['admin_id'].'</span>';
                    }

                    $title = $row->meta['reason'] ?? 'N/A';
                    if ($id = $meta['order_id'] ?? false) {
                        return '<a target="_blank" href="'.route('track-order', ['order' => $id]).'">'.$title.'</a>';
                    }

                    return $title;
                })
                ->addColumn('subtotal', function ($row) use ($loadOrder) {
                    $orderId = $row->meta['order_id'] ?? null;
                    if (! $orderId) {
                        return '-';
                    }
                    $order = $loadOrder((int) $orderId);
                    if (! $order) {
                        return '-';
                    }
                    $buy = number_format((int) ($order->data['subtotal'] ?? 0));
                    $sell = number_format((int) collect((array) $order->products)->sum(
                        fn ($p) => (float) ($p->retail_price ?? $p->price ?? 0) * (int) ($p->quantity ?? 0)
                    ));

                    return '<small class="text-muted">Buy</small> '.$buy.'<br><small class="text-muted">Sell</small> '.$sell;
                })
                ->addColumn('delivery_charge', function ($row) use ($loadOrder) {
                    $orderId = $row->meta['order_id'] ?? null;
                    if (! $orderId) {
                        return '-';
                    }
                    $order = $loadOrder((int) $orderId);
                    if (! $order) {
                        return '-';
                    }
                    $buy = number_format((int) ($order->data['shipping_cost'] ?? 0));
                    $sell = number_format((int) ($order->data['retail_delivery_fee'] ?? $order->data['shipping_cost'] ?? 0));

                    return '<small class="text-muted">Buy</small> '.$buy.'<br><small class="text-muted">Sell</small> '.$sell;
                })
                ->addColumn('advanced', function ($row) use ($loadOrder) {
                    $orderId = $row->meta['order_id'] ?? null;
                    if (! $orderId) {
                        return '-';
                    }
                    $order = $loadOrder((int) $orderId);

                    return $order ? number_format((int) ($order->data['advanced'] ?? 0)) : '-';
                })
                ->addColumn('packaging_charge', function ($row) use ($loadOrder) {
                    $orderId = $row->meta['order_id'] ?? null;
                    if (! $orderId) {
                        return '-';
                    }
                    $order = $loadOrder((int) $orderId);

                    return $order ? number_format((int) ($order->data['packaging_charge'] ?? 0)) : '-';
                })
                ->addColumn('status', function ($row) {
                    if ($row->confirmed) {
                        return '<span class="badge badge-success">Confirmed</span>';
                    } else {
                        return '<span class="badge badge-warning">Pending</span>';
                    }
                })
                ->rawColumns(['type', 'meta', 'status', 'subtotal', 'delivery_charge'])
                ->make(true);
        }

        return view('user.transactions');
    }

    /**
     * Request a withdrawal.
     *
     * @return Response
     */
    public function withdrawRequest(Request $request)
    {
        $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
        ]);

        $user = auth('user')->user();
        $availableBalance = $user->getAvailableBalance();

        if ($request->amount > $availableBalance) {
            $pendingAmount = $user->getPendingWithdrawalAmount();
            $message = 'Insufficient available balance. ';
            if ($pendingAmount > 0) {
                $message .= "You have {$pendingAmount} tk in pending withdrawals.";
            }

            return response()->json(['message' => $message], 422);
        }

        // Create withdraw request with pending status
        $user->wallet->withdraw($request->amount, [
            'reason' => 'Withdraw Request',
            'status' => 'pending',
        ], false); // false for not confirmed

        return response()->json(['message' => 'Withdrawal request submitted successfully']);
    }
}
