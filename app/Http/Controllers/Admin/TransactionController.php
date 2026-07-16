<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
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
    public function index(Request $request, User $user)
    {
        if ($request->ajax()) {
            $query = $user->wallet->transactions();

            $dataTable = DataTables::of($query);

            // Force prepare and fetch only the paginated results for this request
            $transactions = $dataTable->prepareQuery()->results();

            // Pre-load orders for the current page transactions in 1 query
            $orderIds = [];
            foreach ($transactions as $transaction) {
                $orderId = $transaction->meta['order_id'] ?? null;
                if ($orderId) {
                    $orderIds[] = (int) $orderId;
                }
            }
            $orders = Order::whereIn('id', array_unique($orderIds))->get()->keyBy('id');

            $loadOrder = function (int $orderId) use ($orders): ?Order {
                return $orders->get($orderId);
            };

            return $dataTable
                ->addIndexColumn()
                ->editColumn('type', fn ($row): string => $row->type === 'deposit' ?
                    '<span class="badge badge-success">Deposit</span>' :
                    '<span class="badge badge-danger">Withdraw</span>')
                ->editColumn('amount', fn ($row): string => number_format($row->amount, 2))
                ->editColumn('created_at', fn ($row) => $row->created_at->format('d M Y, h:i A'))
                ->addColumn('status', function ($row) {
                    if ($row->confirmed) {
                        return '<span class="badge badge-success">Confirmed</span>';
                    } else {
                        return '<span class="badge badge-warning">Pending</span>';
                    }
                })
                ->addColumn('meta', function ($row) {
                    $meta = $row->meta;

                    if (isset($meta['trx_id']) && isset($meta['admin_id'])) {
                        return '<span class="text-muted">Trx ID: '.$meta['trx_id'].' by staff #'.$meta['admin_id'].'</span>';
                    }

                    $title = $row->meta['reason'] ?? 'N/A';
                    if ($id = $meta['order_id'] ?? false) {
                        return '<a target="_blank" href="'.route('admin.orders.edit', $id).'">'.$title.'</a>';
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

                    return '<span style="white-space:nowrap"><small class="text-muted">Buy</small> '.$buy.'</span><br><span style="white-space:nowrap"><small class="text-muted">Sell</small> '.$sell.'</span>';
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

                    return '<span style="white-space:nowrap"><small class="text-muted">Buy</small> '.$buy.'</span><br><span style="white-space:nowrap"><small class="text-muted">Sell</small> '.$sell.'</span>';
                })
                ->addColumn('advanced', function ($row) use ($loadOrder) {
                    $orderId = $row->meta['order_id'] ?? null;
                    if (! $orderId) {
                        return '-';
                    }
                    $order = $loadOrder((int) $orderId);
                    if (! $order) {
                        return '-';
                    }
                    $amount = number_format((int) ($order->data['advanced'] ?? 0));

                    return '-<br>'.$amount;
                })
                ->addColumn('packaging_charge', function ($row) use ($loadOrder) {
                    $orderId = $row->meta['order_id'] ?? null;
                    if (! $orderId) {
                        return '-';
                    }
                    $order = $loadOrder((int) $orderId);
                    if (! $order) {
                        return '-';
                    }
                    $amount = number_format((int) ($order->data['packaging_charge'] ?? 0));

                    return $amount.'<br>-';
                })
                ->addColumn('total', function ($row) use ($loadOrder) {
                    $orderId = $row->meta['order_id'] ?? null;
                    if (! $orderId) {
                        return '-';
                    }
                    $order = $loadOrder((int) $orderId);
                    if (! $order) {
                        return '-';
                    }
                    $buySubtotal = (int) ($order->data['subtotal'] ?? 0);
                    $buyDelivery = (int) ($order->data['shipping_cost'] ?? 0);
                    $packaging = (int) ($order->data['packaging_charge'] ?? 0);
                    $sellSubtotal = (int) collect((array) $order->products)->sum(
                        fn ($p) => (float) ($p->retail_price ?? $p->price ?? 0) * (int) ($p->quantity ?? 0)
                    );
                    $sellDelivery = (int) ($order->data['retail_delivery_fee'] ?? $order->data['shipping_cost'] ?? 0);
                    $advanced = (int) ($order->data['advanced'] ?? 0);

                    $buyTotal = number_format($buySubtotal + $buyDelivery + $packaging);
                    $sellTotal = number_format($sellSubtotal + $sellDelivery - $advanced);

                    return '<span style="white-space:nowrap"><small class="text-muted">Buy</small> '.$buyTotal.'</span><br><span style="white-space:nowrap"><small class="text-muted">Sell</small> '.$sellTotal.'</span>';
                })
                ->addColumn('actions', function ($row) {
                    if (! $row->confirmed && $row->type === 'withdraw') {
                        return '<div class="btn-group">
                            <button type="button" class="btn btn-sm btn-primary confirm-withdraw" data-id="'.$row->id.'" data-amount="'.$row->amount.'">Confirm</button>
                            <button type="button" class="btn btn-sm btn-danger delete-withdraw" data-id="'.$row->id.'" data-amount="'.$row->amount.'">Delete</button>
                        </div>';
                    }

                    return '';
                })
                ->rawColumns(['type', 'meta', 'status', 'subtotal', 'delivery_charge', 'advanced', 'packaging_charge', 'total', 'actions'])
                ->make(true);
        }

        return view('admin.transactions.index', compact('user'));
    }

    /**
     * Handle the withdrawal request.
     *
     * @return Response
     */
    public function withdraw(Request $request, User $user)
    {
        $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'trx_id' => ['required', 'string', 'max:255'],
        ]);

        $availableBalance = $user->getAvailableBalance();

        if ($request->amount > $availableBalance) {
            $pendingAmount = $user->getPendingWithdrawalAmount();
            $message = 'Insufficient available balance. ';
            if ($pendingAmount > 0) {
                $message .= "User has {$pendingAmount} tk in pending withdrawals.";
            }

            return response()->json(['message' => $message], 422);
        }

        $user->wallet->withdraw($request->amount, [
            'trx_id' => $request->trx_id,
            'admin_id' => auth('admin')->id(),
        ]);

        return response()->json(['message' => 'Withdrawal successful']);
    }

    /**
     * Delete a pending withdrawal request.
     *
     * @return Response
     */
    public function deleteWithdraw(Request $request, User $user)
    {
        $request->validate([
            'transaction_id' => ['required', 'integer'],
        ]);

        $transaction = $user->wallet->transactions()
            ->where('type', 'withdraw')
            ->where('confirmed', false)
            ->where('id', $request->transaction_id)
            ->first();

        if (! $transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        // Delete the unconfirmed transaction
        $transaction->delete();

        // Clear pending withdrawal cache
        cacheMemo()->forget('pending_withdrawal_amount');

        return response()->json(['message' => 'Withdrawal request deleted successfully']);
    }

    /**
     * Confirm a pending withdrawal request.
     *
     * @return Response
     */
    public function confirmWithdraw(Request $request, User $user)
    {
        $request->validate([
            'trx_id' => ['required', 'string', 'max:255'],
            'transaction_id' => ['required', 'integer'],
        ]);

        $transaction = $user->wallet->transactions()
            ->where('type', 'withdraw')
            ->where('confirmed', false)
            ->where('id', $request->transaction_id)
            ->first();

        if (! $transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        // Update transaction meta with trx_id and admin_id
        $meta = $transaction->meta;
        $meta['trx_id'] = $request->trx_id;
        $meta['admin_id'] = auth('admin')->id();
        $transaction->meta = $meta;
        $transaction->save();

        // Confirm the transaction
        $user->confirm($transaction);

        // Clear pending withdrawal cache
        cacheMemo()->forget('pending_withdrawal_amount');

        return response()->json(['message' => 'Withdrawal confirmed successfully']);
    }
}
