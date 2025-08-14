<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Bavix\Wallet\Models\Transaction;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class MoneyRequestController extends Controller
{
    /**
     * Display a listing of money requests.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.money-requests.index');
    }

    /**
     * Get money requests data for DataTables.
     *
     * @return \Illuminate\Http\Response
     */
    public function data()
    {
        $transactions = Transaction::with('payable')
            ->where('type', 'withdraw')
            ->where('confirmed', false)
            ->orderBy('created_at', 'desc');

        return DataTables::of($transactions)
            ->addIndexColumn()
            ->editColumn('id', fn ($row): string => $row->id)
            ->editColumn('reseller', function ($row): string {
                $user = $row->payable;
                if (! $user) {
                    return 'N/A';
                }

                return '<div>
                    <div class="font-weight-bold">'.$user->name.'</div>
                    <small class="text-muted">'.($user->shop_name ?? 'N/A').'</small>
                </div>';
            })
            ->editColumn('phone', function ($row): string {
                $user = $row->payable;

                return $user ? ($user->phone_number ?? 'N/A') : 'N/A';
            })
            ->editColumn('bkash', function ($row): string {
                $user = $row->payable;

                return $user ? ($user->bkash_number ?? 'N/A') : 'N/A';
            })
            ->editColumn('amount', function ($row): string {
                return '<span class="font-weight-bold text-primary">'.theMoney(abs($row->amount)).'</span>';
            })
            ->editColumn('requested_at', function ($row): string {
                return $row->created_at->format('M d, Y H:i');
            })
            ->editColumn('status', function ($row): string {
                return '<span class="badge badge-warning">Pending</span>';
            })
            ->addColumn('actions', function ($row) {
                $user = $row->payable;
                if (! $user) {
                    return 'N/A';
                }

                return '<div class="btn-group">
                    <button type="button" class="btn btn-sm btn-primary confirm-withdraw"
                            data-id="'.$row->id.'"
                            data-user-id="'.$user->id.'"
                            data-amount="'.$row->amount.'">
                        <i class="fa fa-check"></i> Confirm
                    </button>
                    <button type="button" class="btn btn-sm btn-danger delete-withdraw"
                            data-id="'.$row->id.'"
                            data-user-id="'.$user->id.'"
                            data-amount="'.$row->amount.'">
                        <i class="fa fa-times"></i> Delete
                    </button>
                    <a href="'.route('admin.transactions.index', $user->id).'"
                       class="btn btn-sm btn-info" title="View All Transactions">
                        <i class="fa fa-eye"></i> View
                    </a>
                </div>';
            })
            ->rawColumns(['reseller', 'amount', 'status', 'actions'])
            ->make(true);
    }

    /**
     * Confirm a withdrawal request.
     *
     * @return \Illuminate\Http\Response
     */
    public function confirm(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|integer',
            'user_id' => 'required|integer',
            'trx_id' => 'required|string|max:255',
        ]);

        $transaction = Transaction::where('id', $request->transaction_id)
            ->where('type', 'withdraw')
            ->where('confirmed', false)
            ->first();

        if (! $transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        $user = User::find($request->user_id);
        if (! $user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Update transaction meta with trx_id and admin_id
        $meta = $transaction->meta ?? [];
        $meta['trx_id'] = $request->trx_id;
        $meta['admin_id'] = auth('admin')->id();
        $transaction->meta = $meta;
        $transaction->save();

        // Confirm the transaction
        $user->confirm($transaction);

        // Clear pending withdrawal cache
        cache()->forget('pending_withdrawal_amount');

        return response()->json(['message' => 'Withdrawal confirmed successfully']);
    }

    /**
     * Delete a withdrawal request.
     *
     * @return \Illuminate\Http\Response
     */
    public function deleteRequest(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|integer',
            'user_id' => 'required|integer',
        ]);

        $transaction = Transaction::where('id', $request->transaction_id)
            ->where('type', 'withdraw')
            ->where('confirmed', false)
            ->first();

        if (! $transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        // Delete the unconfirmed transaction
        $transaction->delete();

        // Clear pending withdrawal cache
        cache()->forget('pending_withdrawal_amount');

        return response()->json(['message' => 'Withdrawal request deleted successfully']);
    }

    /**
     * Get summary statistics.
     *
     * @return \Illuminate\Http\Response
     */
    public function summary()
    {
        $totalPending = Transaction::where('type', 'withdraw')
            ->where('confirmed', false)
            ->sum('amount');

        $totalRequests = Transaction::where('type', 'withdraw')
            ->where('confirmed', false)
            ->count();

        $todayRequests = Transaction::where('type', 'withdraw')
            ->where('confirmed', false)
            ->whereDate('created_at', today())
            ->count();

        return response()->json([
            'total_pending' => theMoney(abs($totalPending)),
            'total_requests' => $totalRequests,
            'today_requests' => $todayRequests,
        ]);
    }
}
