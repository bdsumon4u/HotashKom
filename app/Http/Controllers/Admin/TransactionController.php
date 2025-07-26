<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class TransactionController extends Controller
{
    /**
     * Display a listing of the transactions.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, User $user)
    {
        if ($request->ajax()) {
            $transactions = $user->wallet->transactions();

            return DataTables::of($transactions)
                ->addIndexColumn()
                ->editColumn('type', function ($row) {
                    return $row->type === 'deposit' ?
                        '<span class="badge badge-success">Deposit</span>' :
                        '<span class="badge badge-danger">Withdraw</span>';
                })
                ->editColumn('amount', function ($row) {
                    return number_format($row->amount, 2);
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('d M Y, h:i A');
                })
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
                ->addColumn('actions', function ($row) {
                    if (! $row->confirmed && $row->type === 'withdraw') {
                        return '<div class="btn-group">
                            <button type="button" class="btn btn-sm btn-primary confirm-withdraw" data-id="'.$row->id.'" data-amount="'.$row->amount.'">Confirm</button>
                            <button type="button" class="btn btn-sm btn-danger delete-withdraw" data-id="'.$row->id.'" data-amount="'.$row->amount.'">Delete</button>
                        </div>';
                    }

                    return '';
                })
                ->rawColumns(['type', 'meta', 'status', 'actions'])
                ->make(true);
        }

        return view('admin.transactions.index', compact('user'));
    }

    /**
     * Handle the withdrawal request.
     *
     * @return \Illuminate\Http\Response
     */
    public function withdraw(Request $request, User $user)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'trx_id' => 'required|string|max:255',
        ]);

        if ($request->amount > $user->balance) {
            return response()->json(['message' => 'Insufficient balance'], 422);
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
     * @return \Illuminate\Http\Response
     */
    public function deleteWithdraw(Request $request, User $user)
    {
        $request->validate([
            'transaction_id' => 'required|integer',
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
        cache()->forget('pending_withdrawal_amount');

        return response()->json(['message' => 'Withdrawal request deleted successfully']);
    }

    /**
     * Confirm a pending withdrawal request.
     *
     * @return \Illuminate\Http\Response
     */
    public function confirmWithdraw(Request $request, User $user)
    {
        $request->validate([
            'trx_id' => 'required|string|max:255',
            'transaction_id' => 'required|integer',
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
        cache()->forget('pending_withdrawal_amount');

        return response()->json(['message' => 'Withdrawal confirmed successfully']);
    }
}
