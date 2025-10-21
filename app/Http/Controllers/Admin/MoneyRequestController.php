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
            // Ensure we're filtering user withdrawals (polymorphic)
            ->where('payable_type', (new User)->getMorphClass())->latest();

        return DataTables::of($transactions)
            // Override ALL searching (global + per-column) to avoid referencing non-existent DB columns
            ->filter(function ($query): void {
                $search = strtolower((string) request('search.value', ''));
                if ($search === '' || $search === null) {
                    return;
                }

                $query->where(function ($q) use ($search): void {
                    // Search by transaction id, amount, created_at
                    $q->whereRaw('LOWER(CAST(`transactions`.`id` AS CHAR)) LIKE ?', ["%{$search}%"]) // id
                        ->orWhereRaw('LOWER(CAST(`transactions`.`amount` AS CHAR)) LIKE ?', ["%{$search}%"]) // amount
                        ->orWhereRaw('LOWER(DATE_FORMAT(`transactions`.`created_at`, "%Y-%m-%d %H:%i:%s")) LIKE ?', ["%{$search}%"]) // requested_at
                        // Search related user (payable) fields
                        ->orWhereHas('payable', function ($uq) use ($search): void {
                            $uq->whereRaw('LOWER(`name`) LIKE ?', ["%{$search}%"]) // reseller name
                                ->orWhereRaw('LOWER(`shop_name`) LIKE ?', ["%{$search}%"]) // shop name
                                ->orWhereRaw('LOWER(`bkash_number`) LIKE ?', ["%{$search}%"]); // bkash
                        });
                });
            }, false)
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
            ->editColumn('bkash', function ($row): string {
                $user = $row->payable;

                return $user ? ($user->bkash_number ?? 'N/A') : 'N/A';
            })
            ->editColumn('amount', fn ($row): string => '<span class="font-weight-bold text-primary">'.theMoney(abs($row->amount)).'</span>')
            ->addColumn('balance', function ($row): string {
                $user = $row->payable;
                if (! $user) {
                    return 'N/A';
                }

                // Bavix\Wallet provides balance accessors; default to 0 if missing
                $balance = $user->balance ?? 0;

                return '<span class="font-weight-bold text-info">'.theMoney(abs($balance)).'</span>';
            })
            ->editColumn('requested_at', fn ($row): string => $row->created_at->format('M d, Y H:i'))
            ->editColumn('status', fn ($row): string => '<span class="badge badge-warning">Pending</span>')
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
            ->rawColumns(['reseller', 'amount', 'balance', 'status', 'actions'])
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
            'transaction_id' => ['required', 'integer'],
            'user_id' => ['required', 'integer'],
            'trx_id' => ['required', 'string', 'max:255'],
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
        cache()->memo()->forget('pending_withdrawal_amount');

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
            'transaction_id' => ['required', 'integer'],
            'user_id' => ['required', 'integer'],
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
        cache()->memo()->forget('pending_withdrawal_amount');

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
