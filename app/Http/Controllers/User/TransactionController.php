<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class TransactionController extends Controller
{
    /**
     * Display a listing of the transactions.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $transactions = auth('user')->user()->wallet->transactions();

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
                ->addColumn('status', function ($row) {
                    if ($row->confirmed) {
                        return '<span class="badge badge-success">Confirmed</span>';
                    } else {
                        return '<span class="badge badge-warning">Pending</span>';
                    }
                })
                ->rawColumns(['type', 'meta', 'status'])
                ->make(true);
        }

        return view('user.transactions');
    }

    /**
     * Request a withdrawal.
     *
     * @return \Illuminate\Http\Response
     */
    public function withdrawRequest(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $user = auth('user')->user();

        if ($request->amount > $user->balance) {
            return response()->json(['message' => 'Insufficient balance'], 422);
        }

        // Create withdraw request with pending status
        $user->wallet->withdraw($request->amount, [
            'reason' => 'Withdraw Request',
            'status' => 'pending',
        ], false); // false for not confirmed

        return response()->json(['message' => 'Withdrawal request submitted successfully']);
    }
}
